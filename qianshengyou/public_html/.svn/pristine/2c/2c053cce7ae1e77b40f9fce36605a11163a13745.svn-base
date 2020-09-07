<?php
use Workerman\Lib\Db;
use Workerman\Lib\DbRedis;

/**
 * Advertise.php
 * @copyright           airplus
 * @license             http://www.airplus.com
 * @lastmodify          2016-7-27
 * */
class Issue
{
    /**
     * 上报故障
     * 
     * @param array $request
     * @return json
     */
    public static function rptissue($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        //参数
        $deviceId = intval($request['deviceId']);
        // token过期校验
        if(key_exists('username', $request)) {
            $request['title'] = "运维自提单_" . $request['username'];
            $add['users_id'] = 0;
        } else {
            $access_token = $request['access_token'];
            $uid = BikeUtil::access_token_index_to_uid_from_redis($redis, $db, $access_token);
            if($uid==0) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token校验失败");
            $tokenarr = BikeUtil::access_token_from_redis($redis, $db, $uid);
            if($tokenarr['expire_in']>0 && $tokenarr['expire_in']<(time()-$tokenarr['createtime'])) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token已过期");
            $add['users_id'] = $uid;
        }
        // 业务处理
        try {
            $add['device_id'] = $deviceId;
            $devarr = BikeUtil::device_info_from_redis($redis, $db, $deviceId);
            if(empty($devarr)) {
                return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "设备不存在");
            } else if(empty($devarr['netpoint_id'])) {
                return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "设备未上线");
            }
            $add['netpoint_id'] = $devarr['netpoint_id'];
            $netpointarr = BikeUtil::netpoint_info_from_redis($redis, $db, $devarr['netpoint_id']);
            $add['admin_id'] = $netpointarr['admin_id'];
            $add['entity_id'] = $devarr['entity_id'];
            $add['title'] = $request['title'];
            if(key_exists('pics', $request) && !empty($request['pics'])) $add['issue_images'] = $request['pics'];
            if(key_exists('desc', $request) && !empty($request['desc'])) $add['issue_desc'] = $request['desc'];
            $add['createtime'] = $add['updatetime'] = time();
            $temp = $db->insert(BikeUtil::table_full_name(BikeConstant::TABLE_ISSUE))->cols($add)->query();
            if($temp==null) {
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "故障反馈提交失败");
            } else {
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "故障反馈成功");
            }
        } catch(Exception $e) {
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $e->getMessage());
        }
        // 返回结果
        return $array;
    }
    
    /**
     * 低电量告警
     * @param unknown $request
     */
    public static function rptlowpower($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        //参数
        $access_token = $request['access_token'];
        $deviceId = intval($request['deviceId']);
        // token过期校验
        $uid = BikeUtil::access_token_index_to_uid_from_redis($redis, $db, $access_token);
        if($uid==0) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token校验失败");
        $tokenarr = BikeUtil::access_token_from_redis($redis, $db, $uid);
        if($tokenarr['expire_in']>0 && $tokenarr['expire_in']<(time()-$tokenarr['createtime'])) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token已过期");
        // 业务处理
        try {
            $cond['device_id'] = $deviceId;
            $powerlist = $db->select("id")->from(BikeUtil::table_full_name(BikeConstant::TABLE_LOWPOWER))->where("device_id=:device_id")->bindValues($cond)->limit(1)->query();
            $devarr = BikeUtil::device_info_from_redis($redis, $db, $deviceId);
            if(count($powerlist)==0) {
                $add['device_id'] = $deviceId;
                if(empty($devarr)) {
                    return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "设备不存在");
                } else if(empty($devarr['netpoint_id'])) {
                    return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "设备未上线");
                }
                $add['netpoint_id'] = $devarr['netpoint_id'];
                $add['entity_id'] = $devarr['entity_id'];
                $add['power'] = intval($request['power']);
                $add['status'] = 1;
                $add['createtime'] = $add['updatetime'] = time();
                $temp = $db->insert(BikeUtil::table_full_name(BikeConstant::TABLE_LOWPOWER))->cols($add)->query();
                if($temp==null) {
                    $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "低电量上报失败");
                } else {
                    $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "低电量上报成功");
                }
            } else {
                if(empty($devarr)) {
                    return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "设备不存在");
                } else if(empty($devarr['netpoint_id'])) {
                    return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "设备未上线");
                }
                $save['netpoint_id'] = $devarr['netpoint_id'];
                $save['entity_id'] = $devarr['entity_id'];
                $save['power'] = intval($request['power']);
                $save['status'] = 1;
                $save['updatetime'] = time();
                $temp = $db->update(BikeUtil::table_full_name(BikeConstant::TABLE_LOWPOWER))->cols($save)->where("device_id=:device_id")->bindValues($cond)->query();
                if($temp==null) {
                    $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "低电量上报失败");
                } else {
                    $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "低电量上报成功");
                }
            }
        } catch(Exception $e) {
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $e->getMessage());
        }
        // 返回结果
        return $array;
    }
    
    /**
     * 故障反馈列表
     * @param unknown $request
     */
    public static function issuelist($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        //参数
        $access_token = $request['access_token'];
        // token过期校验
        $uid = BikeUtil::access_token_index_to_uid_from_redis($redis, $db, $access_token);
        if($uid==0) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token校验失败");
        $tokenarr = BikeUtil::access_token_from_redis($redis, $db, $uid);
        if($tokenarr['expire_in']>0 && $tokenarr['expire_in']<(time()-$tokenarr['createtime'])) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token已过期");
        // 业务处理
        try {
            $cond['uid'] = $uid;
            $list = $db->orderByDESC(array("createtime"))->select(array("title", "issue_images", "issue_desc", "status"))->from(BikeUtil::table_full_name(BikeConstant::TABLE_ISSUE))->where("uid=:uid")->bindValues($cond)->query();
            if(!empty($adverlist)) {
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "获取成功", $list);
            } else {
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "列表为空");
            }
        } catch(Exception $e) {
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $e->getMessage());
        }
        // 返回结果
        return $array;
    }
    
}
