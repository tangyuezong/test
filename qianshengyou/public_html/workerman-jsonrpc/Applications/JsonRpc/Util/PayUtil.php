<?php
//require_once("lib/upmp_service.php");
require_once __DIR__ . '/../Lib/AliPay/AopSdk.php';
require_once __DIR__ . '/../Lib/WxPay/WxPay.Api.php';
require_once __DIR__ . '/../Lib/WxPay/log.php';

/**
 * PayUtil.php
 * @copyright           cobike
 * @license             http://www.cobike.cn
 * @lastmodify          2015-12-23
 * */
class PayUtil 
{
	/**
	 * get transaction serial number from weixin
	 * 
	 * @param array $reqarr
	 * @param int $paytype
	 * @return json
	 */
	public static function get_trans_serial_number_wx_apppay($reqarr, $orderarr)
	{
		//var_dump($reqarr);
		$input = new WxPayUnifiedOrder();
		if($reqarr['payment']==BikeConstant::PAY_CHANNEL_WEIXIN) {
			$input->SetTrade_type("APP");
		} else {
			$input->SetTrade_type("JSAPI");
			$input->SetAppid(WxPayConfig::APPID_JSAPI);
			$input->SetMch_id(WxPayConfig::MCHID_JSAPI);
			$input->SetOpenid($reqarr['openid']);
		}
		$out_trade_no = "";
		switch(intval($reqarr['item'])) {
			case BikeConstant::PAY_ITEM_DEPOSIT:
		        $input->SetTotal_fee($orderarr['deposit'] * 100);
				$out_trade_no = date(BikeConstant::TIME_FORMAT_YMDHIS)."deposit";
			    break;
			case BikeConstant::PAY_ITEM_RENT_CARS:
			    $input->SetTotal_fee($orderarr['pay'] * 100);
			    $reqReserved['order_no'] = $orderarr['order_no'];
			    $out_trade_no = date(BikeConstant::TIME_FORMAT_YMDHIS).$orderarr['order_no'];
			    break;
		    case BikeConstant::PAY_ITEM_GOODS:
		        $input->SetTotal_fee($orderarr['total'] * 100);
		        $reqReserved['order_no'] = $orderarr['order_no'];
		        $out_trade_no = date(BikeConstant::TIME_FORMAT_YMDHIS).$orderarr['order_no'];
		        break;
		}
		$input->SetOut_trade_no($out_trade_no);
		$input->SetNotify_url($reqarr['domain'] . WxPayConfig::APP_NOTIFY_URL);
		$input->SetBody(BikeConstant::$pay_item_desc_map[$reqarr['item']]);
		// 微信透传参数
		$reqReserved['users_id'] = intval($orderarr['users_id']);
		$reqReserved['payitem'] = intval($reqarr['item']);
		$reqReserved['payment'] = intval($reqarr['payment']);
		$input->setAttach(self::buildReserved($reqReserved));
		$input->domain = $reqarr['domain'];
		// 统一下单接口
		try {
			$resp = WxPayApi::unifiedOrder($input);
			if($resp['return_code']=="SUCCESS" && $resp['result_code']=="SUCCESS") {
			    if(intval($reqarr['item'])==BikeConstant::PAY_ITEM_DEPOSIT) {
			        $reqarr['payment']==BikeConstant::PAY_CHANNEL_WEIXIN ? $data=array('weixin'=>self::format_wx_pay_param($resp, $out_trade_no)) : $data=array('weixin'=>self::format_wx_pay_param_jsapi($resp));
			    } else {
				    $reqarr['payment']==BikeConstant::PAY_CHANNEL_WEIXIN ? $data=array('weixin'=>self::format_wx_pay_param($resp, $out_trade_no), 'order_no'=>$orderarr['order_no']) : $data=array('weixin'=>self::format_wx_pay_param_jsapi($resp), 'order_no'=>$orderarr['order_no']);
			    }
			    return BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, BikeConstant::PAY_INVOKE_SUCCESS_MSG, $data);
			} else {
				if(!empty($resp['return_msg'])) {
					$msg = $resp['return_msg'];
				} else {
					$msg = $resp['err_code_des'];
				}
				return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $msg);
			}
		} catch (Exception $e) {
			$msg = BikeConstant::WXPAY_ENCOUNTER_EXCEPTION_MSG;
			if(!empty($e->getMessage())) $msg .=  ", " . $e->getMessage();
			return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, $msg);
		}
	}
	
	/**
	 * generate alipay request and sign
	 * @param string $reqarr
	 * @param double $amount
	 * @param boolean $is_balance
	 * @return json
	 */
	public static function get_trans_serial_number_ali_apppay($reqarr)
	{
		// init the alipay request
		$request['app_id'] = BikeConstant::COBIKE_ALIPAY_APP_ID;
		$array['app_id'] = urlencode($request['app_id']);
		$request['method'] = BikeConstant::ALIPAY_TRADE_APP_PAY;
		$array['method'] = urlencode($request['method']);
		$request['format'] = BikeConstant::ALIPAY_DATA_FORMAT_JSON;
		$array['format'] = urlencode($request['format']);
		$request['charset'] = BikeConstant::ALIPAY_CHARSET;
		$array['charset'] = urlencode($request['charset']);
		$request['sign_type'] = BikeConstant::ALIPAY_SIGN_TYPE;
		$array['sign_type'] = urlencode($request['sign_type']);
		$request['timestamp'] = date(BikeConstant::TIME_FORMAT_YMDHIS_MINUS);
		$array['timestamp'] = urlencode($request['timestamp']);
		$request['version'] = BikeConstant::ALIPAY_API_Version;
		$array['version'] = urlencode($request['version']);
		$request['notify_url'] = BikeConstant::COBIKE_ALIPAY_NOTIFY_URL;
		$array['notify_url'] = urlencode($request['notify_url']);
		
		$biz['subject'] = BikeConstant::$pay_item_desc_map[$reqarr['item']];
		$out_trade_no = "";
		switch((int)$reqarr['item']) {
			case BikeConstant::PAY_ITEM_RENT_FEE:
				$out_trade_no = date(BikeConstant::TIME_FORMAT_YMDHIS).$reqarr['order_no'];
				break;
			case BikeConstant::PAY_ITEM_BALANCE:
				$out_trade_no = date(BikeConstant::TIME_FORMAT_YMDHIS)."balance";
				break;
			case BikeConstant::PAY_ITEM_DEPOSIT:
				$out_trade_no = date(BikeConstant::TIME_FORMAT_YMDHIS)."deposit";
				break;
		}
		$biz['out_trade_no'] = $out_trade_no . "_" . $reqarr['uid'] . "_" . $reqarr['item'];
		if((int)$reqarr['item']==BikeConstant::PAY_ITEM_BALANCE) $biz['out_trade_no'] .= "_" . (string)($reqarr['extra']*100);
		$biz['total_amount'] = (string)$reqarr['amount'];
		$biz['product_code'] = "QUICK_MSECURITY_PAY";
		
		try {
			//$array['biz_content'] = $request['biz_content'] = self::mcrypt_aes_cbc_pkcs5padding(json_encode($biz));
			$request['biz_content'] = json_encode($biz, JSON_UNESCAPED_UNICODE);
			$array['biz_content'] = urlencode($request['biz_content']);
			
			// alipay sdk generate sign
			$aop = new AopClient();
			$aop->rsaPrivateKeyFilePath = __DIR__ . '/../Lib/Cert/cobike_rsa_private_key.pem';
			
			//var_dump($request);
			$request['sign'] = $aop->generateSign($request);
			//var_dump(urlencode($request['sign']));
			$array['sign'] = urlencode($request['sign']);
			
			
			$array['orderStr'] = self::createLinkString($array, true, false);
			
			return BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, BikeConstant::PAY_INVOKE_SUCCESS_MSG, array('zfb'=>$array));
		} catch(Exception $e) {
			$msg = BikeConstant::ALIPAY_ENCOUNTER_EXCEPTION_MSG;
			if(!empty($e->getMessage())) $msg .=  ", " . $e->getMessage();
			return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, BikeConstant::ALIPAY_ENCOUNTER_EXCEPTION_MSG);
		}
	}
	
	/**
	 * generate the weixin signature and format the unifiedorder response
	 *
	 * @param array $resp
	 * @return array $array
	 */
	public static function format_wx_pay_param($resp, $out_trade_no)
	{
		//$array['appid'] = $resp['appid'];
		$signarr['appid'] = $resp['appid'];
		$array['noncestr'] = $signarr['noncestr'] = $resp['nonce_str'];
		$array['package'] = $signarr['package'] = "Sign=WXPay";
		$array['partnerid'] = $signarr['partnerid'] = $resp['mch_id'];
		$array['prepayid'] = $signarr['prepayid'] = $resp['prepay_id'];
		$array['timestamp'] = $signarr['timestamp'] = time();
		
		$array['sign'] = self::getSign($signarr);
		$array['tran_id'] = $out_trade_no;
		
		return $array;
	}
	
	public static function format_wx_pay_param_jsapi($resp)
	{
		$array['appId'] = $resp['appid'];
		$array['nonceStr'] = $resp['nonce_str'];
		$array['timeStamp'] = strval(time());
		$array['package'] = "prepay_id=" . $resp['prepay_id'];
		$array['signType'] = "MD5";
		$array['paySign'] = self::getSign($array);
		
		return $array;
	}
	
	private static function getSign($signarr, $key=WxPayConfig::KEY)
	{
		//签名步骤一：按字典序排序参数
		$buff = "";
		ksort($signarr);
		foreach ($signarr as $k => $v) {
			$buff .= $k . "=" . $v . "&";
		}
		$buff = trim($buff, "&");
		//签名步骤二：在string后加入KEY
		$buff = $buff . "&key=" . WxPayConfig::KEY;
		//签名步骤三：MD5加密
		$buff = md5($buff);
		//签名步骤四：所有字符转为大写
		
		return strtoupper($buff);
	}
	
	/**
     * 拼接保留域
     * @param req 请求要素
     * @return 保留域
     */
	public static function buildReserved($req)
	{
    	$prestr = self::createLinkstring($req, false, false);
    	return $prestr;
    }
    
    /**
     * 把请求要素按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param para 请求要素
     * @param sort 是否需要根据key值作升序排列
     * @param encode 是否需要URL编码
     * @return 拼接成的字符串
     */
    public static function createLinkString($para, $sort, $encode)
    {
        $linkString  = "";
        if ($sort){
            $para = self::argSort($para);
        }
        while (list ($key, $value) = each ($para)) {
            if ($encode){
                $value = urlencode($value);
            }
            $linkString.=$key.BikeConstant::QSTRING_EQUAL.$value.BikeConstant::QSTRING_SPLIT;
        }
        //去掉最后一个&字符
        $linkString = substr($linkString,0,count($linkString)-2);
        return $linkString;
    }
    
    /**
     * 对数组排序
     * @param $para 排序前的数组
     * return 排序后的数组
     */
    public static function argSort($para) {
    	ksort($para);
    	reset($para);
    	return $para;
    }
    
    /**
     * biz content aes/cbc/pkcs5padding encrypt
     * 
     * @param string $input
     * @return string
     */
    public static function mcrypt_aes_cbc_pkcs5padding($input)
    {
    	$size = mcrypt_get_block_size(BikeConstant::CIPHER_AES_256, BikeConstant::CIPHER_MODE_CBC);
    	$input = self::pkcs5_pad($input, $size);
    	
    	$key = BikeConstant::COBIKE_ALIPAY_AES_KEY;
    	$td = mcrypt_module_open(BikeConstant::CIPHER_AES_256, BikeConstant::EMPTY_STRING, BikeConstant::CIPHER_MODE_CBC, BikeConstant::EMPTY_STRING);
    	$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    	mcrypt_generic_init($td, $key, $iv);
    	$data = mcrypt_generic($td, $input);
    	mcrypt_generic_deinit($td);
    	mcrypt_module_close($td);
    	$data = base64_encode($data);
    	return $data;
    }
    
    /**
     * pkcs5 padding
     * 
     * @param string $text
     * @param int $blocksize
     * @return string
     */
    public static function pkcs5_pad ($text, $blocksize)
    {
    	$pad = $blocksize - (strlen($text) % $blocksize);
    	return $text . str_repeat(chr($pad), $pad);
    }
    
    /**
     * pkcs5 unpadding
     * 
     * @param string $text
     * @return string
     */
    public static function pkcs5_unpad($text)
    {
    	$pad = ord($text{strlen($text)-1});
    	if ($pad > strlen($text)) return false;
    	if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false;
    	return substr($text, 0, -1 * $pad);
    }
}
