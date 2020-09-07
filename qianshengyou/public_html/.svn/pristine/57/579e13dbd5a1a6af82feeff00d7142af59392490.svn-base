<?php
use Workerman\Lib\Db;
use Workerman\Lib\DbRedis;

/**
 * Cabinetorder.php
 * @copyright           airplus
 * @license             https://www.air-plus.cn
 * @lastmodify          2018s-7-27
 * */
class Cabinetorder
{
    /**
     * 储物柜订单
     */
    public static function submit($request)
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
            // 获取押金信息
            $devarr = BikeUtil::device_info_from_redis($redis, $db, $deviceId);
            $netpointarr = BikeUtil::netpoint_info_from_redis($redis, $db, $devarr['netpoint_id']);
            if(empty($netpointarr) || empty($netpointarr['deposit'])) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "网点设备押金未设置");
            // 判断用户押金
            $userarr = BikeUtil::userinfo_from_redis($redis, $db, $uid);
            if(bccomp($userarr['deposit'], 0, 2)==-1) {
                return BikeUtil::format_return_array(BikeConstant::Interface_Need_Deposit_Code, "未支付押金");
            } else if(bccomp($userarr['deposit'], $netpointarr['deposit'], 2)==-1) {
                return BikeUtil::format_return_array(BikeConstant::Interface_Need_Deposit_Code, "押金不足");
            }
            // 检查用户是否已经存在储物柜订单
            $orderarr = BikeUtil::cabinetorder_from_redis($redis, $db, $uid);
            if(!empty($orderarr)) {
                $orderarr['deviceId'] = $orderarr['dev_id'];
                return BikeUtil::format_return_array(BikeConstant::Interface_Order_Has_Chg_Code, "已存在订单", $orderarr);
            }
            // 检查设备是否已被租用
            $rent_uid = $redis->hGet(BikeConstant::AIRPLUS_HASH_RENTED_DEVICE, $deviceId);
            if($rent_uid!=false) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "设备租用中");
            // 判断设备类型是否一致
            if($devarr['iscabinet']!==1) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "非储物柜设备");
            // 生成订单
            $orderinfo = new CabinetorderInfo();
            $orderinfo->order_no = BikeUtil::generate_order_no(4);
            $orderinfo->users_id = $uid;
            $userarr = BikeUtil::userinfo_from_redis($redis, $db, $uid);
            $orderinfo->phone = $userarr['phone'];
            $orderinfo->dev_id = $deviceId;
            $orderinfo->netpoint_id = $devarr['netpoint_id'];
            $netpointarr = BikeUtil::netpoint_info_from_redis($redis, $db, $devarr['netpoint_id']);
            $orderinfo->admin_id = $netpointarr['admin_id'];
            $orderinfo->entity_id = $devarr['entity_id'];
            $orderarr = get_object_vars($orderinfo);
            $temp = $db->insert(BikeUtil::table_full_name(BikeConstant::TABLE_CABINETORDER))->cols($orderarr)->query();
            if($temp==null) {
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "订单提交失败");
            } else {
                $redis->hSet(BikeConstant::AIRPLUS_HASH_CABINETORDER, $orderarr['order_no'], json_encode($orderarr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                $redis->hSet(BikeConstant::AIRPLUS_HASH_USER_CURRENT_CABINETORDER, $uid, $orderarr['order_no']);
                $redis->hSet(BikeConstant::AIRPLUS_HASH_RENTED_DEVICE, $orderarr['dev_id'], $orderarr['users_id']);
                $orderarr['deviceId'] = $orderarr['dev_id'];
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "订单提交成功", $orderarr);
            }
        } catch(Exception $e) {
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $e->getMessage());
        }
        // 返回结果
        return $array;
    }
    
    public static function queryorder($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        // 参数
        $access_token = $request['access_token'];
        try {
            // token过期校验
            $uid = BikeUtil::access_token_index_to_uid_from_redis($redis, $db, $access_token);
            if($uid==0) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token校验失败");
            $tokenarr = BikeUtil::access_token_from_redis($redis, $db, $uid);
            if($tokenarr['expire_in']>0 && $tokenarr['expire_in']<(time()-$tokenarr['createtime'])) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token已过期");
            // 检查用户是否已经存在订单
            $orderarr = BikeUtil::cabinetorder_from_redis($redis, $db, $uid);
            if(empty($orderarr)) return BikeUtil::format_return_array(BikeConstant::Interface_Order_Has_Chg_Code, "订单已结束");
            // 返回timestamp及签名
            $data['timestamp'] = time();
            $data['sign'] = md5(md5($data['timestamp']).$orderarr['order_no'].$access_token);
            $devarr = BikeUtil::device_info_from_redis($redis, $db, $orderarr['dev_id']);
            $data['deviceId'] = $orderarr['dev_id'];
            $data['mac'] = $devarr['mac'];
            $data['blekey'] = $devarr['blekey'];
            $data['blepwd'] = $devarr['blepwd'];
            if($devarr['devtype_id']==0) {
                $data['type'] = 1;
            } else {
                $devtypearr = BikeUtil::dev_type_from_redis($redis, $db, $devarr['devtype_id']);
                $data['type'] = intval($devtypearr['type']);
            }
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "查询成功", $data);
        } catch(Exception $e) {
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $e->getMessage());
        }
        // 返回结果
        return $array;
    }
    
    public static function assignauth($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        // 参数
        $access_token = $request['access_token'];
        try {
            // token过期校验
            $uid = BikeUtil::access_token_index_to_uid_from_redis($redis, $db, $access_token);
            if($uid==0) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token校验失败");
            $tokenarr = BikeUtil::access_token_from_redis($redis, $db, $uid);
            if($tokenarr['expire_in']>0 && $tokenarr['expire_in']<(time()-$tokenarr['createtime'])) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token已过期");
            // 检查用户是否已经存在订单
            $orderarr = BikeUtil::bedorder_from_redis($redis, $db, $uid);
            if(empty($orderarr)) return BikeUtil::format_return_array(BikeConstant::Interface_Order_Has_Chg_Code, "订单已结束");
            // 返回timestamp及签名
            $data['timestamp'] = time();
            $devarr = BikeUtil::device_info_from_redis($redis, $db, $orderarr['dev_id']);
            $redis->set("auth_".$devarr['mac'], 1, 30);
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "授权成功", $data);
        } catch(Exception $e) {
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $e->getMessage());
        }
        // 返回结果
        return $array;
    }
    
    public static function queryauth($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        // 参数
        $mac = $request['mac'];
        try {
            // token过期校验
            $authstat = $redis->get("auth_".$mac);
            if($authstat==false) {
                return 1;
            } else {
                return 0;
            }
        } catch(Exception $e) {
            return 99;
        }
    }
    
    public static function rtncabinet($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        // 参数
        $order_no = $request['order_no'];
        try {
            if(key_exists("backendind", $request) && $request['backendind']==1) {
                $uid = $request['users_id'];
                $save['rtn_admin_id'] = $request['rtn_admin_id'];
            } else {
                $access_token = $request['access_token'];
                // token过期校验
                $uid = BikeUtil::access_token_index_to_uid_from_redis($redis, $db, $access_token);
                if($uid==0) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token校验失败");
                $tokenarr = BikeUtil::access_token_from_redis($redis, $db, $uid);
                if($tokenarr['expire_in']>0 && $tokenarr['expire_in']<(time()-$tokenarr['createtime'])) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token已过期");
                // 检测订单状态是否正常
            }
            $orderarr = BikeUtil::cabinetorder_from_redis($redis, $db, $uid);
            if(empty($orderarr)) {
                return BikeUtil::format_return_array(BikeConstant::Interface_Order_Has_Chg_Code, "订单已结束");
            } else if(strcmp($order_no, $orderarr['order_no'])!=0) {
                return BikeUtil::format_return_array(BikeConstant::Interface_Order_Has_Chg_Code, "订单不匹配");
            }
            // 按计费类型处理订单费用
            $orderarr['endtime'] = $save['endtime'] = time();
            // 结束订单
            $save['step'] = 9000;
            $wherestr = sprintf("users_id=%d and step=%d and status=1 and order_no='%s'", intval($uid), intval($orderarr['step']), $order_no);
            $temp = $db->update(BikeUtil::table_full_name(BikeConstant::TABLE_CABINETORDER))->cols($save)->where($wherestr)->query();
            if($temp==null) {
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "储物柜归还失败");
            } else {
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "储物柜归还成功");
                // 更新订单缓存
                $redis->hDel(BikeConstant::AIRPLUS_HASH_CABINETORDER, $order_no);
                $redis->hDel(BikeConstant::AIRPLUS_HASH_USER_CURRENT_CABINETORDER, $uid);
                $redis->hDel(BikeConstant::AIRPLUS_HASH_RENTED_DEVICE, $orderarr['dev_id']);
            }
        } catch(Exception $e) {
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $e->getMessage());
        }
        // 返回结果
        return $array;
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
            $tablecabinetorder = BikeUtil::table_full_name(BikeConstant::TABLE_CABINETORDER);
            $tablenetpoint = BikeUtil::table_full_name(BikeConstant::TABLE_NETPOINT);
            $statement = sprintf("select count(*) as count from %s where users_id=%d and status=1", $tablecabinetorder, $uid);
            $countarr = $db->query($statement);
            $data['total'] = intval($countarr[0]['count']);
            // 查询数据
            $cond['users_id'] = $uid;
            $data['list'] = $db->orderByASC(array('step'))->select(array("order_no","dev_id","step","shortname"))->
                from($tablecabinetorder)->innerJoin($tablenetpoint,"$tablecabinetorder.netpoint_id = $tablenetpoint.id")->
                where("users_id=:users_id and $tablecabinetorder.status=1")->bindValues($cond)->page($page)->query();
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
