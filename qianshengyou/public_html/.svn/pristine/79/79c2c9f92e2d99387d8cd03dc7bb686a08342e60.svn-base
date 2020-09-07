<?php
use Workerman\Lib\Db;
use Workerman\Lib\DbRedis;

require_once __DIR__ . '/../Util/BikeUtil.php';

/**
 * RedisHelper.php
 * @copyright           cobike
 * @license             http://www.cobike.cn
 * @lastmodify          2016-7-27
 * */
class RedisHelper
{
    /**
     * put the lock info to redis
     * 
     * @param array $request, the $request format is: 
     * 			$request['type'] = "price"
     * 			$request['data'] = array(pid1=>price1, pid2=>price2......);
     * @return boolean
     */
    public static function hMSet($key, $value, $usekeymap=true)
    {
    	//$request = json_decode($requestJson, true);
    	$redis = DbRedis::instance('cobike_redis');
    	
    	$hashkey = "";
    	if($usekeymap) {
    		$hashkeymap = BikeConstant::$hashkeymap;
    		$hashkey = $hashkeymap[$key];
    	} else {
    		$hashkey = $key;
    	}
    	if(empty($hashkey)) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, BikeConstant::INFO_SAVE_REDIS_EMPTY_KEY_MSG);
    	
    	try {
    		$result = $redis -> hMSet($hashkey, $value);
    		if($result==false) {
    			$array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, BikeConstant::INFO_SAVE_REDIS_FAILED_MSG);
    		} else {
    			$array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, BikeConstant::INFO_SAVE_REDIS_SUCCESS_MSG);
    		}
    	} catch(Exception $e) {
    		$array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, BikeConstant::INFO_SAVE_REDIS_EXCEPTION_MSG);
    	}
    	
    	return $array;
    }
    
    public static function hMGet($key, $array, $usekeymap=true)
    {
    	$redis = DbRedis::instance('cobike_redis');
    	
    	$hashkey = "";
    	if($usekeymap) {
    		$hashkeymap = BikeConstant::$hashkeymap;
    		$hashkey = $hashkeymap[$key];
    	} else {
    		$hashkey = $key;
    	}
    	if(empty($hashkey)) return BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, BikeConstant::INFO_GET_REDIS_EMPTY_KEY_MSG);
    	
    	try {
    		$result = $redis -> hMGet($hashkey, $array);
    		$array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, BikeConstant::INFO_GET_REDIS_SUCCESS_MSG, $result);
    	} catch(Exception $e) {
    		$array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, BikeConstant::INFO_GET_REDIS_EXCEPTION_MSG);
    	}
    	
    	return $array;
    }
    
    public static function hDel($key, $field, $usekeymap=true)
    {
    	$redis = DbRedis::instance('cobike_redis');
    	
    	$hashkey = "";
    	if($usekeymap) {
    		$hashkeymap = BikeConstant::$hashkeymap;
    		$hashkey = $hashkeymap[$key];
    	} else {
    		$hashkey = $key;
    	}
    	if(empty($hashkey)) return -2;
    	 
    	try {
    		$result = $redis -> hDel($hashkey, $field);
    		if($result==false) return -3;
    		return $result;
    	} catch(Exception $e) {
    		return -1;
    	}
    }
    
    public static function del($key, $usekeymap=true)
    {
    	$redis = DbRedis::instance('cobike_redis');
    	
    	$hashkey = "";
    	if($usekeymap) {
    		$hashkeymap = BikeConstant::$hashkeymap;
    		$hashkey = $hashkeymap[$key];
    	} else {
    		$hashkey = $key;
    	}
    	if(empty($hashkey)) return -2;
    	
    	try {
    		$result = $redis -> del($hashkey);
    		if($result==false) return -3;
    		return $result;
    	} catch(Exception $e) {
    		return -1;
    	}
    }
    
    public static function hMGetArray($key, $array, $usekeymap=true)
    {
    	$redis = DbRedis::instance('cobike_redis');
    	 
    	$hashkey = "";
    	if($usekeymap) {
    		$hashkeymap = BikeConstant::$hashkeymap;
    		$hashkey = $hashkeymap[$key];
    	} else {
    		$hashkey = $key;
    	}
    	if(empty($hashkey)) return -2;
    	
    	try {
    		$result = $redis -> hMGet($hashkey, $array);
    		if($result==false) return -3;
    		return $result;
    	} catch(Exception $e) {
    		return -1;
    	}
    }
    
    public static function hGetAll($key, $usekeymap=true)
    {
    	$redis = DbRedis::instance('cobike_redis');
    	
    	$hashkey = "";
    	if($usekeymap) {
    		$hashkeymap = BikeConstant::$hashkeymap;
    		$hashkey = $hashkeymap[$key];
    	} else {
    		$hashkey = $key;
    	}
    	if(empty($hashkey)) return -2;
    	
    	try {
    		$result = $redis -> hGetAll($hashkey);
    		if($result==false) return array();
    		return $result;
    	} catch(Exception $e) {
    		return -1;
    	}
    }
    
    public static function get($key)
    {
        $redis = DbRedis::instance('cobike_redis');
        
        try {
            $result = $redis -> get($key);
            return $result;
        } catch(Exception $e) {
        }
        
        return "";
    }
    
    public static function set($key, $value, $timeout=0)
    {
        $redis = DbRedis::instance('cobike_redis');
        
        try {
            if($timeout>0) {
                $result = $redis -> setEx($key, $timeout, $value);
            } else {
                $result = $redis -> set($key, $value);
            }
        } catch(Exception $e) {
        }
    }
    
    public static function rtnscope($request)
    {
    	$redis = DbRedis::instance('cobike_redis');
    	$db = Db::instance('cobike_mysql');
    	
    	try {
    		$rtnscope = BikeUtil::hiride_rtnscope_from_redis($redis, $db, $request['hid']);
    		
    		return $rtnscope;
    	} catch(Exception $e) {
    		return array();
    	}
    }
    
}
