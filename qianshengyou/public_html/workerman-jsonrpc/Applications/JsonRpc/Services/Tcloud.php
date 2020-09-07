<?php
use Workerman\Lib\Db;
use Workerman\Lib\DbRedis;

/**
 * Tcloud.php
 * @copyright           cobike
 * @license             http://www.cobike.cn
 * @lastmodify          2016-7-27
 * */

class Tcloud
{
    const appId = "gndA28K8TP2m3pN0Hoqv40qv5IQa";
    
    const appSecret = "y5Lp8gKLpEPOGDgazO3dZm3kT20a";
    
    const certPwd = "IoM@1234";
    
    public static function nbDataTransfer($request)
    {
        $request['appId'] = self::appId;
//         var_dump($request);
        $data_string = json_encode($request);
        $url = "https://develop.api.ct10649.com:8743/iocm/app/cmd/v1.4.0/deviceCommands";
        try {
            $accesstoken = self::getaccesstoken();
            $headers = [
                'app_key: ' . self::appId,
                'Authorization: Bearer '.$accesstoken,
                'Content-Type: application/json',
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSLCERT, __DIR__."/../Lib/Cert/outgoingCert.pem");
            curl_setopt($ch, CURLOPT_SSLKEY, __DIR__."/../Lib/Cert/outgoingKey.pem");
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            $result = curl_exec($ch);
            var_dump("the nbdatatransfer result is: .................");
            var_dump($result);
            var_dump(curl_error($ch));
            curl_close($ch);
            return $result;
        } catch(Exception $e) {
            return $e->getMessage();
        }
    }
    
    private static function getaccesstoken()
	{
        $redis = DbRedis::instance('cobike_redis');
	    $accesstoken = $redis -> get("airplus_tcloud_accesstoken");
	    if($accesstoken==false) {
// 	        $url = "https://180.101.147.208:8743/iocm/app/sec/v1.1.0/login?&appId=" . self::appId . "&secret=" . self::appSecret;
	        $url = "https://develop.api.ct10649.com:8743/iocm/app/sec/v1.1.0/login";
	        try {
	            $ch = curl_init();
	            curl_setopt($ch, CURLOPT_URL, $url);
	            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSLCERT, __DIR__."/../Lib/Cert/outgoingCert.pem");
                curl_setopt($ch, CURLOPT_SSLKEY, __DIR__."/../Lib/Cert/outgoingKey.pem");
	            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['appId'=>self::appId, 'secret'=>self::appSecret]));
	            $result = curl_exec($ch);
	            $error = curl_error($ch);
	            $errno = curl_errno($ch);
	            if($errno) {
	                curl_close($ch);
	                // do nothing, wait return empty string for access token
	            } else {
	                curl_close($ch);
	                $rtnarr = json_decode($result, true);
// 	                var_dump("the getaccess token curl result decode is: ");
// 	                var_dump($rtnarr);
	                if($rtnarr['accessToken']) {
	                    $redis -> set('airplus_tcloud_accesstoken', $rtnarr['accessToken'], $rtnarr['expiresIn']);
	                    return $rtnarr['accessToken'];
	                } else {
	                    var_dump("accesstoken is empty");
	                }
	            }
	        } catch(Exception $e) {
	            var_dump($e->getMessage());
	            curl_close($ch);
	        }
	        return "";
	    } else {
	        return $accesstoken;
	    }
	}
    
}