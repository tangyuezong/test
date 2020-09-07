<?php
use Workerman\Lib\Db;
use Workerman\Lib\DbRedis;

/**
 * Carorder.php
 * @copyright           airplus
 * @license             https://www.air-plus.cn
 * @lastmodify          2018s-7-27
 * */
class Device
{
//     public static function devIntoRepo($request)
//     {
//         $db = Db::instance('cobike_mysql');
//         $redis = DbRedis::instance('cobike_redis');
//         // 参数
//         $devId = intval($request['deviceId']);
//         $mac = $request['mac'];
//         // 判断设备ID，mac是否已经入过库
//         $macache = BikeUtil::mac_index_to_devid_from_redis($redis, $db, $mac);
//         if($macache>0) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "设备mac地址已绑定设备ID：".$macache);
//         $devcache = BikeUtil::device_info_from_redis($redis, $db, $devId);
//         if(!empty($devcache)) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "设备ID已经绑定设备mac：".$devcache['mac']);
//         if(key_exists('mac2', $request)) {
//             $cond['mac'] = $request['mac2'];
//             $cond['mac2'] = $request['mac2'];
//             $list = $db->table(BikeUtil::table_full_name(BikeConstant::TABLE_DEVICE))->where("mac=:mac or mac2=:mac2")->bindValues($cond)->limit(1)->select();
//             if(!empty($list)) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "抽屉mac地址已绑定设备ID：".$list[0]['dev_id']);
//         }
//         // 入库
//         try {
//             $devinfo = new DeviceInfo();
//             $devinfo->dev_id = $devId;
//             $devinfo->mac = $mac;
//             $devinfo->blekey = $request['blekey'];
//             $devinfo->blepwd = $request['blepwd'];
//             if(key_exists('mac2', $request)) {
//                 $devinfo->mac2 = $request['mac2'];
//                 $devinfo->blekey2 = $request['blekey2'];
//                 $devinfo->blepwd2 = $request['blepwd2'];
//             }
//             $devarr = $add = get_object_vars($devinfo);
//             $add['createtime'] = $add['updatetime'] = time();
//             $temp = $db->insert(BikeUtil::table_full_name(BikeConstant::TABLE_DEVICE))->cols($add)->query();
//             if($temp==null) {
//                 $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "入库写db失败");
//             } else {
//                 $devarr['id'] = $db->lastInsertId();
//                 // 更新缓存
//                 $redis->hSet(BikeConstant::AIRPLUS_HASH_MAC_INDEXTO_DEVID, $mac, $devId);
//                 $redis->hSet(BikeConstant::AIRPLUS_HASH_DEVICE_INFO, $devId, json_encode($devarr));
//                 $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "入库成功");
//             }
//         } catch(Exception $e) {
//             $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $e->getMessage());
//             return $array;
//         }
//         // 返回结果
//         return $array;
//     }
    
