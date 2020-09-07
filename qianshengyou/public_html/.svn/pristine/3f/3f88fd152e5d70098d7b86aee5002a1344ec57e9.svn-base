<?php
use Workerman\Lib\Db;
use Workerman\Lib\DbRedis;

/**
 * OAuth.php
 * @copyright           airplus
 * @license             http://www.airplus.com
 * @lastmodify          2016-7-27
 * */
class Oauth
{
    /**
     * 登录接口，获取token
     * 
     * @param array $request
     * @return json
     */
    public static function token($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        // 参数
        $phone = $request['phone'];
        $password = md5(trim($request['password']));
        try {
            // 通过clientId和clientSecret识别用户所属厂商
            $request['clientId'] = BikeUtil::RsaPrivateDecrypt($request['clientId']);
            // 通过clientId和clientSecret识别用户所属厂商
            $companyId = BikeUtil::client_index_to_company_from_redis($redis, $db, $request['clientId']);
            if($companyId==0) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "获取签名秘钥失败");
            $companyarr = BikeUtil::company_info_from_redis($redis, $db, $companyId);
            if(strcasecmp($request['sign'], BikeUtil::MakeSign($request, $companyarr['clientSecret']))!=0) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "签名校验失败");
            // 通过companyId和phone识别用户是否已注册
            $uid = BikeUtil::cid_phone_index_to_userid($redis, $db, $companyId, $phone);
            if($uid==0) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, BikeConstant::USER_NOT_REGISTER_MSG);
            // 获取用户信息
            $userarr = BikeUtil::userinfo_from_redis($redis, $db, $uid);
            // 登录信息校验
            if(strcmp($userarr['password'], $password)==0) {
                $curtime = time();
                // Oauth token表
                $oauthinfo = new OauthInfo();
                $oauthinfo->id = $uid;
                $oauthinfo->access_token = BikeUtil::generate_access_token($uid, $phone, $password, $oauthinfo->createtime);
                $oauthinfo->clientip = $request["clientip"];
                $oautharr = get_object_vars($oauthinfo);
                // 数据写入db
                $temp = $db->insert(BikeUtil::table_full_name(BikeConstant::TABLE_OAUTH))->cols($oautharr)->query();
                if($temp==null) {
                    $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, BikeConstant::AUTHENCODE_FAILED_REASON);
                } else {
                    // 删除老的access_token到uid的索引，并更新新的access_token缓存
                    $pretokenredis = $redis -> hGet(BikeConstant::AIRPLUS_HASH_ACCESS_TOKEN, $uid);
                    if($pretokenredis!=false) {
                        $pretokenarr = json_decode($pretokenredis, true);
                        $redis->hDel(BikeConstant::AIRPLUS_HASH_ACCESS_TOKEN_TO_UID, $pretokenarr['access_token']);
                    }
                    $redis->hSet(BikeConstant::AIRPLUS_HASH_ACCESS_TOKEN_TO_UID, $oautharr["access_token"], $uid);
                    $redis->hSet(BikeConstant::AIRPLUS_HASH_ACCESS_TOKEN, $uid, json_encode($oautharr));
                    // 成功返回值
                    $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, BikeConstant::AUTHENCODE_PASS_REASON, BikeUtil::format_user_array($userarr, $oautharr));
                }
            } else {
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, BikeConstant::AUTHEN_WRONG_PASSWORD_REASON);
            }
        } catch(Exception $e) {
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $e->getMessage());
        }
        // 返回结果
        return $array;
    }
    
    public static function saveformids($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        // 参数
        $access_token = $request['access_token'];
        try {
            // 通过clientId和clientSecret识别用户所属厂商
            $request['clientId'] = BikeUtil::RsaPrivateDecrypt($request['clientId']);
            // 通过clientId和clientSecret识别用户所属厂商
            $companyId = BikeUtil::client_index_to_company_from_redis($redis, $db, $request['clientId']);
            if($companyId==0) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "获取签名秘钥失败");
            $companyarr = BikeUtil::company_info_from_redis($redis, $db, $companyId);
            if(strcasecmp($request['sign'], BikeUtil::MakeSign($request, $companyarr['clientSecret']))!=0) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "签名校验失败");
            // 通过companyId和phone识别用户是否已注册
            $uid = BikeUtil::access_token_index_to_uid_from_redis($redis, $db, $access_token);
            if($uid==0) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, BikeConstant::TOKEN_VALIDATION_FAILED_MSG);
            // 保存formids
            BikeUtil::saveformid($redis, $uid, json_decode($request['formids'], true));
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "保存成功");
        } catch(Exception $e) {
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $e->getMessage());
        }
        // 返回结果
        return $array;
    }
    
    public static function rsaEncryptTest($request)
    {
        return BikeUtil::RsaPublicEncrypt($request['clientId']);
    }
    
    public static function makeSign($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        // 通过clientId和clientSecret识别用户所属厂商
        $companyId = BikeUtil::client_index_to_company_from_redis($redis, $db, $request['clientId']);
        if($companyId==0) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "获取签名秘钥失败");
        $companyarr = BikeUtil::company_info_from_redis($redis, $db, $companyId);
        return BikeUtil::MakeSign($request, $companyarr['clientSecret']);
    }
    
}
