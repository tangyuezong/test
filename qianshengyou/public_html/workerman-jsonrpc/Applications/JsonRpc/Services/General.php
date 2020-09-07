<?php
use Workerman\Lib\Db;
use Workerman\Lib\DbRedis;

/**
 * User.php
 * @copyright           cobike
 * @license             http://www.cobike.cn
 * @lastmodify          2016-7-27
 * */

class General
{
    public static function saveformids($request)
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
            // 保存formids
            BikeUtil::saveformid($redis, $tokenarr['id'], json_decode($request['formids'], true));
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "保存成功");
        } catch(Exception $e) {
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $e->getMessage());
        }
        // 返回结果
        return $array;
    }
    
}