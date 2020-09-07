<?php
use Workerman\Lib\Db;
use Workerman\Lib\DbRedis;

/**
 * Bedorder.php
 * @copyright           airplus
 * @license             https://www.air-plus.cn
 * @lastmodify          2018s-7-27
 * */
class Bedorder
{
    /**
     * 租车订单预定支付
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
            // 检查用户是否已经存在订单
            $orderarr = BikeUtil::bedorder_from_redis($redis, $db, $uid);
            if(!empty($orderarr)) {
                $orderarr['deviceId'] = $orderarr['dev_id'];
                return BikeUtil::format_return_array(BikeConstant::Interface_Order_Has_Chg_Code, "已存在订单", $orderarr);
            }
            // 检查设备是否已被租用
            $rent_uid = $redis->hGet(BikeConstant::AIRPLUS_HASH_RENTED_DEVICE, $deviceId);
            if($rent_uid!=false) {
                $orderarr = BikeUtil::bedorder_from_redis($redis, $db, $rent_uid);
                if(!empty($orderarr) && ($orderarr['pricetype']==1 || ($orderarr['pricetype']!=1 && $orderarr['canuseend']>time()))) {
                    return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "设备租用中");
                }
            }
            // 生成订单
            $orderinfo = new BedorderInfo();
            // 获取押金信息
            $devarr = BikeUtil::device_info_from_redis($redis, $db, $deviceId);
            $netpointarr = BikeUtil::netpoint_info_from_redis($redis, $db, $devarr['netpoint_id']);
            if(empty($netpointarr)) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "网点已下线");
            // 判断用户押金
            $userarr = BikeUtil::userinfo_from_redis($redis, $db, $uid);
            if(bccomp($userarr['deposit'], 0, 2)==-1) {
                return BikeUtil::format_return_array(BikeConstant::Interface_Need_Deposit_Code, "未支付押金");
            } else if(bccomp($userarr['deposit'], $netpointarr['deposit'], 2)==-1) {
                return BikeUtil::format_return_array(BikeConstant::Interface_Need_Deposit_Code, "押金不足");
            }
            $orderinfo->price_id = $devarr['price_id'];
            // 获取价格信息，并判断设备是否可租
            $pricearr = BikeUtil::price_info_from_redis($redis, $db, $orderinfo->price_id);
            // 租用时段判断
            $year = date("Y", $orderinfo->createtime);
            $month = date("m", $orderinfo->createtime);
            $day = date("d", $orderinfo->createtime);
            $orderfrozentime = mktime(3, 0, 0, $month, $day, $year);
            $curnightendtime = mktime(intval(substr($pricearr['nightend'], 0, 2)), intval(substr($pricearr['nightend'], 3)), 0, $month, $day, $year);
            $lightstarttime = mktime(intval(substr($pricearr['lightstart'], 0, 2)), intval(substr($pricearr['lightstart'], 3)), 0, $month, $day, $year);
            $lightendtime = mktime(intval(substr($pricearr['lightend'], 0, 2)), intval(substr($pricearr['lightend'], 3)), 0, $month, $day, $year);
            $nightstarttime = mktime(intval(substr($pricearr['nightstart'], 0, 2)), intval(substr($pricearr['nightstart'], 3)), 0, $month, $day, $year);
            $nightendtime = mktime(intval(substr($pricearr['nightend'], 0, 2)), intval(substr($pricearr['nightend'], 3)), 0, $month, $day+1, $year);
            // 计价类型
            $orderinfo->pricetype = intval($request['pricetype']);
            // 日间可用开始、结束相同
            if($lightstarttime==$lightendtime) {
                if($nightstarttime==$curnightendtime) {
                    return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "设备无可用时段");
                } else {
                    if($orderinfo->createtime < $curnightendtime) {
                        if($orderinfo->createtime > $orderfrozentime) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "可用开始时间：".$pricearr['nightstart']);
                        $orderinfo->canusestart = mktime(intval(substr($pricearr['nightstart'], 0, 2)), intval(substr($pricearr['nightstart'], 3)), 0, $month, $day-1, $year);
                        $orderinfo->canuseend = $curnightendtime;
                    } else {
                        if(($nightstarttime-$orderinfo->createtime) > 600) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "可用开始时间：".$pricearr['nightstart']);
                        $orderinfo->canusestart = $nightstarttime;
                        $orderinfo->canuseend = $nightendtime;
                    }
                    $orderinfo->lightnight = 2;
                }
            }
            // 夜间可用开始、结束相同
            else if($nightstarttime==$curnightendtime) {
                if(!($orderinfo->createtime>=$lightstarttime-600 && $orderinfo->createtime<=$lightendtime)) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "可用开始时间：".$pricearr['lightstart']);
                $orderinfo->canusestart = $lightstarttime;
                $orderinfo->canuseend = $lightendtime;
                $orderinfo->lightnight = 1;
            } 
            // 夜间晚上时间租用
            else if($orderinfo->createtime >= $nightstarttime && $orderinfo->createtime <= $nightendtime) {
                $orderinfo->canusestart = $nightstarttime;
                $orderinfo->canuseend = $nightendtime;
                $orderinfo->lightnight = 2;
            }
            // 夜间凌晨时间租用
            else if($orderinfo->createtime < $curnightendtime) {
                $orderinfo->canusestart = mktime(intval(substr($pricearr['nightstart'], 0, 2)), intval(substr($pricearr['nightstart'], 3)), 0, $month, $day-1, $year);
                $orderinfo->canuseend = $curnightendtime;
                $orderinfo->lightnight = 2;
            } 
            // 日间租用
            else if($orderinfo->createtime >= $lightstarttime && $orderinfo->createtime <= $lightendtime) {
                $orderinfo->canusestart = $lightstarttime;
                $orderinfo->canuseend = $lightendtime;
                $orderinfo->lightnight = 1;
            }
            // 靠近夜间开始时间
            else if($orderinfo->createtime >= $lightendtime && $orderinfo->createtime <= $nightstarttime) {
                if(($nightstarttime-$orderinfo->createtime) > 600) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "可用开始时间：".$pricearr['nightstart']);
                $orderinfo->canusestart = $nightstarttime;
                $orderinfo->canuseend = $nightendtime;
                $orderinfo->lightnight = 2;
            } 
            // 靠近日间开始时间
            else {
                if(($lightstarttime-$orderinfo->createtime) > 600) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "可用开始时间：".$pricearr['lightstart']);
                $orderinfo->canusestart = $lightstarttime;
                $orderinfo->canuseend = $lightendtime;
                $orderinfo->lightnight = 1;
            }
            
//             //夜间租用， 及用户下单时间在日间结束 和 夜间开始之间
//             else if( ($orderinfo->createtime >= $nightstarttime && $orderinfo->createtime <= $nightendtime) 
//                 || ($orderinfo->createtime >= $lightendtime && $orderinfo->createtime <= $nightstarttime) ) {
//                 if($orderinfo->createtime >= $lightendtime && $orderinfo->createtime <= $nightstarttime) {
//                     if(($nightstarttime-$orderinfo->createtime) > 600)  return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "可用开始时间：".$pricearr['nightstart']);
//                 }
//                 $orderinfo->canusestart = $nightstarttime;
//                 $orderinfo->canuseend = $nightendtime;
//                 $orderinfo->lightnight = 2;
//             }
//             // 日间租用，及用户下单时间在夜间结束 和 日间开始之间
//             else {
//                 if(($lightstarttime-$orderinfo->createtime) > 600) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "可用开始时间：".$pricearr['lightstart']);
//                 $orderinfo->canusestart = $lightstarttime;
//                 $orderinfo->canuseend = $lightendtime;
//                 $orderinfo->lightnight = 1;
//             }
            // 日租加上日租天数
            if($orderinfo->pricetype==3) {
                $orderinfo->days = intval($request['days']);
                $orderinfo->canuseend += 86400 * ($orderinfo->days - 1);
            }
            // 根据计费方式计算价格
            $orderinfo->hourprice = $pricearr['hourprice'];
            $orderinfo->lighttimesprice = $pricearr['lighttimesprice'];
            $orderinfo->nighttimesprice = $pricearr['nighttimesprice'];
            $orderinfo->dayprice = $pricearr['dayprice'];
            switch($orderinfo->pricetype) {
                case 1:     // 按时租计费
                    $orderinfo->deposit = $pricearr['hourdeposit'];
                    $orderinfo->total = $orderinfo->pay = $orderinfo->deposit;
                    break;
                case 2:     // 按次租计费
                    $orderinfo->deposit = $pricearr['timesdeposit'];
                    if($orderinfo->lightnight==1) {
                        $orderinfo->total = $orderinfo->pay = bcadd($orderinfo->lighttimesprice, $orderinfo->deposit, 2);
                    } else {
                        $orderinfo->total = $orderinfo->pay = bcadd($orderinfo->nighttimesprice, $orderinfo->deposit, 2);
                    }
                    break;
                case 3:     // 按天租计费
                    $orderinfo->deposit = $pricearr['daydeposit'];
                    $orderinfo->total = $orderinfo->pay = bcadd($orderinfo->dayprice * $orderinfo->days, $orderinfo->deposit, 2);
                    break;
            }
            // 订单其他数据
            $pricedata['lightstart'] = $pricearr['lightstart'];
            $pricedata['lightend'] = $pricearr['lightend'];
            $pricedata['nightstart'] = $pricearr['nightstart'];
            $pricedata['nightend'] = $pricearr['nightend'];
            $orderinfo->pricedata = json_encode($pricedata);
            // 订单其他信息
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
            // 将订单缓存两个小时 + 1分钟
            $redis -> set($orderinfo->order_no, json_encode($orderarr), 7260);
            // 获取用户信息
            $request['item'] = BikeConstant::PAY_ITEM_RENT_CARS;
            $request['openid'] = $userarr['openid'];
            $request['domain'] = $request['domain'];
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
    
    public static function queryorder($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        // 参数
        $access_token = $request['access_token'];
        try {
            if(key_exists('phone', $request)) {
                // phone获取uid
                $uid = BikeUtil::phone_index_to_userid($redis, $db, $request['phone']);
                // token获取uid
                $tokenuid = BikeUtil::access_token_index_to_uid_from_redis_daikai($redis, $db, $access_token);
                if($tokenuid!=$uid || $tokenuid===0 || $uid===0) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "授权信息不匹配");
            } else {
                // token过期校验
                $uid = BikeUtil::access_token_index_to_uid_from_redis($redis, $db, $access_token);
                if($uid==0) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token校验失败");
                $tokenarr = BikeUtil::access_token_from_redis($redis, $db, $uid);
                if($tokenarr['expire_in']>0 && $tokenarr['expire_in']<(time()-$tokenarr['createtime'])) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token已过期");
            }
            // 检查用户是否已经存在订单
            $orderarr = BikeUtil::bedorder_from_redis($redis, $db, $uid);
            if(empty($orderarr)) return BikeUtil::format_return_array(BikeConstant::Interface_Order_Has_Chg_Code, "订单已结束");
            // 返回timestamp及签名
            $data['timestamp'] = time();
            switch($orderarr['pricetype']) {
                case 1:     // 按时租
                    if($data['timestamp']<(intval($orderarr['canusestart'])-600)) {
                        return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "可用开始时间：".date("Y-m-d H:i", $orderarr['canusestart'])."，只可提前10分钟开锁！");
                    }
                    break;
                case 2:     // 按次租
                case 3:     // 按月租
                    if($data['timestamp']<(intval($orderarr['canusestart'])-600)) {
                        return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "可用开始时间：".date("Y-m-d H:i", $orderarr['canusestart'])."，只可提前10分钟开锁！");
                    } else if($data['timestamp']>intval($orderarr['canuseend'])) {
                        return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "已到租期截止时间：".date("Y-m-d H:i", $orderarr['canuseend'])."，请操作还床退款后重新下单使用。若忘记关锁还床，请联系客服在后台操作还床！");
                    }
                    // 按天租还要判断是否在每天可用时段内
                    if($orderarr['pricetype']==3) {
                        $pricedata = json_decode($orderarr['pricedata'], true);
                        $year = date("Y", $orderarr['createtime']);
                        $month = date("m", $orderarr['createtime']);
                        $day = date("d", $orderarr['createtime']);
                        if($orderarr['lightnight']==1) {
                            $starthour = intval(substr($pricedata['lightstart'], 0, 2));
                            $startmin = intval(substr($pricedata['lightstart'], 3));
                            $endhour = intval(substr($pricedata['lightend'], 0, 2));
                            $endmin = intval(substr($pricedata['lightend'], 3));
                            if($data['timestamp']>=mktime($starthour, $startmin, 0, $month, $day, $year) && $data['timestamp']<mktime($endhour, $endmin, 0, $month, $day, $year)) {
                                $canusestart = mktime($starthour, $startmin, 0, $month, $day, $year);
                            } else if($data['timestamp']>=mktime($starthour, $startmin, 0, $month, $day-1, $year) && $data['timestamp']<mktime($endhour, $endmin, 0, $month, $day-1, $year)) {
                                $canusestart = mktime($starthour, $startmin, 0, $month, $day-1, $year);
                            }
                            if($data['timestamp']<($canusestart-600)) {
                                return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "可用开始时间：".date("Y-m-d H:i", $canusestart)."，只可提前10分钟开锁！");
                            }
                        } else {
                            $starthour = intval(substr($pricedata['nightstart'], 0, 2));
                            $startmin = intval(substr($pricedata['nightstart'], 3));
                            $endhour = intval(substr($pricedata['nightend'], 0, 2));
                            $endmin = intval(substr($pricedata['nightend'], 3));
                            if($data['timestamp']>=mktime($starthour, $startmin, 0, $month, $day, $year) && $data['timestamp']<mktime($endhour, $endmin, 0, $month, $day+1, $year)) {
                                $canusestart = mktime($starthour, $startmin, 0, $month, $day, $year);
                            } else if($data['timestamp']>=mktime($starthour, $startmin, 0, $month, $day-1, $year) && $data['timestamp']<mktime($endhour, $endmin, 0, $month, $day, $year)) {
                                $canusestart = mktime($starthour, $startmin, 0, $month, $day-1, $year);
                            }
                            if($data['timestamp']<($canusestart-600)) {
                                return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "可用开始时间：".date("Y-m-d H:i", $canusestart)."，只可提前10分钟开锁！");
                            }
                        }
                    }
                    break;
            }
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
            switch($orderarr['pricetype']) {
                case 1:     // 按时租
                    if($data['timestamp']<(intval($orderarr['canusestart'])-600)) {
                        return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "可用开始时间：".date("Y-m-d H:i", $orderarr['canusestart'])."，只可提前10分钟开锁！");
                    }
                    break;
                case 2:     // 按次租
                case 3:     // 按月租
                    if($data['timestamp']<(intval($orderarr['canusestart'])-600)) {
                        return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "可用开始时间：".date("Y-m-d H:i", $orderarr['canusestart'])."，只可提前10分钟开锁！");
                    } else if($data['timestamp']>intval($orderarr['canuseend'])) {
                        return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "已到租期截止时间：".date("Y-m-d H:i", $orderarr['canuseend'])."，请操作还床退款后重新下单使用。若忘记关锁还床，请联系客服在后台操作还床！");
                    }
                    // 按天租还要判断是否在每天可用时段内
                    if($orderarr['pricetype']==3) {
                        $pricedata = json_decode($orderarr['pricedata'], true);
                        $year = date("Y", $orderarr['createtime']);
                        $month = date("m", $orderarr['createtime']);
                        $day = date("d", $orderarr['createtime']);
                        if($orderarr['lightnight']==1) {
                            $starthour = intval(substr($pricedata['lightstart'], 0, 2));
                            $startmin = intval(substr($pricedata['lightstart'], 3));
                            $endhour = intval(substr($pricedata['lightend'], 0, 2));
                            $endmin = intval(substr($pricedata['lightend'], 3));
                            if($data['timestamp']>=mktime($starthour, $startmin, 0, $month, $day, $year) && $data['timestamp']<mktime($endhour, $endmin, 0, $month, $day, $year)) {
                                $canusestart = mktime($starthour, $startmin, 0, $month, $day, $year);
                            } else if($data['timestamp']>=mktime($starthour, $startmin, 0, $month, $day-1, $year) && $data['timestamp']<mktime($endhour, $endmin, 0, $month, $day-1, $year)) {
                                $canusestart = mktime($starthour, $startmin, 0, $month, $day-1, $year);
                            }
                            if($data['timestamp']<($canusestart-600)) {
                                return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "可用开始时间：".date("Y-m-d H:i", $canusestart)."，只可提前10分钟开锁！");
                            }
                        } else {
                            $starthour = intval(substr($pricedata['nightstart'], 0, 2));
                            $startmin = intval(substr($pricedata['nightstart'], 3));
                            $endhour = intval(substr($pricedata['nightend'], 0, 2));
                            $endmin = intval(substr($pricedata['nightend'], 3));
                            if($data['timestamp']>=mktime($starthour, $startmin, 0, $month, $day, $year) && $data['timestamp']<mktime($endhour, $endmin, 0, $month, $day+1, $year)) {
                                $canusestart = mktime($starthour, $startmin, 0, $month, $day, $year);
                            } else if($data['timestamp']>=mktime($starthour, $startmin, 0, $month, $day-1, $year) && $data['timestamp']<mktime($endhour, $endmin, 0, $month, $day, $year)) {
                                $canusestart = mktime($starthour, $startmin, 0, $month, $day-1, $year);
                            }
                            if($data['timestamp']<($canusestart-600)) {
                                return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "可用开始时间：".date("Y-m-d H:i", $canusestart)."，只可提前10分钟开锁！");
                            }
                        }
                    }
                    break;
            }
            $devarr = BikeUtil::device_info_from_redis($redis, $db, $orderarr['dev_id']);
            $redis->set("auth_".$devarr['mac'], 1, 600);
            var_dump("below is assignauth.....................");
            var_dump("auth_".$devarr['mac'] . " is set to: " . $redis->get("auth_".$devarr['mac']));
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "授权成功", $data);
        } catch(Exception $e) {
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $e->getMessage());
        }
        // 返回结果
        return $array;
    }
    
    public static function assignauthtest($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        try {
            var_dump("below is assignauthtest request.....................");
            var_dump($request);
            $devarr = BikeUtil::device_info_from_redis($redis, $db, $request['deviceId']);
            if($devarr['status']!==0) {
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "入库或维护状态设备才可进行整机测试");
            }
            $redis->set("auth_".$devarr['mac'], 1, 600);
            var_dump("below is assignauthtest.....................");
            var_dump("auth_".$devarr['mac'] . " is set to: " . $redis->get("auth_".$devarr['mac']));
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "授权成功");
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
            $formatmac = "";
            for($i=0; $i<strlen($mac); $i=$i+2) {
                $formatmac .= substr($mac, $i, 2).":";
            }
            $formatmac = rtrim($formatmac, ":");
            $authstat = $redis->get("auth_".$formatmac);
            var_dump("below is queryauth.....................");
            var_dump("auth_".$formatmac . " value is: " . $authstat);
            if($authstat) {
                $redis->del("auth_".$formatmac);
                $result = 0;
            } else {
                $result = 1;
            }
            var_dump("the queryauth return is: " . $result);
            return $result;
        } catch(Exception $e) {
            return 99;
        }
    }
    
    public static function rtnbed($request)
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
                if(key_exists('phone', $request)) {
                    // phone获取uid
                    $uid = BikeUtil::phone_index_to_userid($redis, $db, $request['phone']);
                    // token获取uid
                    $tokenuid = BikeUtil::access_token_index_to_uid_from_redis_daikai($redis, $db, $access_token);
                    if($tokenuid!=$uid || $tokenuid===0 || $uid===0) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "授权信息不匹配");
                } else {
                    // token过期校验
                    $uid = BikeUtil::access_token_index_to_uid_from_redis($redis, $db, $access_token);
                    if($uid==0) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token校验失败");
                    $tokenarr = BikeUtil::access_token_from_redis($redis, $db, $uid);
                    if($tokenarr['expire_in']>0 && $tokenarr['expire_in']<(time()-$tokenarr['createtime'])) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token已过期");
                }
            }
            $orderarr = BikeUtil::bedorder_from_redis($redis, $db, $uid);
            if(empty($orderarr)) {
                return BikeUtil::format_return_array(BikeConstant::Interface_Order_Has_Chg_Code, "订单已结束");
            } else if(strcmp($order_no, $orderarr['order_no'])!=0) {
                return BikeUtil::format_return_array(BikeConstant::Interface_Order_Has_Chg_Code, "订单不匹配");
            }
            // 按计费类型处理订单费用
            $orderarr['endtime'] = $save['endtime'] = time();
            switch($orderarr['pricetype']) {
                case 1:     // 按小时收费
                    // 计算时间费用
                    $orderfee = self::hour_fee_calculator($orderarr);
                    if(bccomp($orderarr['deposit'], $orderfee, 2)>0) {
                        $save['refund'] = $orderarr['refund'] = bcsub($orderarr['deposit'], $orderfee, 2);
                        $save['refundnote'] = "退款已提交，待退款通知更新结果";
                    } else {
                        $save['refund'] = $orderarr['refund'] = 0;
                        $save['refundnote'] = "无剩余退款";
                    }
                    break;
                case 2:     // 按次收费
                case 3:     // 按天收费
                    if($orderarr['endtime'] - $orderarr['createtime']<=600) {   // 10分钟内免费
                        $save['refund'] = $orderarr['refund'] = $orderarr['total'];
                        $save['refundnote'] = "退款已提交，待退款通知更新结果";
                    } else {
                        $save['refund'] = $orderarr['refund'] = $orderarr['deposit'];
                    }
            }
            // 取消订单
            $save['step'] = 9000;
            $save['endtime'] = time();
            $wherestr = sprintf("users_id=%d and step=%d and status=1 and order_no='%s'", intval($uid), intval($orderarr['step']), $order_no);
            $temp = $db->update(BikeUtil::table_full_name(BikeConstant::TABLE_BEDORDER))->cols($save)->where($wherestr)->query();
            if($temp==null) {
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "还床失败");
            } else {
                if(key_exists('refundnote', $save)) {
                    $msg = "还床成功，剩余按时租用预付金将原路退回，请在微信内支付通知注意查收退款消息";
                } else {
                    $msg = "还床成功";
                }
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, $msg);
                // 更新订单缓存
                $redis->hDel(BikeConstant::AIRPLUS_HASH_BEDORDER, $order_no);
                $redis->hDel(BikeConstant::AIRPLUS_HASH_USER_CURRENT_ORDER, $uid);
                $redis->hDel(BikeConstant::AIRPLUS_HASH_RENTED_DEVICE, $orderarr['dev_id']);
                // 按时租用退款
                if(bccomp($orderarr['refund'], 0, 2)==1) {
                    $rtnbedrefund = self::wxpay_tran_refund($db, $orderarr);
                    var_dump($rtnbedrefund);
                }
                // NB关锁，消息推送
                if(key_exists('rtn_admin_id', $request) && $orderarr['pricetype']==1) {
                    // 发送推送
                    $userarr = BikeUtil::userinfo_from_redis($redis, $db, $uid);
                    $shortmsgind = true;
                    if(!empty($userarr['openid'])) {
                        $formisredis = $redis -> hGet(BikeConstant::AIRPLUS_HASH_FORMID, $uid);
                        if($formisredis!=false) {
                            $formidarr = json_decode($formisredis, true);
                            $formidcnt = count($formidarr);
                            if($formidcnt>0) {
                                $loopcnt = 0;
                                $time = time();
                                foreach ($formidarr as $key=>$val) {
                                    if($time-$val['time']<=604795 && strpos($val['formid'], "the formId is a mock one")===false) {
                                        $accesstoken = BikeUtil::wxaccesstoken($redis, BikeConstant::AIRPLUS_WXAPP_APPID, BikeConstant::AIRPLUS_WXAPP_APPSECRET);
                                        if(!empty($accesstoken)) {
                                            $data['touser'] = $userarr['openid'];
                                            $data['template_id'] = "99INU_MWsrPMwhbmvqs7xK1Cy_xXMZfQhPrCbnNvgGE";      // 还床通知模板消息ID
                                            $data['page'] = "pages/prelogin/prelogin";
                                            $data['form_id'] = $val['formid'];
                                            $data['data']['keyword1']['value'] = $orderarr['dev_id'];
                                            $data['data']['keyword2']['value'] = date("Y-m-d", $orderarr['createtime']);
                                            $netpointarr = BikeUtil::netpoint_info_from_redis($redis, $db, $orderarr['netpoint_id']);
                                            $data['data']['keyword3']['value'] = $netpointarr['name'];
                                            $data['data']['keyword4']['value'] = "";
                                            $duration = $orderarr['endtime'] - $orderarr['createtime'];
                                            $hours = floor($duration/3600);
                                            if($hours>0) $data['data']['keyword4']['value'] .= $hours."小时";
                                            $minutes = ceil(($duration-$hours*3600)/60);
                                            if($minutes>0) $data['data']['keyword4']['value'] .= $minutes . "分钟（不足整时按整时计费）";
                                            if($orderfee==0) {
                                                $data['data']['keyword5']['value'] = "免费(10分钟以内免费)";
                                            } else {
                                                $data['data']['keyword5']['value'] = $orderfee . "元";
                                            }
                                            $data['data']['keyword6']['value'] = "订单已结束，若有押金且需退押金，请进入小程序“我的->钱包”中退押金。点击进入小程序";   // 双锁版本提示
                                            $data['data']['keyword6']['color'] = "#ED642A";
                                            $noticeresult = BikeUtil::wxtmplnotice($accesstoken, $data);
                                            var_dump($noticeresult);
                                            if($noticeresult) {
                                                $loopcnt++;
                                                $shortmsgind = false;
                                            } else {
                                                $loopcnt++;
                                                continue;
                                            }
                                        }
                                        break;
                                    } else {
                                        $loopcnt++;
                                        continue;
                                    }
                                }
                                // 删除过期，已成功使用的formid，并将剩余数据保存至redis
                                array_splice($formidarr, 0, $loopcnt);
                                $redis -> hSet(BikeConstant::AIRPLUS_HASH_FORMID, $uid, json_encode($formidarr));
                            }
                        }
                    }
                    // 发送通知短信
                    if($shortmsgind) {
                        // todo
                    }
                    return 0;
                }
            }
        } catch(Exception $e) {
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $e->getMessage());
        }
        // 返回结果
        return $array;
    }
    
    private static function wxpay_tran_refund($db, $request)
    {
        $time = time();
        $input = new WxPayRefund();
        $input->SetTransaction_id($request['tran_id']);
        $input->SetOut_refund_no($request['order_no']."_".$time);
        $input->SetTotal_fee($request['pay'] * 100);
        $input->SetRefund_fee($request['refund'] * 100);
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
//             var_dump($input);
            $resp = WxPayApi::refund($input);
//             var_dump($resp);
            // 微信退款结果判断
            if($resp['return_code']=="SUCCESS" && $resp['result_code']=="SUCCESS") {
                // 启动transaction
                $db->beginTrans();
                // carorder表数据更新
                $ordersave['refund'] = round(intval($resp['refund_fee'])/100, 2);
                $ordersave['refundtime'] = $time;
                $ordersave['refundnote'] = "按时租预付金自动退款成功";
                // carorder表条件
                $orderwherestr = sprintf("users_id=%d and step=%d and status=1 and order_no='%s'", intval($request['users_id']), BikeConstant::ORDER_FINISHED, $request['order_no']);
                // trans表数据更新
                $transave['refund_id'] = $resp['refund_id'];
                $transave['refund_fee'] = $ordersave['refund'];
                $transave['refund_time'] = $time;
                // trans表条件
                $trancond['uid'] = $request['users_id'];
                $trancond['tran_id'] = $request['tran_id'];
                // 表操作执行
                $ordertemp = $db->update(BikeUtil::table_full_name(BikeConstant::TABLE_BEDORDER))->cols($ordersave)->where($orderwherestr)->query();
                $trantemp = $db->update(BikeUtil::table_full_name(BikeConstant::TABLE_TRANS))->cols($transave)->where("uid=:uid and item=4 and tran_id=:tran_id")->bindValues($trancond)->query();
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
    
    public static function cancel($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        // 参数
        $access_token = $request['access_token'];
        $order_no = $request['order_no'];
        try {
            // token过期校验
            $uid = BikeUtil::access_token_index_to_uid_from_redis($redis, $db, $access_token);
            if($uid==0) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token校验失败");
            $tokenarr = BikeUtil::access_token_from_redis($redis, $db, $uid);
            if($tokenarr['expire_in']>0 && $tokenarr['expire_in']<(time()-$tokenarr['createtime'])) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token已过期");
            // 检测订单状态是否正常
            $orderarr = BikeUtil::carorder_from_redis($redis, $db, $uid, $order_no);
            if(empty($orderarr)) {
                return BikeUtil::format_return_array(BikeConstant::Interface_Order_Has_Chg_Code, "订单不存在");
            } else if($orderarr['status']!==1) {
                return BikeUtil::format_return_array(BikeConstant::Interface_Order_Has_Chg_Code, "订单已被取消");
            } else if($orderarr['step']!=BikeConstant::ORDER_PEND_PAY) {
                return BikeUtil::format_return_array(BikeConstant::Interface_Order_Has_Chg_Code, "订单状态已变更", $orderarr);
            }
            // 取消订单
            $cond['uid'] = $uid;
            $cond['order_no'] = $order_no;
            $save['status'] = $orderarr['status'] = -1;
            $temp = $db->update(BikeUtil::table_full_name(BikeConstant::TABLE_CARORDER))->cols($save)->where("uid=:uid and order_no=:order_no")->bindValues($cond)->query();
            if($temp==null) {
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "订单取消失败");
            } else {
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "订单取消成功", $save);
                // 更新订单缓存
                $redis->hSet(BikeConstant::AIRPLUS_HASH_CAR_ORDER, $order_no, json_encode($orderarr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            }
        } catch(Exception $e) {
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $e->getMessage());
        }
        // 返回结果
        return $array;
    }
    
    public static function refund($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        // 参数
        $order_no = $request['order_no'];
        try {
            if(key_exists('backendind', $request) && $request['backendind']==1) {
                $uid = $request['uid'];
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
            $tranlist = $db->select(array('payment, uid, tran_id, amount, refund_fee, tran_time'))->from(BikeUtil::table_full_name(BikeConstant::TABLE_TRANS))->where('uid=:uid and item=4 and order_no=:order_no')->bindValues($cond)->limit(1)->query();
            if(empty($tranlist)) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, BikeConstant::REFUND_TRANS_EMPTY_MSG);
            // 根据支付渠道，调用退款方法
            switch(intval($tranlist[0]['payment'])) {
                case BikeConstant::PAY_CHANNEL_WEIXIN:
                case BikeConstant::PAY_CHANNEL_WXJSAPI:
                    $array = self::wxpay_tran_refund($db, $request + $tranlist[0]);
                    break;
                case BikeConstant::PAY_CHANNEL_ALIPAY:
                    $array = self::alipay_tran_refund($db, $request + $tranlist[0]);
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
    
    private static function wxpay_tran_refund_old($db, $request)
    {
        $time = time();
        $input = new WxPayRefund();
        $input->SetTransaction_id($request['tran_id']);
        $input->SetOut_refund_no($request['order_no']."_".$time);
        $input->SetTotal_fee(round($request['amount'],2) * 100);
        $input->SetRefund_fee(round($request['amount']*0.9,2) * 100);
        // 设置退款appid、商户、xxx等信息
        if($request['payment']==BikeConstant::PAY_CHANNEL_WXJSAPI) {
            $input->SetOp_user_id(WxPayConfig::MCHID_JSAPI);
            $input->SetAppid(WxPayConfig::APPID_JSAPI);
            $input->SetMch_id(WxPayConfig::MCHID_JSAPI);
        } else {
            $input->SetOp_user_id(WxPayConfig::MCHID);
        }
        // 退款流程
        try {
            $resp = WxPayApi::refund($input);
            // var_dump($resp);
            // 微信退款结果判断
            if($resp['return_code']=="SUCCESS" && $resp['result_code']=="SUCCESS") {
                // 启动transaction
                $db->beginTrans();
                // carorder表数据更新
                $ordersave['refund'] = round(intval($resp['refund_fee'])/100, 2);
                $ordersave['refundtime'] = $time;
                $ordersave['cancelnote'] = "退款成功";
                // carorder表条件
                $ordercond['uid'] = $trancond['uid'] = $reasonadd['uid'] = $request['uid'];
                $ordercond['order_no'] = $reasonadd['order_no'] = $request['order_no'];
                // trans表数据更新
                $transave['refund_id'] = $resp['refund_id'];
                $transave['refund_fee'] = round(intval($resp['refund_fee'])/100 + $request['refund_fee'], 2);
                $transave['refund_time'] = $time;
                // trans表条件
                $trancond['tran_id'] = $request['tran_id'];
                // 取消原因
                $reasonadd['reason'] = "取消订单自动退款";
                $reasonadd['createtime'] = $time;
                // 表操作执行
                // order cond: status=-1原因是，前面已经取消订单成功后，才发起退款的，因此status必须为取消的状态-1
                $ordertemp = $db->update(BikeUtil::table_full_name(BikeConstant::TABLE_CARORDER))->cols($ordersave)->where("uid=:uid and order_no=:order_no and status=-1")->bindValues($ordercond)->query();
                $trantemp = $db->update(BikeUtil::table_full_name(BikeConstant::TABLE_TRANS))->cols($transave)->where("uid=:uid and item=4 and tran_id=:tran_id")->bindValues($trancond)->query();
                $reasontemp = $db->insert(BikeUtil::table_full_name(BikeConstant::TABLE_ORDEREASON))->cols($reasonadd)->query();
                // 执行结果判断
                $extramsg = BikeConstant::REFUND_INVOKE_SUCCESS_MSG;
                if($trantemp==null || $ordertemp==null || $reasontemp==null) {
                    $db->rollBackTrans();
                    $extramsg .= "，但db数据保存失败，请立即联系技术支持，数据为：" . json_encode($transave) . " - " . json_encode($trancond) . " - " . json_encode($ordersave) . " - " . json_encode($ordercond). " - " . json_encode($reasonadd);
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
    
    public static function pendlist($request)
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
            $tablebedorder = BikeUtil::table_full_name(BikeConstant::TABLE_BEDORDER);
            $tabledevice = BikeUtil::table_full_name(BikeConstant::TABLE_DEVICE);
            $statement = sprintf("select count(*) as count from %s where users_id=%d and status=1 and step=%d", $tablebedorder, $uid, BikeConstant::ORDER_PAID);
            $countarr = $db->query($statement);
            $data['total'] = intval($countarr[0]['count']);
            // 查询数据
            $cond['users_id'] = $uid;
            $cond['step'] = BikeConstant::ORDER_PAID;
            $list = $db->orderByASC(array('step'))->select(array("order_no","$tablebedorder.dev_id","pricetype","lightnight","canusestart","canuseend","hourprice","lighttimesprice","nighttimesprice","dayprice","days","total","pay","refund","step","$tablebedorder.createtime","endtime","$tabledevice.mac","blekey","blepwd","devtype_id"))->
                from($tablebedorder)->innerJoin($tabledevice,"$tablebedorder.dev_id = $tabledevice.dev_id")->
                where("users_id=:users_id and $tablebedorder.status=1 and step=:step")->bindValues($cond)->page($page)->query();
            if(!empty($list)) {
                foreach ($list as $key=>$val) {
                    if($val['devtype_id']==0) {
                        $list[$key]['type'] = 1;
                    } else {
                        $devtypearr = BikeUtil::dev_type_from_redis($redis, $db, $val['devtype_id']);
                        $list[$key]['type'] = intval($devtypearr['type']);
                    }
                }
            }
            $data['list'] = $list;
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
            $tablebedorder = BikeUtil::table_full_name(BikeConstant::TABLE_BEDORDER);
            $tablenetpoint = BikeUtil::table_full_name(BikeConstant::TABLE_NETPOINT);
            $statement = sprintf("select count(*) as count from %s where users_id=%d and status=1", $tablebedorder, $uid);
            $countarr = $db->query($statement);
            $data['total'] = intval($countarr[0]['count']);
            // 查询数据
            $cond['users_id'] = $uid;
            $data['list'] = $db->orderByDESC(array('createtime'))->select(array("order_no","dev_id","pricetype","lightnight","canusestart","canuseend","hourprice","lighttimesprice","nighttimesprice","dayprice","days","total","pay","refund","step","createtime","endtime","shortname"))->
                from($tablebedorder)->innerJoin($tablenetpoint,"$tablebedorder.netpoint_id = $tablenetpoint.id")->
                where("users_id=:users_id and $tablebedorder.status=1")->bindValues($cond)->page($page)->query();
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
    
    private static function hour_fee_calculator($orderarr)
    {
        $interval = $orderarr['endtime'] - $orderarr['createtime'];
        if($interval<=600) return 0;
        $hours = ceil($interval/3600);
        return round($hours * $orderarr['hourprice'], 2);
    }
    
}