    public static function bedIntoRepo($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        // 参数
        $devId = intval($request['deviceId']);
        $mac = $request['mac'];
        // 判断设备ID，mac是否已经入过库
        $macache = BikeUtil::mac_index_to_devid_from_redis($redis, $db, $mac);
        if($macache>0) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "设备mac地址已绑定设备ID：".$macache);
        $devcache = BikeUtil::device_info_from_redis($redis, $db, $devId);
        if(!empty($devcache)) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "设备ID已经绑定设备mac：".$devcache['mac']);
        // 入库
        try {
            $devinfo = new DeviceInfo();
            $devinfo->dev_id = $devId;
            $devinfo->iscabinet = 2;
            $devinfo->mac = $mac;
            $devinfo->blekey = $request['blekey'];
            $devinfo->blepwd = $request['blepwd'];
            $devarr = $add = get_object_vars($devinfo);
            $add['createtime'] = $add['updatetime'] = time();
            $add['admin_id'] = 2;
            $temp = $db->insert(BikeUtil::table_full_name(BikeConstant::TABLE_DEVICE))->cols($add)->query();
            if($temp==null) {
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "陪护床入库写db失败");
            } else {
                $devarr['id'] = $db->lastInsertId();
                // 更新缓存
                $redis->hSet(BikeConstant::AIRPLUS_HASH_MAC_INDEXTO_DEVID, $mac, $devId);
                $redis->hSet(BikeConstant::AIRPLUS_HASH_DEVICE_INFO, $devId, json_encode($devarr));
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "陪护床入库成功");
            }
        } catch(Exception $e) {
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $e->getMessage());
        }
        // 返回结果
        return $array;
    }
    
    public static function cabinetIntoRepo($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        // 参数
        $devId = intval($request['deviceId']);
        $mac = $request['mac'];
        // 判断设备ID，mac是否已经入过库
        $macache = BikeUtil::mac_index_to_devid_from_redis($redis, $db, $mac);
        if($macache>0) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "设备mac地址已绑定设备ID：".$macache);
        $devcache = BikeUtil::device_info_from_redis($redis, $db, $devId);
        if(!empty($devcache)) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "设备ID已经绑定设备mac：".$devcache['mac']);
        // 入库
        try {
            $devinfo = new DeviceInfo();
            $devinfo->dev_id = $devId;
            $devinfo->iscabinet = 1;
            $devinfo->mac = $mac;
            $devinfo->blekey = $request['blekey'];
            $devinfo->blepwd = $request['blepwd'];
            $devarr = $add = get_object_vars($devinfo);
            $add['createtime'] = $add['updatetime'] = time();
            $add['admin_id'] = 2;
            $temp = $db->insert(BikeUtil::table_full_name(BikeConstant::TABLE_DEVICE))->cols($add)->query();
            if($temp==null) {
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "储物柜入库写db失败");
            } else {
                $devarr['id'] = $db->lastInsertId();
                // 更新缓存
                $redis->hSet(BikeConstant::AIRPLUS_HASH_MAC_INDEXTO_DEVID, $mac, $devId);
                $redis->hSet(BikeConstant::AIRPLUS_HASH_DEVICE_INFO, $devId, json_encode($devarr));
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "储物柜入库成功");
            }
        } catch(Exception $e) {
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $e->getMessage());
        }
        // 返回结果
        return $array;
    }
    
    public static function devdetail($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        // 参数
        $iscabinet = intval($request['iscabinet']);
        $devId = intval($request['deviceId']);
        $access_token = $request['access_token'];
        if(key_exists('phone', $request)) {
            // phone获取uid
            $phoneuid = BikeUtil::phone_index_to_userid($redis, $db, $request['phone']);
            // token获取uid
            $uid = BikeUtil::access_token_index_to_uid_from_redis_daikai($redis, $db, $access_token);
            if($phoneuid!=$uid || $phoneuid===0 || $uid===0) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "授权信息不匹配");
        } else {
            // token过期校验
            $uid = BikeUtil::access_token_index_to_uid_from_redis($redis, $db, $access_token);
            if($uid==0) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token校验失败");
            $tokenarr = BikeUtil::access_token_from_redis($redis, $db, $uid);
            if($tokenarr['expire_in']>0 && $tokenarr['expire_in']<(time()-$tokenarr['createtime'])) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token已过期");
        }
        // 检查设备是否下线运维
        $devarr = BikeUtil::device_info_from_redis($redis, $db, $devId);
        if($devarr['status']==-1) {
            return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "设备运营维护中");
        } else if($devarr['status']==0) {
            return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "设备未上线");
        }
        if($devarr['iscabinet']!=$iscabinet) {
            if($devarr['iscabinet']==1) {
                return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "请用储物柜扫码功能进行扫码");
            } else if($devarr['iscabinet']==2) {
                return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "请用陪护床扫码功能进行扫码");
            } else {
                return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "设备类型错误");
            }
        }
        // 判断网点押金
        $netpointarr = BikeUtil::netpoint_info_from_redis($redis, $db, $devarr['netpoint_id']);
        if(empty($netpointarr)) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "网点已下线");
        // 返回结果
        $pricearr = BikeUtil::price_info_from_redis($redis, $db, $devarr['price_id']);
        if(empty($pricearr)) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "设备未设置价格");
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
            if($nightstarttime==$curnightendtime) {
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
        else if($nightstarttime==$curnightendtime) {
            if(!($time>=$lightstarttime-600 && $time<=$lightendtime)) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "可用开始时间：".$pricearr['lightstart']);
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
        if($devarr['devtype_id']==0) {
            $devarr['type'] = 1;
        } else {
            $devtypearr = BikeUtil::dev_type_from_redis($redis, $db, $devarr['devtype_id']);
            $devarr['type'] = intval($devtypearr['type']);
        }
        // 判断用户押金是否正常
        $userarr = BikeUtil::userinfo_from_redis($redis, $db, $uid);
        if(bccomp($userarr['deposit'], $netpointarr['deposit'], 2)==-1) {
            $pricearr['depopay'] = bcsub($netpointarr['deposit'], $userarr['deposit'], 2);
        } else {
            $pricearr['depopay'] = 0;
        }
        return BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "获取详情成功", self::format_devdetail_array($devarr, $pricearr));
    }
    
    public static function repoDevDetail($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        // 参数
        $devId = intval($request['deviceId']);
        // 检查设备是否下线运维
        $devarr = BikeUtil::device_info_from_redis($redis, $db, $devId);
        if(empty($devarr)) {
            return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "设备不存在");
        } else if($devarr['status']!=0) {
            return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "设备非入库状态，无权限");
        }
        return BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "获取详情成功", $devarr);
    }
    
    public static function rptstat($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        // 参数
        $systemtime = date("Y-m-d H:i:s");
        $mac = $request['mac'];
        $formatmac = "";
        for($i=0; $i<strlen($mac); $i=$i+2) {
            $formatmac .= substr($mac, $i, 2).":";
        }
        $formatmac = rtrim($formatmac, ":");
        $imei = $request['imei'];
        $imsi = $request['imsi'];
        $lockstat = intval($request['lockstat']);
        var_dump("$systemtime the rptstat parameter is:...............");
        var_dump($request);
        $deviceId = BikeUtil::mac_index_to_devid_from_redis($redis, $db, $formatmac);
        $devarr = BikeUtil::device_info_from_redis($redis, $db, $deviceId);
        // NB设备关锁检测
        try {
            if($devarr['devtype_id']>0) {   // 设置了设备类型，默认为蓝牙锁不用检测
                $devtypearr = BikeUtil::dev_type_from_redis($redis, $db, $devarr['devtype_id']);
                if($devtypearr['type']&2) {     // 锁有NB能力
                    $rent_uid = $redis->hGet(BikeConstant::AIRPLUS_HASH_RENTED_DEVICE, $deviceId);
                    var_dump("$systemtime the dev rent stat during rptstat is:...............");
                    var_dump($rent_uid);
                    if($rent_uid!=false) {
                        $orderarr = BikeUtil::bedorder_from_redis($redis, $db, $rent_uid);
                        var_dump($orderarr);
                        switch($orderarr['pricetype']) {
                            case 1:     // 按时租
                                if($lockstat==1) {  // 锁状态为1，已关锁成功，结束订单 
                                    $reqarr['backendind'] = 1;
                                    $reqarr['rtn_admin_id'] = -1;
                                    $reqarr['order_no'] = $orderarr['order_no'];
                                    $reqarr['users_id'] = $orderarr['users_id'];
                                    $rtnbedrtn = Bedorder::rtnbed($reqarr);
                                    var_dump("$systemtime the rtnbed return is:..................");
                                    var_dump($rtnbedrtn);
                                }
                                break;
                            case 2:     // 按次，按天租；设置锁状态，用于定时脚本检测
                            case 3:
                                if(time()>$orderarr['canuseend']) {
                                    if($lockstat==1) {
                                        $reqarr['backendind'] = 1;
                                        $reqarr['rtn_admin_id'] = -1;
                                        $reqarr['order_no'] = $orderarr['order_no'];
                                        $reqarr['users_id'] = $orderarr['users_id'];
                                        $rtnbedrtn = Bedorder::rtnbed($reqarr);
                                        var_dump($rtnbedrtn);
                                    }
                                } else {
                                    $redis->hSet(BikeConstant::AIRPLUS_HASH_NB_LOCK_STAT, $lockstat);
                                }
                                break;
                            default:    // 订单为空
                                break;
                        }
                    }
                }
            }
        } catch(Exception $e) {
            var_dump($e);
        }
        // 低电量处理
        try {
            if(!empty($devarr) && !empty($devarr['netpoint_id'])) {
                $add['device_id'] = $deviceId;
                $add['netpoint_id'] = $devarr['netpoint_id'];
                $add['entity_id'] = $devarr['entity_id'];
                $add['power'] = intval($request['power']);
                $add['createtime'] = time();
                $add['updatetime'] = 0;
                $db->insert(BikeUtil::table_full_name(BikeConstant::TABLE_LOWPOWER))->cols($add)->query();
            }
        } catch(Exception $e) {
            // do nothing
        }
        // 判断设备上是否更换了sim卡，锁状态是否有变更
        try {
            $devsimarr = BikeUtil::devsim_info_from_redis($redis, $db, $formatmac);
            if(!empty($devsimarr)) {
                $save = [];
                if(strcmp($devsimarr['imei'], $imei)!=0) $save['imei'] = $devsimarr['imei'] = $imei;
                if(strcmp($devsimarr['imsi'], $imsi)!=0) $save['imsi'] = $devsimarr['imsi'] = $imsi;
                if($devsimarr['lockstat']!=$lockstat) $save['lockstat'] = $devsimarr['lockstat'] = $lockstat;
                if(!empty($save)) {
                    $cond['id'] = $devsimarr['id'];
                    $db->update(BikeUtil::table_full_name(BikeConstant::TABLE_DEVSIM))->cols($save)->where("id=:id")->bindValues($cond)->query();
                    $redis->hSet(BikeConstant::AIRPLUS_HASH_DEVSIM_INFO, $formatmac, json_encode($devsimarr));
                }
            } else {
                $add['mac'] = $formatmac;
                $add['imei'] = $redisave['imei'] = $imei;
                $add['imsi'] = $redisave['imsi'] = $imsi;
                $add['lockstat'] = $redisave['lockstat'] = $lockstat;
                $db->insert(BikeUtil::table_full_name(BikeConstant::TABLE_DEVSIM))->cols($add)->query();
                $redisave['id'] = $db->lastInsertId();
                $redis->hSet(BikeConstant::AIRPLUS_HASH_DEVSIM_INFO, $formatmac, json_encode($redisave));
            }
        } catch(Exception $e) {
            // do nothing
        }
        // 返回结果
        return 0;
    }
    
    /**
     * 格式化设备信息
     * @param object $cardetail
     * @param object $carmodel
     * @param object $cardiscount
     * @return json
     */
    private static function format_devdetail_array($devarr, $pricearr)
    {
//         $encryptkey = implode("", self::PRIVATE_KEY);
//         $encryptkey = chr(58) . chr(96) . chr(67) . chr(42) . chr(92) . chr(1) . chr(33) . chr(31) . chr(41) . chr(30) . chr(15) . chr(78) . chr(12) . chr(19) . chr(40) . chr(37);
//         $encryptkey = pack('H*', "5896674292133314130157812194037");
//         $blekey = pack('H*', "3A60432A5C01211F291E0F4E0C132825");
        $rtnarr['deviceId'] = $devarr['dev_id'];
        $rtnarr['mac'] = $devarr['mac'];
//         $rtnarr['blekey'] = base64_encode(mcrypt_encrypt(self::CIPHER, $encryptkey, $devarr['blekey'], self::MODE));
//         $rtnarr['blepwd'] = base64_encode(mcrypt_encrypt(self::CIPHER, $encryptkey, $devarr['blepwd'] . str_repeat("0", 16-strlen($devarr['blepwd'])) , self::MODE));
        $rtnarr['blekey'] = $devarr['blekey'];
        $rtnarr['blepwd'] = $devarr['blepwd'];
        $rtnarr['type'] = $devarr['type'];
        $rtnarr['lightstart'] = $pricearr['lightstart'];
        $rtnarr['lightend'] = $pricearr['lightend'];
        $rtnarr['nightstart'] = $pricearr['nightstart'];
        $rtnarr['nightend'] = $pricearr['nightend'];
        $rtnarr['hourdeposit'] = $pricearr['hourdeposit'];
        $rtnarr['hourprice'] = $pricearr['hourprice'];
        $rtnarr['timesdeposit'] = $pricearr['timesdeposit'];
        $rtnarr['lighttimesprice'] = $pricearr['lighttimesprice'];
        $rtnarr['nighttimesprice'] = $pricearr['nighttimesprice'];
        $rtnarr['daydeposit'] = $pricearr['daydeposit'];
        $rtnarr['dayprice'] = $pricearr['dayprice'];
        $rtnarr['depopay'] = $pricearr['depopay'];
        return $rtnarr;
    }
    
}
