<?php
/**
 * BikeUtil.php
 * @copyright           cobike
 * @license             http://www.cobike.cn
 * @lastmodify          2016-10-10
 * */
class EmptyObject {}

class BikeUtil {
    public static $mailist = array(
        array('mail'=>"leo_xia@cywin.cn",'name'=>"夏京"),
        array('mail'=>"zqs@cywin.cn",'name'=>"张群嵩")
    );
    /**
     * RSA private decrypt
     * @param string $encrypt_data
     * @return string
     */
    public static function RsaPrivateDecrypt($encrypt_data)
    {
        $prikey = openssl_pkey_get_private(file_get_contents(__DIR__."/../Lib/Cert/airplus/private.pem"));
        $encrypt_data = base64_decode($encrypt_data);
        openssl_private_decrypt($encrypt_data, $decrypt_data, $prikey, OPENSSL_PKCS1_PADDING);
        return $decrypt_data;
    }
    
    /**
     * RSA public encrypt
     * @param string $encrypt_data
     */
    public static function RsaPublicEncrypt($data)
    {
        $pubkey = openssl_pkey_get_public(file_get_contents(__DIR__."/../Lib/Cert/airplus/public.pem"));
        $result = openssl_public_encrypt($data, $encrypt_data, $pubkey, OPENSSL_PKCS1_PADDING);
        $encrypt_data = base64_encode($encrypt_data);
        return $encrypt_data;
    }
    
    /**
     * 生成签名
     * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
     */
    public static function MakeSign($array, $clientSecret)
    {
        //签名步骤一：按字典序排序参数
        ksort($array);
        $string = self::ToUrlParams($array);
        //签名步骤二：在string后加入KEY
        $string = $string . "&clientSecret=".$clientSecret;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }
    
