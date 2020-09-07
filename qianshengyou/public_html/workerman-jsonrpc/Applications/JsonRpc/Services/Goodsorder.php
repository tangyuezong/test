<?php
use Workerman\Lib\Db;
use Workerman\Lib\DbRedis;

/**
 * Goodsorder.php
 * @copyright           airplus
 * @license             https://www.air-plus.cn
 * @lastmodify          2018s-7-27
 * */
class Goodsorder
{
    /**
     * 商品支付
     * @param array $request
     * @return json
     */
    public static function pay($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        // 参数
        $access_token = $request['access_token'];
        $goodsId = intval($request['goodsId']);
        $num = intval($request['num']);
        try {
            // token过期校验
            $uid = BikeUtil::access_token_index_to_uid_from_redis($redis, $db, $access_token);
            if($uid==0) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token校验失败");
            $tokenarr = BikeUtil::access_token_from_redis($redis, $db, $uid);
            if($tokenarr['expire_in']>0 && $tokenarr['expire_in']<(time()-$tokenarr['createtime'])) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token已过期");
            // 生成订单
            $orderinfo = new GoodsorderInfo();
            // 获取商品信息
            $goodsarr = BikeUtil::goods_info_from_redis($redis, $db, $goodsId);
            if(empty($goodsarr) || empty($goodsarr['price'])) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "商品未上架");
            $orderinfo->order_no = "g_".BikeUtil::generate_order_no(2);
            $orderinfo->users_id = $uid;
            $orderinfo->goods_id = $goodsId;
            $orderinfo->netpoint_id = intval($request['netpoint_id']);
            $orderinfo->department = $request['department'];
            $orderinfo->room = $request['room'];
            $orderinfo->price = $goodsarr['price'];
            $orderinfo->num = $num;
            $orderinfo->total = $orderinfo->price * $num;
            $orderarr = get_object_vars($orderinfo);
            // 将订单缓存两个小时 + 1分钟
            $redis -> set($orderinfo->order_no, json_encode($orderarr), 7260);
            // 获取用户信息
            $userarr = BikeUtil::userinfo_from_redis($redis, $db, $uid);
            $request['item'] = BikeConstant::PAY_ITEM_GOODS;
            $request['openid'] = $userarr['openid'];
            $request['domain'] = $request['domain'];
            $request['payment'] = BikeConstant::PAY_CHANNEL_WXJSAPI;
            switch ($request['payment']) {
                case BikeConstant::PAY_CHANNEL_WEIXIN:		//微信app支付
                case BikeConstant::PAY_CHANNEL_WXJSAPI:		//微信jsapi支付
                    $array = PayUtil::get_trans_serial_number_wx_apppay($request, $orderarr);
                    break;
                default:
                    $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "支付通道不支持");
                    break;
            }
        } catch(Exception $e) {
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $e->getMessage());
        }
        // 返回结果
        return $array;
    }
    
    /**
     * 商品退款
     * @param unknown $request
     */
    public static function refund($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        // 参数
        $order_no = $request['order_no'];
        try {
            if(key_exists('backendind', $request) && $request['backendind']==1) {
                $uid = $request['users_id'];
            } else {
                $access_token = $request['access_token'];
                // token过期校验
                $uid = BikeUtil::access_token_index_to_uid_from_redis($redis, $db, $access_token);
                if($uid==0) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token校验失败");
                $request['uid'] = $uid;
                $tokenarr = BikeUtil::access_token_from_redis($redis, $db, $uid);
                if($tokenarr['expire_in']>0 && $tokenarr['expire_in']<(time()-$tokenarr['createtime'])) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token已过期");
            }
            // 获取退款订单对应的第三方trans
            $cond['uid'] = $uid;
            $cond['order_no'] = $order_no;
            $tranlist = $db->select(array('payment, uid, tran_id, amount, refund_fee, tran_time'))->from(BikeUtil::table_full_name(BikeConstant::TABLE_TRANS))->where('uid=:uid and item=5 and order_no=:order_no')->bindValues($cond)->limit(1)->query();
            if(empty($tranlist)) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, BikeConstant::REFUND_TRANS_EMPTY_MSG);
            // 根据支付渠道，调用退款方法
            switch(intval($tranlist[0]['payment'])) {
                case BikeConstant::PAY_CHANNEL_WEIXIN:
                case BikeConstant::PAY_CHANNEL_WXJSAPI:
                    $array = self::wxpay_tran_refund($db, $request + $tranlist[0]);
                    break;
                default:
                    $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, BikeConstant::REFUND_CHANNEL_ERROR_MSG);
                    break;
            }
        } catch(Exception $e) {
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $e->getMessage());
        }
        // var_dump($array);
        // 返回结果
        return $array;
    }
    
    private static function wxpay_tran_refund($db, $request)
    {
        $time = time();
        $input = new WxPayRefund();
        $input->SetTransaction_id($request['tran_id']);
        $input->SetOut_refund_no($request['order_no']."_".$time);
        $input->SetTotal_fee($request['total'] * 100);
        $input->SetRefund_fee($request['refundamount'] * 100);
        // 设置退款appid、商户、xxx等信息
        if($request['payment']==BikeConstant::PAY_CHANNEL_WEIXIN) {
            $input->SetOp_user_id(WxPayConfig::MCHID);
        } else {
            $input->SetOp_user_id(WxPayConfig::MCHID_JSAPI);
            $input->SetAppid(WxPayConfig::APPID_JSAPI);
            $input->SetMch_id(WxPayConfig::MCHID_JSAPI);
        }
        // 退款流程
        try {
            $resp = WxPayApi::refund($input);
            // 微信退款结果判断
            if($resp['return_code']=="SUCCESS" && $resp['result_code']=="SUCCESS") {
                // 启动transaction
                $db->beginTrans();
                // carorder表数据更新
                $ordersave['refund_time'] = $time;
                $ordersave['refundnote'] = "退款成功";
                // carorder表条件
                $orderwherestr = sprintf("users_id=%d and status=99999 and order_no='%s'", intval($request['users_id']), $request['order_no']);
                // trans表数据更新
                $transave['refund_id'] = $resp['refund_id'];
                $transave['refund_fee'] = $request['refunded'];
                $transave['refund_time'] = $time;
                // trans表条件
                $trancond['uid'] = $request['users_id'];
                $trancond['tran_id'] = $request['tran_id'];
                // 表操作执行
                $ordertemp = $db->update(BikeUtil::table_full_name(BikeConstant::TABLE_GOODSORDER))->cols($ordersave)->where($orderwherestr)->query();
                $trantemp = $db->update(BikeUtil::table_full_name(BikeConstant::TABLE_TRANS))->cols($transave)->where("uid=:uid and item=5 and tran_id=:tran_id")->bindValues($trancond)->query();
                // 执行结果判断
                $extramsg = BikeConstant::REFUND_INVOKE_SUCCESS_MSG;
                if($trantemp==null || $ordertemp==null) {
                    $db->rollBackTrans();
                    $extramsg .= "，但db数据保存失败，请立即联系技术支持，数据为：" . json_encode($transave) . " - " . json_encode($trancond) . " - " . json_encode($ordersave) . " - " . $orderwherestr;
                } else {
                    $db->commitTrans();
                }
                // 返回结果
                return BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, $extramsg);
            } else {
                if(!empty($resp['err_code_des'])) {
                    $msg = $resp['err_code_des'];
                } else {
                    $msg = $resp['return_msg'];
                }
                return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $msg);
            }
        } catch(Exception $e) {
            $msg = BikeConstant::WXREFUND_ENCOUNTER_EXCEPTION_MSG;
            if(!empty($e->getMessage())) $msg .=  ", " . $e->getMessage();
            return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $msg);
        }
    }
    
    public static function orderlist($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        // 参数
        $access_token = $request['access_token'];
        empty($request['page']) ? $page = 1 : $page = $request['page'];
        try {
            // token过期校验
            $uid = BikeUtil::access_token_index_to_uid_from_redis($redis, $db, $access_token);
            if($uid==0) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token校验失败");
            $tokenarr = BikeUtil::access_token_from_redis($redis, $db, $uid);
            if($tokenarr['expire_in']>0 && $tokenarr['expire_in']<(time()-$tokenarr['createtime'])) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token已过期");
            // 列表获取分页数据
            $tablegoodsorder = BikeUtil::table_full_name(BikeConstant::TABLE_GOODSORDER);
            $tablegoods = BikeUtil::table_full_name(BikeConstant::TABLE_GOODS);
            $statement = sprintf("select count(*) as count from %s where users_id=%d and status in (1,9)", $tablegoodsorder, $uid);
            $countarr = $db->query($statement);
            $data['total'] = intval($countarr[0]['count']);
            // 查询数据
            $cond['users_id'] = $uid;
            $data['list'] = $db->orderByASC(array("$tablegoodsorder.status"))->orderByASC(array("$tablegoodsorder.createtime"))->select(array("order_no","$tablegoodsorder.price","num","total","$tablegoodsorder.status","name"))->
                from($tablegoodsorder)->innerJoin($tablegoods,"$tablegoodsorder.goods_id = $tablegoods.id")->
                where("users_id=:users_id and $tablegoodsorder.status in (1,9)")->bindValues($cond)->page($page)->query();
            $data['pagesize'] = $db->getPaging();
            $remaining = $data['total'] - $page*$data['pagesize'];
            if($remaining<0) $remaining=0;
            $data['remaining'] = $remaining;
            // 返回结果
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "获取列表成功", $data);
        } catch(Exception $e) {
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $e->getMessage());
        }
        // 返回结果
        return $array;
    }
    
}
