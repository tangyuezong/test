<?php
use Workerman\Lib\Db;
use Workerman\Lib\DbRedis;

/**
 * Deposit.php
 * @copyright           airplus
 * @license             https://www.air-plus.cn
 * @lastmodify          2018-11-23
 * */
class Deposit
{
    /**
     * 押金
     * @param array $request
     * @return json
     */
    public static function pay($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        // 参数
        $access_token = $request['access_token'];
        $deviceId = intval($request['deviceId']);
        try {
            // token过期校验
            $uid = BikeUtil::access_token_index_to_uid_from_redis($redis, $db, $access_token);
            if($uid==0) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token校验失败");
            $tokenarr = BikeUtil::access_token_from_redis($redis, $db, $uid);
            if($tokenarr['expire_in']>0 && $tokenarr['expire_in']<(time()-$tokenarr['createtime'])) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token已过期");
            // 检查用户押金
            $userarr = BikeUtil::userinfo_from_redis($redis, $db, $uid);
            $devarr = BikeUtil::device_info_from_redis($redis, $db, $deviceId);
            // 获取价格信息，并判断设备是否可租
//             $pricearr = BikeUtil::price_info_from_redis($redis, $db, $devarr['price_id']);
            $netpointarr = BikeUtil::netpoint_info_from_redis($redis, $db, $devarr['netpoint_id']);
            if(empty($netpointarr)) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "押金未设置");
            // 获取价格信息，并判断设备是否可租
            $pricearr = BikeUtil::price_info_from_redis($redis, $db, $devarr['price_id']);
            // 租用时段判断
            $time = time();
            $year = date("Y", $time);
            $month = date("m", $time);
            $day = date("d", $time);
            $orderfrozentime = mktime(3, 0, 0, $month, $day, $year);
            $curnightendtime = mktime(intval(substr($pricearr['nightend'], 0, 2)), intval(substr($pricearr['nightend'], 3)), 0, $month, $day, $year);
            $lightstarttime = mktime(intval(substr($pricearr['lightstart'], 0, 2)), intval(substr($pricearr['lightstart'], 3)), 0, $month, $day, $year);
            $lightendtime = mktime(intval(substr($pricearr['lightend'], 0, 2)), intval(substr($pricearr['lightend'], 3)), 0, $month, $day, $year);
            $nightstarttime = mktime(intval(substr($pricearr['nightstart'], 0, 2)), intval(substr($pricearr['nightstart'], 3)), 0, $month, $day, $year);
            $nightendtime = mktime(intval(substr($pricearr['nightend'], 0, 2)), intval(substr($pricearr['nightend'], 3)), 0, $month, $day+1, $year);
            // 日间可用开始、结束相同
            if($lightstarttime==$lightendtime) {
                if($nightstarttime==$nightendtime) {
                    return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "设备无可用时段");
                } else {
                    if($time < $curnightendtime) {
                        if($time > $orderfrozentime) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "可用开始时间：".$pricearr['nightstart']);
                    } else {
                        if(($nightstarttime-$time) > 600) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "可用开始时间：".$pricearr['nightstart']);
                    }
                }
            }
            // 夜间可用开始、结束相同
            else if($nightstarttime==$nightendtime) {
                if(($lightstarttime-$time) > 600) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "可用开始时间：".$pricearr['lightstart']);
            }
            // 夜间夜晚时间租用
            else if($time >= $nightstarttime && $time <= $nightendtime) {
                // do nothing
            }
            // 夜间凌晨时间租用
            else if($time < $curnightendtime) {
                // do nothing
            }
            // 日间租用
            else if($time >= $lightstarttime && $time <= $lightendtime) {
                // do nothing
            }
            // 靠近夜间开始时间
            else if($time >= $lightendtime && $time <= $nightstarttime) {
                if(($nightstarttime-$time) > 600) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "可用开始时间：".$pricearr['nightstart']);
            } 
            // 靠近日间开始时间
            else {
                if(($lightstarttime-$time) > 600) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "可用开始时间：".$pricearr['lightstart']);
            }
            $request['item'] = BikeConstant::PAY_ITEM_DEPOSIT;
            $request['payment'] = BikeConstant::PAY_CHANNEL_WXJSAPI;
            $request['openid'] = $userarr['openid'];
            $request['domain'] = $request['domain'];
            switch ($request['payment']) {
                case BikeConstant::PAY_CHANNEL_WEIXIN:		// 微信app支付
                case BikeConstant::PAY_CHANNEL_WXJSAPI:		// 微信jsapi支付
                    $array = PayUtil::get_trans_serial_number_wx_apppay($request, ['users_id'=>$uid, 'deposit'=>bcsub($netpointarr['deposit'], $userarr['deposit'], 2)]);
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
    
    public static function querydeposit($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        try {
            // token过期校验
            $access_token = $request['access_token'];
            $uid = BikeUtil::access_token_index_to_uid_from_redis($redis, $db, $access_token);
            if($uid==0) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token校验失败");
            $tokenarr = BikeUtil::access_token_from_redis($redis, $db, $uid);
            if($tokenarr['expire_in']>0 && $tokenarr['expire_in']<(time()-$tokenarr['createtime'])) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token已过期");
            // 获取用户的deposit
            $userarr = BikeUtil::userinfo_from_redis($redis, $db, $uid);
            if(empty($userarr)) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "用户不存在");
            $cols = array('payment, uid, tran_id, amount, tran_time');
            $cond['uid'] = $uid;
            $tranlist = $db->select($cols)->from(BikeUtil::table_full_name(BikeConstant::TABLE_TRANS))->where('uid=:uid and item=3 and status=1')->bindValues($cond)->query();
            if(empty($tranlist)) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "无押金记录，若已支付押金且未退押金，请及时联系客服！");
            if(bccomp($userarr['deposit'], array_sum(array_column($tranlist, 'amount')))!=0) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "押金金额不匹配，请及时联系客服！");
            return BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "查询成功");
        } catch(Exception $e) {
            var_dump($e->getMessage());
            return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, BikeConstant::REFUND_ENCOUNTER_EXCEPTION_MSG);
        }
    }
    
    public static function refund($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        try {
            if(key_exists('backendind', $request) && $request['backendind']==1) {
                $uid = $request['uid'];
            } else {
                // token过期校验
                $access_token = $request['access_token'];
                $uid = BikeUtil::access_token_index_to_uid_from_redis($redis, $db, $access_token);
                if($uid==0) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token校验失败");
                $tokenarr = BikeUtil::access_token_from_redis($redis, $db, $uid);
                if($tokenarr['expire_in']>0 && $tokenarr['expire_in']<(time()-$tokenarr['createtime'])) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token已过期");
            }
            // 检查用户是否有储物柜订单
            $cabinetorderarr = BikeUtil::cabinetorder_from_redis($redis, $db, $uid);
            if(!empty($cabinetorderarr)) return BikeUtil::format_return_array(BikeConstant::Interface_Has_Cabinet_Order_Code, "储物柜订单未结束", $cabinetorderarr);
            // 检查用户是否有陪护床订单
            $bedorderarr = BikeUtil::bedorder_from_redis($redis, $db, $uid);
            if(!empty($bedorderarr)) return BikeUtil::format_return_array(BikeConstant::Interface_Has_Bed_Order_Code, "陪护床订单未结束", $bedorderarr);
            // 获取用户的deposit
            $userarr = BikeUtil::userinfo_from_redis($redis, $db, $uid);
            if(empty($userarr)) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "用户不存在");
            $cols = array('payment, uid, tran_id, amount, tran_time');
            $cond['uid'] = $uid;
            $tranlist = $db->select($cols)->orderByDESC(["id"])->from(BikeUtil::table_full_name(BikeConstant::TABLE_TRANS))->where('uid=:uid and item=3 and status=1')->bindValues($cond)->limit(1)->query();
            if(empty($tranlist) || !key_exists('amount', $tranlist[0]) || (double)$tranlist[0]['amount']<0) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, BikeConstant::REFUND_TRANS_EMPTY_MSG);
            $payment = intval($tranlist[0]['payment']);
            switch($payment) {
                case BikeConstant::PAY_CHANNEL_WEIXIN:
                case BikeConstant::PAY_CHANNEL_WXJSAPI:
                    return self::wxpay_tran_refund($redis, $db, $tranlist[0], $userarr, $payment);
                    break;
                default:
                    return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, BikeConstant::REFUND_CHANNEL_ERROR_MSG);
                    break;
            }
        } catch(Exception $e) {
            var_dump($e->getMessage());
            return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, BikeConstant::REFUND_ENCOUNTER_EXCEPTION_MSG);
        }
    }
    
    /**
     * weixin refund
     *
     * @param object $redis
     * @param object $db
     * @param array $tranarr
     * @return array
     */
    private static function wxpay_tran_refund($redis, $db, $tranarr, $userarr, $payment)
    {
        $userdeposit = (double)$userarr['deposit'];
        $input = new WxPayRefund();
        $input->SetTransaction_id($tranarr['tran_id']);
        $input->SetOut_refund_no($tranarr['tran_id']);
        $input->SetTotal_fee((double)$tranarr['amount'] * 100);
        $input->SetRefund_fee((double)$tranarr['amount'] * 100);
        if($payment==BikeConstant::PAY_CHANNEL_WXJSAPI) {
            $input->SetOp_user_id(WxPayConfig::MCHID_JSAPI);
            $input->SetAppid(WxPayConfig::APPID_JSAPI);
            $input->SetMch_id(WxPayConfig::MCHID_JSAPI);
        } else {
            $input->SetOp_user_id(WxPayConfig::MCHID);
        }
        // 用户押金更新
        if(bccomp($userdeposit, (double)$tranarr['amount'], 2)==1) {
            $userarr['deposit'] = $usersave['deposit'] = bcsub($userdeposit, (double)$tranarr['amount'], 2);
        } else {
            $userarr['deposit'] = $usersave['deposit'] = 0;
        }
        try {
            $resp = WxPayApi::refund($input);
            if($resp['return_code']=="SUCCESS" && $resp['result_code']=="SUCCESS") {
                $db->beginTrans();
                // trans表
                $transave['status'] = 0;
                $transave['refund_id'] = $resp['refund_id'];
                $transave['refund_fee'] = intval($resp['refund_fee']) / 100;
                $transave['refund_time'] = time();
                $trancond['tran_id'] = $tranarr['tran_id'];
                // 用户表
                $usercond['id'] = $trancond['uid'] = intval($tranarr['uid']);
//                 $userwhere = "id=:id and deposit=$userdeposit";
                $userwhere = "id=:id";
                // db操作
                if(bccomp($userdeposit, $usersave['deposit'], 2)==0) {
                    $usertemp = true;
                } else {
                    $usertemp = $db->update(BikeUtil::table_full_name(BikeConstant::TABLE_USERS))->cols($usersave)->where($userwhere)->bindValues($usercond)->query();
                }
                $trantemp = $db->update(BikeUtil::table_full_name(BikeConstant::TABLE_TRANS))->cols($transave)->where("uid=:uid and tran_id=:tran_id and item=3")->bindValues($trancond)->query();
                // 结果判断
                if($trantemp==null || $usertemp==null) {
                    $db->rollBackTrans();
                    // todo 通知运营人员退款保存失败
                    var_dump("押金退款成功，数据更新db，log start .............");
                    var_dump($tranarr);
                    var_dump($userarr);
                    var_dump($userdeposit);
                    var_dump($transave);
                    var_dump($usersave);
                    var_dump("押金退款成功，数据更新db失败，log end .............");
                } else {
                    $db->commitTrans();
                    $redis -> hSet(BikeConstant::AIRPLUS_HASH_USERINFO, $tranarr['uid'], json_encode($userarr));
                }
                return BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, BikeConstant::REFUND_INVOKE_SUCCESS_MSG, $usersave);
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
    
}
