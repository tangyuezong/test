<?php
use Workerman\Lib\Db;
use Workerman\Lib\DbRedis;

/**
 * User.php
 * @copyright           cobike
 * @license             http://www.cobike.cn
 * @lastmodify          2016-7-27
 * */
class TheEmptyObj{}

class User
{
    public static function wxlogin($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        // 参数
        $code = $request['code'];
        try {
            $openarr = BikeUtil::getopenid(BikeConstant::AIRPLUS_WXAPP_APPID, BikeConstant::AIRPLUS_WXAPP_APPSECRET, $code);
            if(empty($openarr)) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "开放ID为空");
            $openid = $openarr['openid'];
            $uid = BikeUtil::openid_index_to_uid($redis, $db, $openid);
            // 用户首次使用
            if($uid==0) {
                // 生成用户ID
                $uid = BikeUtil::get_next_autoincrement($redis, $db, BikeConstant::TABLE_USERS);
                if($uid<1) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, BikeConstant::AUTHENCODE_GENERATE_ID_WRONG_REASON);
                // 用户表
                $userinfo = new UserInfo();
                $userinfo->id = $uid;
                $userinfo->openid = $openid;
                // Oauth token表
                $oauthinfo = new OauthInfo();
                $oauthinfo->id = $uid;
                $oauthinfo->access_token = BikeUtil::generate_access_token($uid, $openid, $oauthinfo->createtime);
                $oauthinfo->clientip = $request["clientip"];
                // 数据对象转保存db array
                $userarr = get_object_vars($userinfo);
                $oautharr = get_object_vars($oauthinfo);
                // 数据写DB
                $db->beginTrans();
                $usertemp = $db->insert(BikeUtil::table_full_name(BikeConstant::TABLE_USERS))->cols($userarr)->query();
                $oauthtemp = $db->insert(BikeUtil::table_full_name(BikeConstant::TABLE_OAUTH))->cols($oautharr)->query();
                if($usertemp==null || $oauthtemp==null) {
                    $db->rollBackTrans();
                    $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, BikeConstant::REGISTER_WRITE_DB_REASON);
                } else {
                    $db->commitTrans();
                    // 获取缓存array
                    $userarr = BikeUtil::userinfo_redis_saving_parser($userarr);
                    $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, BikeConstant::REGISTER_PASS_REASON, BikeUtil::format_user_array($userarr, $oautharr));
                    // 更新redis
                    $redis->hSet(BikeConstant::AIRPLUS_HASH_USERINFO, $uid, json_encode($userarr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
                    $redis->hSet(BikeConstant::AIRPLUS_HASH_ACCESS_TOKEN_TO_UID, $oautharr["access_token"], $uid);
                    $redis->hSet(BikeConstant::AIRPLUS_HASH_ACCESS_TOKEN, $uid, json_encode($oautharr));
                    // 保存session_key用来微信授权获取手机号时使用
                    $redis->hSet(BikeConstant::AIRPLUS_HASH_OPENID_INDEX_TO_SESSION_KEY, $openid, $openarr['session_key']);
                }
            }
            // 用户已经存在
            else {
                $userarr = BikeUtil::userinfo_from_redis($redis, $db, $uid);
                // Oauth token表
                $oauthinfo = new OauthInfo();
                $oauthinfo->id = $uid;
                $oauthinfo->access_token = BikeUtil::generate_access_token($uid, $openid, $oauthinfo->createtime);
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
                    // 保存session_key用来微信授权获取手机号时使用
                    $redis->hSet(BikeConstant::AIRPLUS_HASH_OPENID_INDEX_TO_SESSION_KEY, $openid, $openarr['session_key']);
                    // 获取陪护床订单信息
                    $orderarr = BikeUtil::bedorder_from_redis($redis, $db, $uid);
                    if(!empty($orderarr)) {
                        $bedtemp['order_no'] = $orderarr['order_no'];
                        $bedtemp['deviceId'] = $orderarr['dev_id'];
                        $devarr = BikeUtil::device_info_from_redis($redis, $db, $orderarr['dev_id']);
                        $bedtemp['mac'] = $devarr['mac'];
                        $bedtemp['blekey'] = $devarr['blekey'];
                        if($devarr['devtype_id']==0) {
                            $bedtemp['type'] = 1;
                        } else {
                            $devtypearr = BikeUtil::dev_type_from_redis($redis, $db, $devarr['devtype_id']);
                            $bedtemp['type'] = intval($devtypearr['type']);
                        }
                        $orderobj['bed'] = $bedtemp;
                    } else {
                        $orderobj['bed'] = new TheEmptyObj();
                    }
                    // 获取储物柜订单
                    $cabinetorder = BikeUtil::cabinetorder_from_redis($redis, $db, $uid);
                    if(!empty($cabinetorder)) {
                        $cabinettemp['order_no'] = $cabinetorder['order_no'];
                        $cabinettemp['deviceId'] = $cabinetorder['dev_id'];
                        $devarr = BikeUtil::device_info_from_redis($redis, $db, $cabinetorder['dev_id']);
                        $cabinettemp['mac'] = $devarr['mac'];
                        $cabinettemp['blekey'] = $devarr['blekey'];
                        if($devarr['devtype_id']==0) {
                            $cabinettemp['type'] = 1;
                        } else {
                            $devtypearr = BikeUtil::dev_type_from_redis($redis, $db, $devarr['devtype_id']);
                            $cabinettemp['type'] = intval($devtypearr['type']);
                        }
                        $orderobj['cabinet'] = $cabinettemp;
                    } else {
                        $orderobj['cabinet'] = new TheEmptyObj();
                    }
                    // 成功返回值
                    $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, BikeConstant::AUTHENCODE_PASS_REASON, BikeUtil::format_user_array($userarr, $oautharr, $orderobj));
                }
            }
        } catch(Exception $e) {
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $e->getMessage());
        }
        // 返回值
        return $array;
    }
    
    /**
     * 发送验证码
     */
    public static function vericode($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        // 参数
        $access_token = $request['access_token'];
        $phone = $request['phone'];
        try {
            // token过期校验
            $uid = BikeUtil::access_token_index_to_uid_from_redis($redis, $db, $access_token);
            if($uid==0) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token校验失败");
            $tokenarr = BikeUtil::access_token_from_redis($redis, $db, $uid);
            if($tokenarr['expire_in']>0 && $tokenarr['expire_in']<(time()-$tokenarr['createtime'])) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token已过期");
            $userarr = BikeUtil::userinfo_from_redis($redis, $db, $uid);
            // 注册场景下，检查手机是否已注册；忘记密码场景下，检查手机号是否注册过
            $chkuid = BikeUtil::phone_index_to_userid($redis, $db, $phone);
            if($chkuid>0) return BikeUtil::format_return_array(BikeConstant::Interface_User_Has_Register_Code, "手机号码已被绑定");
            // 参数，手机号和公司ID一起识别用户发送短信次数
            $cachephone = $phone;
            $iptimes = $redis -> hGet(BikeConstant::AIRPLUS_HASH_VERICODE_IP_LIMIT, $request['ip']);
            if($iptimes>=3) {
                var_dump(date("Y-m-d H:i:s") . " - 用户 $uid 使用" . $request['ip'] . "该IP地址触发发短信达到三次");
                return BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "短信发送成功");
            }
            $usertimes = $redis -> hGet(BikeConstant::AIRPLUS_HASH_VERICODE_USER_LIMIT, $uid);
            if($usertimes>=3) {
                var_dump(date("Y-m-d H:i:s") . " - 用户 $uid 触发发短信达到三次");
                return BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "短信发送成功");
            }
            // 生成验证码及短信内容
            $vericode = $redis -> get(BikeConstant::VERICODE_REDIS_PREFIX . $cachephone);
            if($vericode===false) {
                $vericode = BikeUtil::generate_auth_code();
            } else {
                // 三分钟内只能发送一次短信
                $timelimit = $redis -> get(BikeConstant::VERICODE_REDIS_TIMELIMIT . $cachephone);
                if($timelimit!=false) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, BikeConstant::VERICODE_TIMELIMIT_REASON);
                $vericode = BikeUtil::generate_auth_code();
            }
            // 发送短信逻辑
            $curtime = time();
            $veritemplate = "SMS_164513555";
            $msgparam['code'] = $vericode;
            $sendstat = $redis -> hGet(BikeConstant::AIRPLUS_HASH_VERICODE_SENDSTAT, $cachephone);
            if($sendstat==false) {
                $sendarr['times'] = 1;
                $sendarr['time'] = $curtime;
                $redis -> hSet(BikeConstant::AIRPLUS_HASH_VERICODE_SENDSTAT, $cachephone, json_encode($sendarr));
                $result = DaYuShortMsg::sendSms($phone, $veritemplate, $msgparam);
                $redis -> hIncrBy(BikeConstant::AIRPLUS_HASH_VERICODE_IP_LIMIT, $request['ip'], 1);
                $redis -> hIncrBy(BikeConstant::AIRPLUS_HASH_VERICODE_USER_LIMIT, $uid, 1);
            } else {
                $sendarr = json_decode($sendstat, true);
                if(($curtime-$sendarr['time'])<86400) {
                    if($sendarr['times']>=3) {
                        return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "验证码一天最多发送3次");
                    } else {
                        $sendarr['times'] = $sendarr['times'] + 1;
                        $redis -> hSet(BikeConstant::AIRPLUS_HASH_VERICODE_SENDSTAT, $cachephone, json_encode($sendarr));
                        $result = DaYuShortMsg::sendSms($phone, $veritemplate, $msgparam);
                        $redis -> hIncrBy(BikeConstant::AIRPLUS_HASH_VERICODE_IP_LIMIT, $request['ip'], 1);
                        $redis -> hIncrBy(BikeConstant::AIRPLUS_HASH_VERICODE_USER_LIMIT, $uid, 1);
                    }
                } else {
                    $sendarr['times'] = 1;
                    $sendarr['time'] = $curtime;
                    $redis -> hSet(BikeConstant::AIRPLUS_HASH_VERICODE_SENDSTAT, $cachephone, json_encode($sendarr));
                    $result = DaYuShortMsg::sendSms($phone, $veritemplate, $msgparam);
                    $redis -> hIncrBy(BikeConstant::AIRPLUS_HASH_VERICODE_IP_LIMIT, $request['ip'], 1);
                    $redis -> hIncrBy(BikeConstant::AIRPLUS_HASH_VERICODE_USER_LIMIT, $uid, 1);
                }
            }
            // 短信发送结果判断
            $result = get_object_vars($result);
            if(key_exists('Code', $result) && $result['Code']!="OK") {
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $result['Message']);
            } else {
                $redis -> set(BikeConstant::VERICODE_REDIS_PREFIX . $cachephone, $vericode, 3600);
                $redis -> set(BikeConstant::VERICODE_REDIS_TIMELIMIT . $cachephone, 1, 180);
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, BikeConstant::VERICODE_PASS_REASON);
            }
        } catch(Exception $e) {
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $e->getMessage());
        }
        // 返回值
        return $array;
    }
    
    /**
     * 绑定手机号
     */
    public static function bindphone($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        // 参数
        $phone = $request['phone'];
        $postcode = trim($request["vericode"]);
        $access_token = $request['access_token'];
        try {
            // token过期校验
            $uid = BikeUtil::access_token_index_to_uid_from_redis($redis, $db, $access_token);
            if($uid==0) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token校验失败");
            $tokenarr = BikeUtil::access_token_from_redis($redis, $db, $uid);
            if($tokenarr['expire_in']>0 && $tokenarr['expire_in']<(time()-$tokenarr['createtime'])) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token已过期");
            $userarr = BikeUtil::userinfo_from_redis($redis, $db, $uid);
            // 短信验证码校验
            $cachecode = $redis -> get(BikeConstant::VERICODE_REDIS_PREFIX . $phone);
            if($cachecode===false) {
                return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, BikeConstant::AUTHENCODE_EXPIRED_REASON);
            } else if(strcmp($cachecode, $postcode)!=0) {
                return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, BikeConstant::AUTHENCODE_WRONG_REASON);
            }
            // 检测phone是否已被绑定
            $chkuid = BikeUtil::phone_index_to_userid($redis, $db, $phone);
            if($chkuid>0) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, BikeConstant::USER_HAS_REGISTER_MSG);
            // 获取用户信息
            $userarr['phone'] = $save['phone'] = $phone;
            $cond['id'] = $uid;
            // 数据写DB
            $usertemp = $db->update(BikeUtil::table_full_name(BikeConstant::TABLE_USERS))->cols($save)->where("id=:id")->bindValues($cond)->query();
            if($usertemp==null) {
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, BikeConstant::PHONE_BINDED_ERROR_MSG);
            } else {
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, BikeConstant::PHONE_BINDED_PASS_MSG, $save);
                // 更新redis
                $redis->del(BikeConstant::VERICODE_REDIS_PREFIX . $phone);
                $redis->hSet(BikeConstant::AIRPLUS_HASH_PHONE_INDEX_TO_UID, $phone, $uid);
                $redis->hSet(BikeConstant::AIRPLUS_HASH_USERINFO, $uid, json_encode($userarr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
            }
        } catch(Exception $e) {
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $e->getMessage());
        }
        // 返回结果
        return $array;
    }
    
    /**
     * 绑定手机号
     */
    public static function wxbindphone($request)
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
            $userarr = BikeUtil::userinfo_from_redis($redis, $db, $uid);
            // 解析出手机号
            if(key_exists('code', $request)) {  // 微信授权登录状态已过期重新登录
                $openarr = BikeUtil::getopenid(BikeConstant::AIRPLUS_WXAPP_APPID, BikeConstant::AIRPLUS_WXAPP_APPSECRET, $request['code']);
                if(empty($openarr)) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "获取秘钥失败");
                $phonearr = BikeUtil::wxBizDataDecrypt(BikeConstant::AIRPLUS_WXAPP_APPID, $openarr['session_key'], $request['encryptedData'], $request['iv']);
            } else {    // 微信授权登录状态未过期
                $sessionkey = $redis->hGet(BikeConstant::AIRPLUS_HASH_OPENID_INDEX_TO_SESSION_KEY, $userarr['openid']);
                if($sessionkey==false) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "获取缓存秘钥失败");
                $phonearr = BikeUtil::wxBizDataDecrypt(BikeConstant::AIRPLUS_WXAPP_APPID, $sessionkey, $request['encryptedData'], $request['iv']);
            }
            $phone = $phonearr['purePhoneNumber'];
            // 通过phone识别用户是否已注册
            $chkuid = BikeUtil::phone_index_to_userid($redis, $db, $phone);
            if($chkuid>0) {
                if($chkuid==$uid) {
                    return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "您已绑定该手机号");
                } else {
                    return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "手机号已被其他用户绑定");
                }
            }
            // 获取用户信息
            $userarr['phone'] = $save['phone'] = $phone;
            $cond['id'] = $uid;
            // 数据写DB
            $temp = $db->update(BikeUtil::table_full_name(BikeConstant::TABLE_USERS))->cols($save)->where("id=:id")->bindValues($cond)->query();
            if($temp==null) {
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "手机绑定失败");
            } else {
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "手机绑定成功", $phone);
                // 更新redis
                $redis->hSet(BikeConstant::AIRPLUS_HASH_PHONE_INDEX_TO_UID, $phone, $uid);
                $redis->hSet(BikeConstant::AIRPLUS_HASH_USERINFO, $uid, json_encode($userarr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
            }
        } catch(Exception $e) {
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $e->getMessage());
        }
        // 返回结果
        return $array;
    }
    
    public static function updateinfo($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        // 参数
        $access_token = $request['access_token'];
        $phone = $request['phone'];
        try {
            // token过期校验
            $uid = BikeUtil::access_token_index_to_uid_from_redis($redis, $db, $access_token);
            if($uid==0) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token校验失败");
            $tokenarr = BikeUtil::access_token_from_redis($redis, $db, $uid);
            if($tokenarr['expire_in']>0 && $tokenarr['expire_in']<(time()-$tokenarr['createtime'])) return BikeUtil::format_return_array(BikeConstant::Interface_Reauth_Code, "token已过期");
            // 注册场景下，检查手机是否已注册；忘记密码场景下，检查手机号是否注册过
            $userarr = BikeUtil::userinfo_from_redis($redis, $db, $uid);
            $save = array();
            if(!empty($request["wxgender"])) $userarr['wxgender'] = $save['wxgender'] = intval($request["wxgender"]);
            if(!empty($request["brand"])) $userarr['brand'] = $save['brand'] = $request["brand"];
            if(!empty($request["model"])) $userarr['model'] = $save['model'] = $request["model"];
            if(!empty($request["city"])) $userarr['city'] = $save['city'] = $request["city"];
            if(!empty($request["province"])) $userarr['province'] = $save['province'] = $request["province"];
            if(!empty($request["nickname"])) $userarr['nickname'] = $save['nickname'] = $request["nickname"];
            if(!empty($request["head_image"])) {
                $userarr['head_image'] = $save['head_image'] = $request["head_image"];
                $localpath = self::file_exists_S3($request["domain"], $request["head_image"], $uid);
                if($localpath!="") $userarr['head_image'] = $save['head_image'] = $localpath;
            }
            if(!empty($save)) {
                $cond['id'] = $uid;
                $temp = $db->update(BikeUtil::table_full_name(BikeConstant::TABLE_USERS))->cols($save)->where("id=:id")->bindValues($cond)->query();
                if($temp==null) {
                    $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "信息更新失败");
                } else {
                    $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "信息更新成功");
                }
            } else {
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "更新信息为空");
            }
        } catch(Exception $e) {
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $e->getMessage());
        }
        // 返回值
        return $array;
    }

    /**
     * 退出登录
     * 
     * @param array $request
     * @return json
     */
    public static function logout($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        // 参数
        $access_token = $request['access_token'];
        try {
            // token过期校验
            $uid = BikeUtil::access_token_index_to_uid_from_redis($redis, $db, $access_token);
            if($uid>0) {
                $redis->hDel(BikeConstant::AIRPLUS_HASH_ACCESS_TOKEN, $uid);
                $redis->hDel(BikeConstant::AIRPLUS_HASH_ACCESS_TOKEN_TO_UID, $access_token);
            }
            // 成功返回值
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, BikeConstant::LOGOUT_PASS_MSG);
        } catch(Exception $e) {
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $e->getMessage());
        }
        // 返回结果
        return $array;
    }
    
    private static function file_exists_S3($domain, $url, $uid)
    {
        $state = @file_get_contents($url,0,null,0,1);//获取网络资源的字符内容
        if($state) {
            $folderpath = substr(__DIR__, 0, strpos(__DIR__, "workerman"))."public/uploads/".$uid."/";
            if(!is_dir($folderpath)) {
                mkdir($folderpath, 0777, true);
            }
            $urlpath = $domain."/uploads/".$uid."/";;
            $filename = "headimg_".date("YmdHis").".jpg";   //文件名称生成
            ob_start();//打开输出
            readfile($url);//输出图片文件
            $img = ob_get_contents();//得到浏览器输出
            ob_end_clean();//清除输出并关闭
            //$size = strlen($img);//得到图片大小
            $fp2 = @fopen($folderpath.$filename, "a");
            $result = fwrite($fp2, $img); //向用户图片目录写入图片文件，并重新命名
            fclose($fp2);
            if($result!=false) {
                return $urlpath.$filename;
            }
        }
        return "";
    }
    
    public static function clearusercache($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        $tokenarr = BikeUtil::access_token_from_redis($redis, $db, $request['id']);
        if(!empty($tokenarr)) $redis -> hDel(BikeConstant::AIRPLUS_HASH_ACCESS_TOKEN_TO_UID, $tokenarr['access_token']);
        $redis -> hDel(BikeConstant::AIRPLUS_HASH_ACCESS_TOKEN, $request['id']);
        $redis -> hDel(BikeConstant::AIRPLUS_HASH_PHONE_INDEX_TO_UID, $request['phone']);
        $redis -> hDel(BikeConstant::AIRPLUS_HASH_OPENID_INDEX_TO_UID, $request['openid']);
        $redis -> hDel(BikeConstant::AIRPLUS_HASH_USERINFO, $request['id']);
        return true;
    }
    
}