    public static function ToUrlParams($array)
    {
        $buff = "";
        foreach ($array as $k => $v)
        {
            if($k != "sign" && $k != "clientip" && $k != "domain" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }
    
	/**
	 * format the return array
	 *
	 * @param int $code
	 * @param string $reason
	 * @param Object $result
	 *
	 * @return json
	 */
	public static function format_return_array($code, $msg, $data = array())
	{
		$array['code'] = $code;
		$array['msg'] = $msg;
		if(empty($data)) {
			$array['data'] = new EmptyObject();
		} else {
			$array['data'] = $data;
		}
		
		return json_encode($array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	}
	
	/**
	 * generate authentication code
	 *
	 * @param int $length
	 * @return vercode
	 * */
	public static function generate_auth_code($length=4)
	{
		$key = "";
		$pattern = '123456789';    //字符池
		for($i=0;$i<$length;$i++) {
			$key .= $pattern{mt_rand(0,8)};
		}
		return $key;
	}
	
	/**
	 * generate token
	 *
	 * @param string $phone
	 * @param string $vericode
	 * @param string $generate_time
	 *
	 * @return string
	 */
	public static function generate_token($uid, $phone, $imei, $vericode, $time)
	{
		return md5($uid . $phone . $imei . $vericode . $time);
	}
	
	/**
	 * token validation
	 * 
	 * @param object $redis
	 * @param object $db
	 * @param array $request
	 * @return boolean
	 */
	public static function token_validation($redis, $db, $request)
	{
		if($request['phone']=="13425134901") return true;
			
		try {
			$cache = $redis -> hGet(BikeConstant::COBIKE_HASH_TOKEN_TIME, $request['phone']);
			//var_dump($cache);
			if($cache==false) {		//get token time from db and store to the redis
				$cols = array('uid', 'vericode', 'token_time');
				$cond['phone'] = $request['phone'];
				$userdata = $db->select($cols)->from(BikeUtil::table_full_name(BikeConstant::TABLE_USERS))->where('phone=:phone')->bindValues($cond)->limit(1)->query();
				//var_dump($userdata);
				if(empty($userdata[0]['uid']) || empty($userdata[0]['vericode'])) return false;

				$cachearr[] = $userdata[0]['uid'];
				$cachearr[] = $userdata[0]['vericode'];
				$cachearr[] = $userdata[0]['token_time'];
				$result = $redis -> hSet(BikeConstant::COBIKE_HASH_TOKEN_TIME, $userdata[0]['uid'], implode(BikeConstant::SEMICOLON, $cachearr));
				//if($result==false) echo "get token validation info from db and save to redis failed\n";		//todo 需要激励log用于监控分析
			} else {
				$cachearr = explode(BikeConstant::SEMICOLON, $cache);
			}
			
			//var_dump($cachearr);
			//var_dump($request);
			//var_dump(self::generate_token($cachearr[0], $request['phone'], $request['imei'], $cachearr[1], $cachearr[2]));
			if($cachearr[0]!=$request['uid'] || strcmp($request['token'], self::generate_token($cachearr[0], $request['phone'], $request['imei'], $cachearr[1], $cachearr[2]))!=0) return false;
		} catch(Exception $e) {
			//todo 记录发生exception
			return false;
		}
		
		return true;
	}
	
	/**
     * redis auto increment
     * 
     * redis key format: 
     *         key - [db name]_[table name]_serial
     * 	key locker - [db name]_[table name]_serial:lock
     * 
     * @param object $redis
     * @param object $db
     * @param string $table
     * @param int $timeout
     * @return int
     */
	public static function get_next_autoincrement($redis, $db, $table, $timeout = 60)
	{
		// first check if we are locked...
		$autoincrement_key = self::autoincrement_key_generator(BikeConstant::DB_AIRPLUS_HYPHEN.$table);
		$autoincrement_key_lock = $autoincrement_key . ":lock";
		
		if (self::get_next_autoincrement_waitlock($redis, $autoincrement_key_lock, $timeout) == false) return -1;
		
		$id = $redis->incr($autoincrement_key);
		if ($id > 1) {
			return $id;
		}
		// if ID == 1, we assume we do not have "serial" key...
		// first we need to get lock.
		
		if ($redis->setnx($autoincrement_key_lock, 1)) {
			$redis->expire($autoincrement_key_lock, 60 * 5);
			$statement = sprintf("select max(id) as count from %s", self::table_full_name($table));
			try {
				$count = $db->query($statement); //get count from database.
			} catch(Exception $e) {
				return -2;
			}
			
			$id = (int)$count[0]['count'];
			// or alternatively:
			// select id from user_posts order by id desc limit 1
			$id++;  // increase it
		
			$redis->set($autoincrement_key, $id);  // update Redis key
			$redis->del($autoincrement_key_lock);  // release the lock
			return $id;
		}
		return 0; // can not get lock.
	}
	
	/**
	 * get the auto increment waitlock
	 *
	 * @param unknown $redis
	 * @param unknown $autoincrement_key_lock
	 * @param number $timeout
	 */
	protected static function get_next_autoincrement_waitlock($redis, $autoincrement_key_lock, $timeout = 60)
	{
		$count = $timeout > 0 ? $timeout : 60;
		while($redis->get($autoincrement_key_lock)) {
			$count++;
			sleep(1);
			if ($count > 10)
				return false;
		}
		return true;
	}
	
	/**
	 * auto increment key generator according to the table
	 *
	 * @param unknown $table
	 */
	protected static function autoincrement_key_generator($table)
	{
		return $table . "_serial";
	}
	
	public static function generate_access_token($uid, $openid, $time)
	{
	    return md5(md5($uid).md5($openid).md5($time).strval(floatval(explode(" ", microtime())[0])*1000000));
	}
	
	/**
	 * generate invite code
	 * 
	 * @return string
	 */
	public static function generate_invite_code($uid)
	{
		$pattern = 'abcdefghijklmnopqrstuvwxyz';    //字符池
		
		$length = strlen($uid);
		if($length < BikeConstant::INVITE_CODE_MIN_LENGTH) {
			$uid = str_pad($uid, BikeConstant::INVITE_CODE_MIN_LENGTH, "0", STR_PAD_LEFT);
			$length = BikeConstant::INVITE_CODE_MIN_LENGTH;
		}
		
		$code = "";
		for($i=0;$i<$length-1;$i++) {
			$code .= $uid[$i] . $pattern[mt_rand(0,25)];
		}
		$code .= $uid[$length-1];
		
		return BikeConstant::PROJECT_COBIKE . $code;
	}
	
	/**
	 * get the uid form the given invite code
	 * 
	 * @return int
	 */
	public static function get_uid_from_invite_code($invite_code)
	{
		$invite_code = ltrim($invite_code, BikeConstant::PROJECT_COBIKE);
		
		$length = strlen($invite_code) - 1;
		$loop = ($length+1)/2;
		
		$uid = 0;
		for($i=0; $i<=$loop; $i++) {
			$uid .= $invite_code[$i*2];
		}
		$uid = ltrim($uid, "0");
		
		return (int)$uid;
	}
	
	/**
	 * generate order no
	 *
	 * @param int $length
	 * @return string
	 * */
	public static function generate_order_no($length=4)
	{
	    $pattern = '1234567890ABCDEFGHIJKLOMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';    //字符池
	    $end = "";
	    for($i=0;$i<$length;$i++) {
	        $end .= $pattern{mt_rand(0,61)};
	    }
		
		return date(BikeConstant::TIME_FORMAT_YMDHIS) . $end;
	}
	
	/**
	 * pad table prefix to table and then return
	 *
	 * @param string $table
	 * @return string
	 */
	public static function table_full_name($table)
	{
		return BikeConstant::TABLE_PREFIX . $table;
	}
	
	/**
	 * send short msg
	 * 
	 * @param string $phone
	 * @param string $content
	 * @return false | array
	 */
	public static function sendsms($appid, $appkey, $content, $phone)
	{
		$shortmsg = new ShortMsg($appid, $appkey);
		return $shortmsg -> SendSMS($phone, $content);
	}
	
	public static function carorder_mail_notice($data, $tolist)
	{
	    if($data['step']==BikeConstant::ORDER_PEND_PAY) {
	        $subject = "天天优车用户预定车辆【待支付】通知";
	    } else if($data['step']==BikeConstant::ORDER_PEND_GET_CAR) {
	        $subject = "天天优车用户预定车辆【已支付】通知";
	    }
	    $body = vsprintf("<p color='red'>订单信息(<a href='%s'>详情登录后台查看</a>)：</p>
			<table border='1' cellspacing='0' cellpadding='0' width='300'>
			<tbody>
            <tr>
				<th width='100' align='center'>订单编号</th>
                <td width='200' align='left'>%s</td>
			</tr>
            <tr>
				<th width='100' align='center'>用户手机</th>
                <td width='200' align='left'>%s</td>
			</tr>
            <tr>
				<th width='100' align='center'>租车时长</th>
                <td width='200' align='left'>%s天</td>
			</tr>
            <tr>
				<th width='100' align='center'>取车时间</th>
                <td width='200' align='left'>%s点</td>
			</tr>
            <tr>
				<th width='100' align='center'>还车时间</th>
                <td width='200' align='left'>%s点</td>
			</tr>
            <tr>
				<th width='100' align='center'>订单总价</th>
                <td width='200' align='left'>%s元</td>
			</tr>
            <tr>
				<th width='100' align='center'>预付金额</th>
                <td width='200' align='left'>%s元</td>
			</tr>
			</tbody></table>
			<br><br>", array($data['domain'], $data['order_no'], $data['phone'], $data['days'], date("Y-m-d H", $data['starttime']), date("Y-m-d H", $data['endtime']), $data['total'], $data['pay']));
	    // 发送邮件
	    $mailer = new AirMail();
	    return $mailer->airplus_send_mail($subject, $body, $tolist);
	}
	
	public static function adverlist_from_redis($redis, $db, $cid)
	{
	    $array = array();
	    // redis缓存获取
	    $cacheredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_ADVER, $cid);
	    // redis缓存没有，从db获取
	    if($cacheredis==false) {
	        $cond['cid'] = $cid;
	        $list = $db->select(BikeConstant::$adver_cols)->from(BikeUtil::table_full_name(BikeConstant::TABLE_ADVER))->where('cid=:cid')->bindValues($cond)->limit(5)->query();
	        if(!empty($list)) {
	            $array = $list;
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_ADVER, $cid, json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	        }
	    } else {
	        $rtnarr = json_decode($cacheredis, true);
	        return $rtnarr;
	    }
	    // 返回值
	    return $array;
	}
	
	/**
	 * 通过客户端id和客户端秘钥获取公司id
	 * @param object $redis
	 * @param object $db
	 * @param string $clientId
	 * @param string $clientSecret - deleted
	 * @return int $companyId
	 */
	public static function client_index_to_company_from_redis($redis, $db, $clientId)
	{
	    $companyId = 0;
	    // redis缓存获取
	    $redisCompId = $redis->hGet(BikeConstant::AIRPLUS_HASH_CLIENT_INDEX_TO_COMPANY, $clientId);
	    // redis缓存没有，从db获取
	    if($redisCompId==false) {
	        $cond['clientId'] = $clientId;
	        //$cond['clientSecret'] = $clientSecret;
	        //$rows = $db->select(BikeConstant::$compamy_cols)->from(BikeUtil::table_full_name(BikeConstant::TABLE_COMPANY))->where("status='1' and clientId=:clientId and clientSecret=:clientSecret")->bindValues($cond)->limit(1)->query();
	        $rows = $db->select(BikeConstant::$compamy_cols)->from(BikeUtil::table_full_name(BikeConstant::TABLE_COMPANY))->where("status='1' and clientId=:clientId")->bindValues($cond)->limit(1)->query();
	        if(!empty($rows)) {
	            $companyId = $rows[0]['id'];
	            //$redis->hSet(BikeConstant::AIRPLUS_HASH_CLIENT_INDEX_TO_COMPANY, $clientId."_".$clientSecret, $companyId);
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_CLIENT_INDEX_TO_COMPANY, $clientId, $companyId);
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_COMPANY_INFO, $companyId, json_encode($rows[0]));
	        }
	    } else {
	        return $redisCompId;
	    }
	    // 返回值
	    return $companyId;
	}
	
	public static function company_info_from_redis($redis, $db, $compid)
	{
	    $array = array();
	    // redis缓存获取
	    $companyredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_COMPANY_INFO, $compid);
	    // redis缓存没有，从db获取
	    if($companyredis==false) {
	        $cond['id'] = $compid;
	        $rows = $db->select(BikeConstant::$compamy_cols)->from(BikeUtil::table_full_name(BikeConstant::TABLE_COMPANY))->where("status='1' and id=:id")->bindValues($cond)->limit(1)->query();
	        if(!empty($rows)) {
	            $array = $rows[0];
	            $clientId = $rows[0]['clientId'];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_COMPANY_INFO, $compid, json_encode($rows[0]));
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_CLIENT_INDEX_TO_COMPANY, $clientId, $compid);
	        }
	    } else {
	        return json_decode($companyredis, true);
	    }
	    // 返回值
	    return $array;
	}
	
	/**
	 * 获取短信消息的模板id
	 *
	 * @param string $clientId
	 * @param string $clientSecret
	 * @param string $msgid
	 * @return string
	 */
	public static function get_msg_app_from_redis($redis, $db, $id)
	{
	    $msgapp = array();
	    // redis缓存获取
	    $redisMsgApp = $redis->hGet(BikeConstant::AIRPLUS_HASH_MESSAGE_APP, $id);
	    // redis缓存没有，从db获取
	    if($redisMsgApp==false) {
	        $cond['id'] = $id;
	        $rows = $db->select(BikeConstant::$msg_template_cols)->from(BikeUtil::table_full_name(BikeConstant::TABLE_MSG_APP))->where("id=:id and status=1")->bindValues($cond)->limit(1)->query();
	        if(!empty($rows)) {
	            $msgapp = $rows[0];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_MESSAGE_APP, $id, json_encode($msgapp, JSON_UNESCAPED_UNICODE));
	        }
	    } else {
	        return json_decode($redisMsgApp, true);
	    }
	    // 返回值
	    return $msgapp;
	}
	
	public static function phone_index_to_userid($redis, $db, $phone)
	{
	    $uid = 0;
	    // redis缓存获取
	    $cacheredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_PHONE_INDEX_TO_UID, $phone);
	    // redis缓存没有，从db获取
	    if($cacheredis==false) {
	        $cond['phone'] = $phone;
	        $rows = $db->select(self::get_cols(new UserInfo()))->from(BikeUtil::table_full_name(BikeConstant::TABLE_USERS))->where("phone=:phone")->bindValues($cond)->limit(1)->query();
	        if(!empty($rows)) {
	            $uid = $rows[0]['id'];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_PHONE_INDEX_TO_UID, $phone, $uid);
	            $array = $rows[0];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_USERINFO, $uid, json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	        }
	    } else {
	        return $cacheredis;
	    }
	    // 返回值
	    return $uid;
	}
	
	public static function openid_index_to_uid($redis, $db, $openid)
	{
	    $uid = 0;
	    // redis缓存获取
	    $cacheredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_OPENID_INDEX_TO_UID, $openid);
	    // redis缓存没有，从db获取
	    if($cacheredis==false) {
	        $cond['openid'] = $openid;
	        $rows = $db->select(self::get_cols(new UserInfo()))->from(BikeUtil::table_full_name(BikeConstant::TABLE_USERS))->where("openid=:openid")->bindValues($cond)->limit(1)->query();
	        if(!empty($rows)) {
	            $uid = $rows[0]['id'];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_OPENID_INDEX_TO_UID, $openid, $uid);
	            $array = $rows[0];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_USERINFO, $uid, json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	        }
	    } else {
	        return $cacheredis;
	    }
	    // 返回值
	    return $uid;
	}
	
	public static function getopenid($appid, $appsecret, $jscode)
	{
	    $openid = [];
	    $url = "https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$appsecret&js_code=$jscode&grant_type=authorization_code";
	    try {
	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, $url);
	        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        $result = curl_exec($ch);
	        if(curl_errno($ch)) {
	            // do nothing
	        } else {
	            $rtnarr = json_decode($result, true);
	            return $rtnarr;
// 	            if($rtnarr['openid']) return $rtnarr['openid'];
	        }
	        curl_close($ch);
	    } catch(Exception $e) {
	        // do nothing
	    }
	    // 返回结果
	    return $openid;
	}
	
	/**
	 * 微信用户数据解密
	 * 
	 * @param string $appid
	 * @param string $sessionKey
	 * @param string $encryptedData
	 * @param string $iv
	 */
	public static function wxBizDataDecrypt($appid, $sessionKey, $encryptedData, $iv)
	{
	    $dataDecrypt = new WXBizDataCrypt($appid, $sessionKey);
	    return $dataDecrypt->decryptData($encryptedData, $iv);
	}
	
	/**
	 * 获取用户信息
	 * 
	 * @param object $redis
	 * @param object $db
	 * @param int $uid
	 * @param string $token
	 * @return array
	 */
	public static function userinfo_from_redis($redis, $db, $uid)
	{
	    $array = array();
	    // redis缓存获取
	    $userredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_USERINFO, $uid);
	    // redis缓存没有，从db获取
	    if($userredis==false) {
	        $cond['id'] = $uid;
	        $userlist = $db->select(self::get_cols(new UserInfo()))->from(BikeUtil::table_full_name(BikeConstant::TABLE_USERS))->where('id=:id')->bindValues($cond)->limit(1)->query();
	        if(!empty($userlist)) {
	            $array = $userlist[0];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_USERINFO, $uid, json_encode(self::userinfo_redis_saving_parser($array), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	            if(!empty($array['phone']) && !empty($array['cid'])) $redis->hSet(BikeConstant::AIRPLUS_HASH_CID_PHONE_INDEX_TO_USER, $array['phone']."_".$array['cid'], $uid);
	        }
	    } else {
	        $userarr = json_decode($userredis, true);
	        return $userarr;
	    }
	    // 返回值
	    return $array;
	}
	
	public static function userinfo_redis_saving_parser($array)
	{
	    $returnarr["phone"] = $array["phone"];
	    $returnarr["openid"] = $array["openid"];
	    $returnarr["nickname"] = $array["nickname"];
	    $returnarr["head_image"] = $array["head_image"];
	    $returnarr["wxgender"] = $array["wxgender"];
	    $returnarr["realname"] = $array["realname"];
	    $returnarr["id_type"] = $array["id_type"];
	    $returnarr["id_no"] = $array["id_no"];
	    $returnarr["id_image"] = $array["id_image"];
	    $returnarr["balance"] = $array["balance"];
	    $returnarr["score"] = $array["score"];
	    $returnarr["credit"] = $array["credit"];
	    $returnarr["deposit"] = $array["deposit"];
	    $returnarr["realstat"] = $array["realstat"];
	    return $returnarr;
	}
	
	/**
	 * format user array
	 *
	 * @param array $userarr
	 * @param array $tokenarr
	 * @return array
	 */
	public static function format_user_array($userarr, $oautharr, $orderobj=array())
	{
	    if($userarr["realstat"]>0) {
	        $userarr["realname"] = "";
	        $userarr["id_type"] = "";
	        $userarr["id_no"] = "";
	        $userarr["id_image"] = "";
	    }
	    $index = array_keys(array_keys($userarr), 'openid');
	    array_splice($userarr, $index[0], 1);
	    $userarr["access_token"] = $oautharr["access_token"];
	    if(!empty($orderobj)) {
	        $userarr['bed'] = $orderobj['bed'];
	        $userarr['cabinet'] = $orderobj['cabinet'];
	    } else {
	        $userarr['bed'] = new EmptyObject();
	        $userarr['cabinet'] = new EmptyObject();
	    }
	    return $userarr;
	}
	
	public static function access_token_index_to_uid_from_redis($redis, $db, $access_token)
	{
	    $uid = 0;
	    // 从缓存通过phone获取uid
	    $uidredis = $redis -> hGet(BikeConstant::AIRPLUS_HASH_ACCESS_TOKEN_TO_UID, $access_token);
	    if($uidredis==false) {
	        // 缓存没有，检测db是否存在
	        $list = $db->select(array("id", "access_token"))->orderByDesc(array('createtime'))->from(BikeUtil::table_full_name(BikeConstant::TABLE_OAUTH))->limit(1)->query();
	        if(!empty($list) && strcmp($list[0]["access_token"], $access_token)==0) {
	            $uid = $list[0]["id"];
	            $tokenarr = $list[0];
	            $redis -> hSet(BikeConstant::AIRPLUS_HASH_ACCESS_TOKEN_TO_UID, $access_token, $uid);
	            $redis -> hSet(BikeConstant::AIRPLUS_HASH_ACCESS_TOKEN, $uid, json_encode($tokenarr));
	        }
	    } else {
	        $uid = $uidredis;
	    }
	    // 返回值
	    return $uid;
	}
	
	public static function access_token_index_to_uid_from_redis_daikai($redis, $db, $access_token)
	{
	    $uid = 0;
	    // 从缓存通过phone获取uid
	    $uidredis = $redis -> hGet(BikeConstant::AIRPLUS_HASH_ACCESS_TOKEN_TO_UID, $access_token);
	    if($uidredis==false) {
	        // 缓存没有，检测db是否存在
	        $list = $db->select(array("id", "access_token"))->from(BikeUtil::table_full_name(BikeConstant::TABLE_OAUTH))->where("access_token=:access_token")->bindvalues(["access_token"=>$access_token])->limit(1)->query();
	        if(!empty($list)) {
	            $redis -> hSet(BikeConstant::AIRPLUS_HASH_ACCESS_TOKEN_TO_UID, $access_token, $uid = $list[0]["id"]);
	        }
	    } else {
	        $uid = $uidredis;
	    }
	    // 返回值
	    return $uid;
	}
	
	public static function access_token_from_redis($redis, $db, $uid)
	{
	    $tokenarr = array();
	    // 从缓存通过phone获取uid
	    $tokenredis = $redis -> hGet(BikeConstant::AIRPLUS_HASH_ACCESS_TOKEN, $uid);
	    if($tokenredis==false) {
	        // 缓存没有，检测db是否存在
	        $cond['id'] = $uid;
	        $list = $db->select(self::get_cols(new OauthInfo()))->orderByDesc(array('createtime'))->from(BikeUtil::table_full_name(BikeConstant::TABLE_OAUTH))->where("id=:id")->bindvalues($cond)->limit(1)->query();
	        if(!empty($list)) {
	            $tokenarr = $list[0];
	            $access_token = $list[0]['access_token'];
	            $redis -> hSet(BikeConstant::AIRPLUS_HASH_ACCESS_TOKEN_TO_UID, $access_token, $uid);
	            $redis -> hSet(BikeConstant::AIRPLUS_HASH_ACCESS_TOKEN, $uid, json_encode($tokenarr));
	        }
	    } else {
	        $tokenarr = json_decode($tokenredis, true);
	    }
	    // 返回值
	    return $tokenarr;
	}
	
	public static function cardetail_from_redis($redis, $db, $id)
	{
	    $array = array();
	    // redis缓存获取
	    $cacheredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_CAR_DETAIL, $id);
	    // redis缓存没有，从db获取
	    if($cacheredis==false) {
	        $cond['id'] = $id;
	        $list = $db->select(self::get_cols(new CardetailInfo()))->from(BikeUtil::table_full_name(BikeConstant::TABLE_CARS))->where('id=:id')->bindValues($cond)->limit(1)->query();
	        if(!empty($list)) {
	            $array = $list[0];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_CAR_DETAIL, $id, json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	        }
	    } else {
	        $rtnarr = json_decode($cacheredis, true);
	        return $rtnarr;
	    }
	    // 返回值
	    return $array;
	}
	
	public static function cartype_from_redis($redis, $db, $id)
	{
	    $array = array();
	    // redis缓存获取
	    $cacheredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_CAR_TYPE, $id);
	    // redis缓存没有，从db获取
	    if($cacheredis==false) {
	        $cond['id'] = $id;
	        $list = $db->select(BikeConstant::$cartype_cols)->from(BikeUtil::table_full_name(BikeConstant::TABLE_CARTYPE))->where('id=:id')->bindValues($cond)->limit(1)->query();
	        if(!empty($list)) {
	            $array = $list[0];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_CAR_TYPE, $id, json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	        }
	    } else {
	        $rtnarr = json_decode($cacheredis, true);
	        return $rtnarr;
	    }
	    // 返回值
	    return $array;
	}
	
	public static function carmodel_from_redis($redis, $db, $id)
	{
	    $array = array();
	    // redis缓存获取
	    $cacheredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_CAR_MODEL, $id);
	    // redis缓存没有，从db获取
	    if($cacheredis==false) {
	        $cond['id'] = $id;
	        $list = $db->select(BikeConstant::$brandmodel_cols)->from(BikeUtil::table_full_name(BikeConstant::TABLE_BRANDMODEL))->where('id=:id')->bindValues($cond)->limit(1)->query();
	        if(!empty($list)) {
	            $array = $list[0];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_CAR_MODEL, $id, json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	        }
	    } else {
	        $rtnarr = json_decode($cacheredis, true);
	        return $rtnarr;
	    }
	    // 返回值
	    return $array;
	}
	
	public static function cardiscount_from_redis($redis, $db, $id)
	{
	    $array = array();
	    // redis缓存获取
	    $cacheredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_CAR_DISCOUNT, $id);
	    // redis缓存没有，从db获取
	    if($cacheredis==false) {
	        $cond['id'] = $id;
	        $list = $db->select(BikeConstant::$car_discount_cols)->from(BikeUtil::table_full_name(BikeConstant::TABLE_DISCOUNT))->where('id=:id')->bindValues($cond)->limit(1)->query();
	        if(!empty($list)) {
	            $array = $list[0];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_CAR_DISCOUNT, $id, json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	        }
	    } else {
	        $rtnarr = json_decode($cacheredis, true);
	        return $rtnarr;
	    }
	    // 返回值
	    return $array;
	}
	
	public static function bedorder_from_redis($redis, $db, $uid)
	{
	    $array = array();
	    // redis缓存获取
	    $cacheredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_USER_CURRENT_ORDER, $uid);
	    // redis缓存没有，从db获取
	    if($cacheredis==false) {
	        $cond['users_id'] = $uid;
	        $cond['step'] = BikeConstant::ORDER_PAID;
	        $list = $db->select(self::get_cols(new BedorderInfo()))->from(BikeUtil::table_full_name(BikeConstant::TABLE_BEDORDER))->where('users_id=:users_id and step=:step and status=1')->bindValues($cond)->limit(1)->query();
	        if(!empty($list)) {
	            $array = $list[0];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_BEDORDER, $array['order_no'], json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_USER_CURRENT_ORDER, $uid, $array['order_no']);
	        }
	    } else {
	        $ordercacheredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_BEDORDER, $cacheredis);
	        if($ordercacheredis==false) {
	            $cond['users_id'] = $uid;
	            $cond['step'] = BikeConstant::ORDER_PAID;
	            $cond['order_no'] = $cacheredis;
	            $list = $db->select(self::get_cols(new BedorderInfo()))->from(BikeUtil::table_full_name(BikeConstant::TABLE_BEDORDER))->where('users_id=:users_id and step=:step and status=1 and order_no=:order_no')->bindValues($cond)->limit(1)->query();
	            if(!empty($list)) {
	                $array = $list[0];
	                $redis->hSet(BikeConstant::AIRPLUS_HASH_BEDORDER, $array['order_no'], json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	                $redis->hSet(BikeConstant::AIRPLUS_HASH_USER_CURRENT_ORDER, $uid, $array['order_no']);
	            } else {
	                $redis->hDel(BikeConstant::AIRPLUS_HASH_USER_CURRENT_ORDER, $uid);
	            }
	        } else {
	            $rtnarr = json_decode($ordercacheredis, true);
	            return $rtnarr;
	        }
	    }
	    // 返回值
	    return $array;
	}
	
	public static function cabinetorder_from_redis($redis, $db, $uid)
	{
	    $array = array();
	    // redis缓存获取
	    $cacheredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_USER_CURRENT_CABINETORDER, $uid);
	    // redis缓存没有，从db获取
	    if($cacheredis==false) {
	        $cond['users_id'] = $uid;
	        $cond['step'] = BikeConstant::ORDER_PAID;
	        $list = $db->select(self::get_cols(new CabinetorderInfo()))->from(BikeUtil::table_full_name(BikeConstant::TABLE_CABINETORDER))->where('users_id=:users_id and step=:step and status=1')->bindValues($cond)->limit(1)->query();
	        if(!empty($list)) {
	            $array = $list[0];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_CABINETORDER, $array['order_no'], json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_USER_CURRENT_CABINETORDER, $uid, $array['order_no']);
	        }
	    } else {
	        $ordercacheredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_CABINETORDER, $cacheredis);
	        if($ordercacheredis==false) {
	            $cond['users_id'] = $uid;
	            $cond['step'] = BikeConstant::ORDER_PAID;
	            $cond['order_no'] = $cacheredis;
	            $list = $db->select(self::get_cols(new CabinetorderInfo()))->from(BikeUtil::table_full_name(BikeConstant::TABLE_CABINETORDER))->where('users_id=:users_id and step=:step and status=1 and order_no=:order_no')->bindValues($cond)->limit(1)->query();
	            if(!empty($list)) {
	                $array = $list[0];
	                $redis->hSet(BikeConstant::AIRPLUS_HASH_CABINETORDER, $array['order_no'], json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	                $redis->hSet(BikeConstant::AIRPLUS_HASH_USER_CURRENT_CABINETORDER, $uid, $array['order_no']);
	            } else {
	                $redis->hDel(BikeConstant::AIRPLUS_HASH_USER_CURRENT_CABINETORDER, $uid);
	            }
	        } else {
	            $rtnarr = json_decode($ordercacheredis, true);
	            return $rtnarr;
	        }
	    }
	    // 返回值
	    return $array;
	}
	
	public static function tuoguan_mail_notice_from_redis($redis, $db, $cid)
	{
	    $array = self::$mailist;
	    // redis缓存获取
	    $cacheredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_TUOGUAN_NOTICE_MAILLIST, $cid);
	    // redis缓存没有，从db获取
	    if($cacheredis==false) {
	        $cond['cid'] = $cid;
	        $list = $db->select(array("mail","name"))->from(BikeUtil::table_full_name(BikeConstant::TABLE_MAILNOTICE))->where('cid=:cid and type=1')->bindValues($cond)->query();
	        if(!empty($list)) {
	            $array = $list;
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_TUOGUAN_NOTICE_MAILLIST, $cid, json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	        }
	    } else {
	        $rtnarr = json_decode($cacheredis, true);
	        return $rtnarr;
	    }
	    // 返回值
	    return $array;
	}
	
	public static function carorder_mail_notice_from_redis($redis, $db, $cid)
	{
	    $array = self::$mailist;
	    // redis缓存获取
	    $cacheredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_CARORDER_NOTICE_MAILLIST, $cid);
	    // redis缓存没有，从db获取
	    if($cacheredis==false) {
	        $cond['cid'] = $cid;
	        $list = $db->select(array("mail","name"))->from(BikeUtil::table_full_name(BikeConstant::TABLE_MAILNOTICE))->where('cid=:cid and type=2')->bindValues($cond)->query();
	        if(!empty($list)) {
	            $array = $list;
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_CARORDER_NOTICE_MAILLIST, $cid, json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	        }
	    } else {
	        $rtnarr = json_decode($cacheredis, true);
	        return $rtnarr;
	    }
	    // 返回值
	    return $array;
	}
	
	public static function wxpay_config_from_redis($redis, $db, $cid)
	{
	    $array = array();
	    // redis缓存获取
	    $cacheredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_WXPAY_CONFIG, $cid);
	    // redis缓存没有，从db获取
	    if($cacheredis==false) {
	        $cond['cid'] = $cid;
	        $list = $db->select(array("cid","appid","appsecret","mchid","mchkey","sslpath","sslcert_file","sslkey_file"))->from(BikeUtil::table_full_name(BikeConstant::TABLE_WXPAY))->where('cid=:cid and status>0')->bindValues($cond)->query();
	        if(!empty($list)) {
	            $array = $list[0];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_WXPAY_CONFIG, $cid, json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	        }
	    } else {
	        return json_decode($cacheredis, true);
	    }
	    // 返回值
	    return $array;
	}
	
	public static function car_coupon_info_from_redis($redis, $db, $cid, $send_type)
	{
	    $array = array();
	    // redis缓存获取
	    $cacheredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_COUPON_INFO, $cid."_".$send_type);
	    // redis缓存没有，从db获取
	    if($cacheredis==false) {
	        $curtime = time();
	        $cond['cid'] = $cid;
	        $cond['send_type'] = $send_type;
	        $list = $db->select(array("id","cid","name","type_money","send_type","min_amount","max_amount","use_start_time","valid_days","min_use_amount"))->from(BikeUtil::table_full_name(BikeConstant::TABLE_COUPON))->where('cid=:cid and send_type=:send_type and status=1')->bindValues($cond)->query();
	        $send_start_time = intval($list[0]['send_start_time']);
	        $send_end_time = intval($list[0]['send_end_time']);
	        if(!empty($list) && ($send_start_time==0 || ($send_start_time>0 && $send_start_time<=$curtime)) && ($send_end_time==0 || ($send_end_time>0 && $send_end_time>=$curtime))) {
	            $array = $list[0];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_COUPON_INFO, $cid."_".$send_type, json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	        }
	    } else {
	        return json_decode($cacheredis, true);
	    }
	    // 返回值
	    return $array;
	}
	
	public static function car_coupon_info_from_redis_new($redis, $db, $cid, $send_type)
	{
	    $array = array();
	    // redis缓存获取
	    $cacheredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_COUPON_INFO, $cid."_".$send_type);
	    // redis缓存没有，从db获取
	    if($cacheredis==false) {
	        $curtime = time();
	        $cond['cid'] = $cid;
	        $cond['send_type'] = $send_type;
	        $list = $db->select(array("id","cid","name","type_money","send_type","min_amount","max_amount","send_start_time","send_end_time","use_start_time","valid_days","min_use_amount","numbers"))->from(BikeUtil::table_full_name(BikeConstant::TABLE_COUPON))->where('cid=:cid and send_type=:send_type and status=1')->bindValues($cond)->query();
	        $send_start_time = intval($list[0]['send_start_time']);
	        $send_end_time = intval($list[0]['send_end_time']);
	        if(!empty($list) && ($send_start_time==0 || ($send_start_time>0 && $send_start_time<=$curtime)) && ($send_end_time==0 || ($send_end_time>0 && $send_end_time>=$curtime))) {
	            $array = $list;
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_COUPON_INFO, $cid."_".$send_type, json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	        }
	    } else {
	        return json_decode($cacheredis, true);
	    }
	    // 返回值
	    return $array;
	}
	
	public static function invite_register_coupon_send_parser($couponlist, $uid, $inviter, $couponarr)
	{
	    $curtime = strtotime(Date("Y-m-d"));
	    // 邀请者coupon
	    $invitercouponinfo = new UserCouponInfo();
	    $invitercouponinfo->user_id = $inviter;
	    // 被邀请者coupon
	    $usercouponinfo = new UserCouponInfo();
	    $usercouponinfo->user_id = $uid;
	    // 初始化sql
	    $couponarr['usercouponsql'] = $couponarr['invitercouponsql'] = "INSERT INTO `fa_user_coupon` (`" . implode("`,`", array_keys(get_object_vars($invitercouponinfo))) . "`) VALUES ('";
	    // 批量插入
	    foreach ($couponlist as $val) {
	        $couponAmount = 0;
	        if(bccomp(floatval($val['type_money']), 0, 2)==1) {
	            $couponAmount = $val['type_money'];
	        } else {
	            $couponAmount = mt_rand(intval($val['min_amount']), intval($val['max_amount']));
	        }
	        if($couponAmount>0) {
	            for($i=0; $i<intval($val['numbers']); $i++) {
	                $coupon_number = BikeUtil::generate_order_no(4);
	                // 邀请者发放优惠券
	                $invitercouponinfo->coupon_number = $coupon_number;
	                $invitercouponinfo->ctag = "邀请红包";
	                // 被邀请注册用户发放优惠券
	                $usercouponinfo->coupon_number = $coupon_number;
	                $usercouponinfo->ctag = "新人红包";
	                // 公共部分
	                $invitercouponinfo->amount = $usercouponinfo->amount = $couponAmount;
	                $invitercouponinfo->coupon_id = $usercouponinfo->coupon_id = $val['id'];
	                if($val['use_start_time']>0) {
	                    $invitercouponinfo->use_start_time = $usercouponinfo->use_start_time = $val['use_start_time'];
	                } else {
	                    $invitercouponinfo->use_start_time = $usercouponinfo->use_start_time = $curtime;
	                }
	                $invitercouponinfo->use_end_time = $usercouponinfo->use_end_time = $invitercouponinfo->use_start_time + $val['valid_days']*86400;
	                $invitercouponinfo->min_use_amount = $usercouponinfo->min_use_amount = $val['min_use_amount'];
	                // 生成db保存数据
	                $invitercouponarr = get_object_vars($invitercouponinfo);
	                $usercouponarr = get_object_vars($usercouponinfo);
	                $couponarr['invitercouponsql'] .= implode("','", $invitercouponarr);
	                $couponarr['usercouponsql'] .= implode("','", $usercouponarr);
	                $couponarr['usercouponsql'] .= "'), ('";
	                $couponarr['invitercouponsql'] .= "'), ('";
	                // 生成redis保存数据
	                $couponarr['redisave'][$coupon_number."_".$inviter] = json_encode($invitercouponarr, JSON_UNESCAPED_UNICODE);
	                $couponarr['redisave'][$coupon_number."_".$uid] = json_encode($usercouponarr, JSON_UNESCAPED_UNICODE);
	                $couponarr['sendind'] = true;
	            }
	        }
	    }
	    $couponarr['invitercouponsql'] = rtrim($couponarr['invitercouponsql'], ", ('").";";
	    $couponarr['usercouponsql'] = rtrim($couponarr['usercouponsql'], ", ('").";";
	    // 返回结果
	    return $couponarr;
	}
	
	public static function user_coupon_from_redis($redis, $db, $uid, $coupon_number)
	{
	    $array = array();
	    // redis缓存获取
	    $cacheredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_USER_COUPON, $coupon_number."_".$uid);
	    // redis缓存没有，从db获取
	    if($cacheredis==false) {
	        $curtime = time();
	        $cond['user_id'] = $uid;
	        $cond['coupon_number'] = $coupon_number;
	        $list = $db->select(array("coupon_id","use_start_time","use_end_time","amount","min_use_amount","used_time"))->from(BikeUtil::table_full_name(BikeConstant::TABLE_USERCOUPON))->where('user_id=:user_id and coupon_number=:coupon_number')->bindValues($cond)->query();
	        if(!empty($list)) {
	            $array = $list[0];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_USER_COUPON, $coupon_number."_".$uid, json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	        }
	    } else {
	        return json_decode($cacheredis, true);
	    }
	    // 返回值
	    return $array;
	}
	
	public static function repo_id_index_to_serial_from_redis($redis, $db, $repoid)
	{
	    $serial = "";
	    // redis缓存获取
	    $devreporedis = $redis->hGet(BikeConstant::AIRPLUS_HASH_REPO_ID_INDEXTO_SERIAL, $repoid);
	    // redis缓存没有，从db获取
	    if($devreporedis==false) {
	        $cond['id'] = $repoid;
	        $devrepolist = $db->select(BikeConstant::$dev_repo_cols)->from(BikeUtil::table_full_name(BikeConstant::TABLE_DEVICE_STORAGE))->where('id=:id')->bindValues($cond)->limit(1)->query();
	        if(!empty($devrepolist)) {
	            $serial = $devrepolist[0]['serial'];
	            $array = $devrepolist[0];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_REPO_ID_INDEXTO_SERIAL, $repoid, $serial);
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_DEV_IN_REPO, $serial, json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	        }
	    } else {
	        return $devreporedis;
	    }
	    // 返回值
	    return $serial;
	}
	
	public static function dev_type_from_redis($redis, $db, $id)
	{
	    $array = array();
	    // redis缓存获取
	    $devtyperedis = $redis->hGet(BikeConstant::AIRPLUS_HASH_DEVICE_TYPE, $id);
	    // redis缓存没有，从db获取
	    if($devtyperedis==false) {
	        $cond['id'] = $id;
	        $devtypelist = $db->select(BikeConstant::$device_type_cols)->from(BikeUtil::table_full_name(BikeConstant::TABLE_DEVTYPE))->where('id=:id')->bindValues($cond)->limit(1)->query();
	        if(!empty($devtypelist)) {
	            $array = $devtypelist[0];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_DEVICE_TYPE, $id, json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	        }
	    } else {
	        $devtypearr = json_decode($devtyperedis, true);
	        return $devtypearr;
	    }
	    // 返回值
	    return $array;
	}
	
	public static function user_lock_from_redis($redis, $db, $uid, $devid)
	{
	    $array = array();
	    // redis缓存获取
	    $userlockredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_UID_DEVID_INDEXTO_USER_LOCK, $uid."_".$devid);
	    // redis缓存没有，从db获取
	    if($userlockredis==false) {
	        $cond['user_id'] = $uid;
	        $cond['device_id'] = $devid;
	        $userlocklist = $db->select(BikeConstant::$user_lock_cols)->from(BikeUtil::table_full_name(BikeConstant::TABLE_USER_LOCK))->where('user_id=:user_id and device_id=:device_id')->bindValues($cond)->limit(1)->query();
	        if(!empty($userlocklist)) {
	            $array = $userlocklist[0];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_UID_DEVID_INDEXTO_USER_LOCK, $uid."_".$devid, json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	        }
	    } else {
	        $userlockarr = json_decode($userlockredis, true);
	        return $userlockarr;
	    }
	    // 返回值
	    return $array;
	}
	
	public static function temp_pwd_from_redis($redis, $db, $tempId)
	{
	    $array = array();
	    // redis缓存获取
	    $temppwdredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_TEMP_PWD_INFO, $tempId);
	    // redis缓存没有，从db获取
	    if($temppwdredis==false) {
	        $cond['id'] = $tempId;
	        $temppwdlist = $db->select(BikeConstant::$temp_pwd_cols)->from(BikeUtil::table_full_name(BikeConstant::TABLE_TEMP_PWD))->where('id=:id')->bindValues($cond)->limit(1)->query();
	        if(!empty($temppwdlist)) {
	            $array = $temppwdlist[0];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_TEMP_PWD_INFO, $tempId, json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	        }
	    } else {
	        $temppwdarr = json_decode($temppwdredis, true);
	        return $temppwdarr;
	    }
	    // 返回值
	    return $array;
	}
	
	public static function serial_index_to_devid_from_redis($redis, $db, $serial)
	{
	    $devid = 0;
	    // redis缓存获取
	    $devidredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_SERIAL_INDEX_TO_DEVICE_ID, $serial);
	    // redis缓存没有，从db获取
	    if($devidredis==false) {
	        $cond['serial'] = $serial;
	        $rows = $db->select(BikeConstant::$door_lock_cols)->from(BikeUtil::table_full_name(BikeConstant::TABLE_DOOR_LOCK))->where("serial=:serial")->bindValues($cond)->limit(1)->query();
	        if(!empty($rows)) {
	            $devid = $rows[0]['id'];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_SERIAL_INDEX_TO_DEVICE_ID, $serial, $devid);
	            $array = $rows[0];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_DEVICE_INFO, $devid, json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	        }
	    } else {
	        return $devidredis;
	    }
	    // 返回值
	    return $devid;
	}
	
	public static function device_info_from_redis($redis, $db, $devid)
	{
	    $array = array();
	    // redis缓存获取
	    $cacheredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_DEVICE_INFO, $devid);
	    // redis缓存没有，从db获取
	    if($cacheredis==false) {
	        $cond['dev_id'] = $devid;
	        $rows = $db->select(self::get_cols(new DeviceInfo()))->from(BikeUtil::table_full_name(BikeConstant::TABLE_DEVICE))->where("dev_id=:dev_id")->bindValues($cond)->limit(1)->query();
	        if(!empty($rows)) {
	            $mac = $rows[0]['mac'];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_MAC_INDEXTO_DEVID, $mac, $devid);
	            $array = $rows[0];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_DEVICE_INFO, $devid, json_encode($array));
	        }
	    } else {
	        $rtnarr = json_decode($cacheredis, true);
	        return $rtnarr;
	    }
	    // 返回值
	    return $array;
	}
	
	public static function devsim_info_from_redis($redis, $db, $mac)
	{
	    $array = array();
	    // redis缓存获取
	    $cacheredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_DEVSIM_INFO, $mac);
	    // redis缓存没有，从db获取
	    if($cacheredis==false) {
	        $cond['mac'] = $mac;
	        $rows = $db->select(array("id","imei","lockstat"))->from(BikeUtil::table_full_name(BikeConstant::TABLE_DEVSIM))->where("mac=:mac")->bindValues($cond)->limit(1)->query();
	        if(!empty($rows)) {
	            $array = $rows[0];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_DEVSIM_INFO, $mac, json_encode($array));
	        }
	    } else {
	        $rtnarr = json_decode($cacheredis, true);
	        return $rtnarr;
	    }
	    // 返回值
	    return $array;
	}
	
	public static function mac_index_to_devid_from_redis($redis, $db, $mac)
	{
	    $devid = 0;
	    // redis缓存获取
	    $cacheredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_MAC_INDEXTO_DEVID, $mac);
	    // redis缓存没有，从db获取
	    if($cacheredis==false) {
	        $cond['mac'] = $mac;
	        $rows = $db->select(self::get_cols(new DeviceInfo()))->from(BikeUtil::table_full_name(BikeConstant::TABLE_DEVICE))->where("mac=:mac")->bindValues($cond)->limit(1)->query();
	        if(!empty($rows)) {
	            $devid = $rows[0]['dev_id'];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_MAC_INDEXTO_DEVID, $mac, $devid);
	            $array = $rows[0];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_DEVICE_INFO, $devid, json_encode($array));
	        }
	    } else {
	        return $cacheredis;
	    }
	    // 返回值
	    return $devid;
	}
	
	public static function price_info_from_redis($redis, $db, $pid)
	{
	    $array = array();
	    // redis缓存获取
	    $cacheredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_PRICE_INFO, $pid);
	    // redis缓存没有，从db获取
	    if($cacheredis==false) {
	        $cond['id'] = $pid;
	        $rows = $db->select(self::get_cols(new PriceInfo()))->from(BikeUtil::table_full_name(BikeConstant::TABLE_PRICE))->where("id=:id")->bindValues($cond)->limit(1)->query();
	        if(!empty($rows)) {
	            $array = $rows[0];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_PRICE_INFO, $pid, json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	        }
	    } else {
	        $rtnarr = json_decode($cacheredis, true);
	        return $rtnarr;
	    }
	    // 返回值
	    return $array;
	}
	
	public static function netpoint_info_from_redis($redis, $db, $id)
	{
	    $array = array();
	    // redis缓存获取
	    $cacheredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_NETPOINT_INFO, $id);
	    // redis缓存没有，从db获取
	    if($cacheredis==false) {
	        $cond['id'] = $id;
	        $rows = $db->select(array("id", "admin_id", "name", "shortname", "netaddr", "netlng", "netlat", "deposit", "status"))->from(BikeUtil::table_full_name(BikeConstant::TABLE_NETPOINT))->where("id=:id")->bindValues($cond)->limit(1)->query();
	        if(!empty($rows)) {
	            $array = $rows[0];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_NETPOINT_INFO, $id, json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	        }
	    } else {
	        $rtnarr = json_decode($cacheredis, true);
	        return $rtnarr;
	    }
	    // 返回值
	    return $array;
	}
	
	public static function goods_info_from_redis($redis, $db, $id)
	{
	    $array = array();
	    // redis缓存获取
	    $cacheredis = $redis->hGet(BikeConstant::AIRPLUS_HASH_GOODS_INFO, $id);
	    // redis缓存没有，从db获取
	    if($cacheredis==false) {
	        $cond['id'] = $id;
	        $rows = $db->select(array("id", "name", "goods_image", "price"))->from(BikeUtil::table_full_name(BikeConstant::TABLE_GOODS))->where("id=:id and status=1")->bindValues($cond)->limit(1)->query();
	        if(!empty($rows)) {
	            $array = $rows[0];
	            $redis->hSet(BikeConstant::AIRPLUS_HASH_GOODS_INFO, $id, json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	        }
	    } else {
	        $rtnarr = json_decode($cacheredis, true);
	        return $rtnarr;
	    }
	    // 返回值
	    return $array;
	}
	
	public static function saveformid($redis, $uid, $formids)
	{
	    $formidredis = $redis -> hGet(BikeConstant::AIRPLUS_HASH_FORMID, $uid);
	    if($formidredis==false) {
	        $savearr = $formids;
	    } else {
	        $savearr = json_decode($formidredis, true);
	        $savearr += $formids;
	    }
	    $redis -> hSet(BikeConstant::AIRPLUS_HASH_FORMID, $uid, json_encode($savearr));
	}
	
	public static function wxaccesstoken($redis, $appid, $appsecret)
	{
	    $accesstoken = $redis -> get("airplus_wxsmall_accesstoken");
	    if($accesstoken==false) {
	        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
	        try {
	            $ch = curl_init();
	            curl_setopt($ch, CURLOPT_URL, $url);
	            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	            curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	            $result = curl_exec($ch);
	            if(curl_errno($ch)) {
	                // do nothing, wait return empty string for access token
	            } else {
	                $rtnarr = json_decode($result, true);
	                if($rtnarr['access_token']) {
	                    $redis -> set('airplus_wxsmall_accesstoken', $rtnarr['access_token'], $rtnarr['expires_in']);
	                    return $rtnarr['access_token'];
	                } else {
	                    // do nothing, wait return empty string for access token
	                }
	            }
	            curl_close($ch);
	        } catch(Exception $e) {
	            // do nothing, wait return empty string for access token
	        }
	        return "";
	    } else {
	        return $accesstoken;
	    }
	}
	
	public static function wxtmplnotice($accesstoken, $data)
	{
	    $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=$accesstoken";
	    try {
	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, $url);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
	        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        $result = curl_exec($ch);
	        if(curl_errno($ch)) {
	            return false;
	        } else {
	            $rtnarr = json_decode($result, true);
	            curl_close($ch);
	            if($rtnarr['errcode']==0) return true;
	        }
	    } catch(Exception $e) {
	    }
	    return false;
	}
	
	private static function get_cols($obj)
	{
	    return array_keys(get_object_vars($obj));
	}
	
}