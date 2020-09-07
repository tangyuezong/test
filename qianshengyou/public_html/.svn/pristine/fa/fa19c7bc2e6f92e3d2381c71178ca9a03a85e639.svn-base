<?php
use Workerman\Lib\Db;
use Workerman\Lib\DbRedis;

require_once __DIR__ . '/../Lib/AliPay/AopSdk.php';
require_once __DIR__ . '/../Lib/WxPay/WxPay.Config.php';
require_once __DIR__ . '/../Lib/WxPay/WxPay.Api.php';
require_once __DIR__ . '/../Lib/WxPay/WxPay.Notify.php';
require_once __DIR__ . '/../Lib/WxPay/log.php';

class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id, $payment)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		if($payment==BikeConstant::PAY_CHANNEL_WXJSAPI) {
			$input->SetAppid(WxPayConfig::APPID_JSAPI);
			$input->SetMch_id(WxPayConfig::MCHID_JSAPI);
		}
		$result = WxPayApi::orderQuery($input);
		WxPayLog::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
				&& array_key_exists("result_code", $result)
				&& $result["return_code"] == "SUCCESS"
				&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		$db = Db::instance('cobike_mysql');
		$redis = DbRedis::instance('cobike_redis');
	    $datajson = json_encode($data, JSON_UNESCAPED_UNICODE);
	    
// 	    if(!array_key_exists("transaction_id", $data) || !array_key_exists("attach", $data)) {
	    if(!array_key_exists("transaction_id", $data)) {
	    	$msg = "输入参数不正确";
	    	WxPayLog::WARN("wxnotify call back - $msg: " . $datajson);
	        return false;
	    }
	    
	    $notifycache = $redis -> get(BikeConstant::PROJECT_AIRPLUS_HYPHEN . $data['transaction_id']);
	    //var_dump($notifycache);
	    if($notifycache==1) {
	    	$msg = "重复微信支付异步通知消息，消息已经正常处理";
	    	WxPayLog::INFO("wxnotify call back - $msg: " . $datajson);
	    	return false;
	    }
	    
	    $attach = explode("&", $data['attach']);
	    foreach ($attach as $tvalue) {
	    	$temp = explode("=", $tvalue);
	    	$reqReserved[$temp[0]] = $temp[1];
	    }
	    $uid = $reqReserved['users_id'];
	    $item = $reqReserved['payitem'];
	    $payment = $reqReserved['payment'];
	    //查询订单，判断订单真实性
	    if(!$this->Queryorder($data["transaction_id"], $payment)) {
	    	$msg = "订单查询失败";
	    	WxPayLog::WARN("wxnotify call back - $msg: " . $datajson);
	    	return false;
	    }
	    // trans table
	    $tranew['uid'] = $uid;
	    //$tranew['payment'] = BikeConstant::PAY_CHANNEL_WEIXIN;
	    $tranew['payment'] = $payment;
	    $tranew['item'] = $item;
	    $tranew['extra'] = 0;
	    $tranew['order_no'] = "";		// 默认为空，如果是订单支付那么就是订单的order_no
	    $tranew['tran_id'] = $data['transaction_id'];
	    $tranew['tran_time'] = $data['time_end'];
	    $tranew['createtime'] = strtotime($tranew['tran_time']);
	    // 其他相关表
	    $usersave = $bedorderarr = $goodsorderarr = array();
	    try {
	    	switch($item) {
	    		case BikeConstant::PAY_ITEM_RENT_CARS:           // 租车预定金支付异步通知
	    		    // bed order insert
	    		    $ordercache = $redis->get($reqReserved['order_no']);
	    		    $bedorderarr = json_decode($ordercache, true);
	    		    $bedorderarr['tran_id'] = $tranew['tran_id'];
	    		    // trans insert
	    		    $tranew['order_no'] = $reqReserved['order_no'];
	    		    $tranew['amount'] = round($data['total_fee']/100, 2);
	    		    $tranew['status'] = 1;
	    		    break;
	    		case BikeConstant::PAY_ITEM_DEPOSIT:
	    		    $userarr = BikeUtil::userinfo_from_redis($redis, $db, $uid);
	    			if(empty($userarr)) {
	    				$msg = "获取用户 $uid 账户信息失败";
	    				WxPayLog::WARN("wxnotify call back - $msg: " . $datajson);
	    				return false;
	    			}
	    			// 用户支付押金金额
	    			$tranew['amount'] = intval($data['total_fee'])/100;
	    			$tranew['status'] = 1;
	    			// 用户支付押金后，押金账户金额
	    			$deposit = (double)$userarr['deposit'];
	    			$userarr['deposit'] = $usersave['deposit'] = bcadd($deposit, $tranew['amount'], 2);
	    			// 用户表更新where语句
	    			$userwhere = "id=$uid and deposit=$deposit";
	    		    break;
    		    case BikeConstant::PAY_ITEM_GOODS:           // 商品支付
    		        // bed order insert
    		        $ordercache = $redis->get($reqReserved['order_no']);
    		        $goodsorderarr = json_decode($ordercache, true);
    		        // trans insert
    		        $tranew['order_no'] = $reqReserved['order_no'];
    		        $tranew['amount'] = round($data['total_fee']/100, 2);
    		        $tranew['status'] = 1;
    		        break;
	    		default:
	    			return false;
	    			break;
	    	}
	    	$tranew['refund_id'] = "";
	    	$tranew['refund_fee'] = 0;
	    	$tranew['refund_time'] = 0;
	    	// db操作
	    	$db->beginTrans();
	    	$trantemp = $db->insert(BikeUtil::table_full_name(BikeConstant::TABLE_TRANS))->cols($tranew)->query();
	    	$usertemp = $bedordertemp = $goodsordertemp = true;
	    	if(!empty($bedorderarr)) $ordertemp = $db->insert(BikeUtil::table_full_name(BikeConstant::TABLE_BEDORDER))->cols($bedorderarr)->query();
	    	if(!empty($goodsorderarr)) $ordertemp = $db->insert(BikeUtil::table_full_name(BikeConstant::TABLE_GOODSORDER))->cols($goodsorderarr)->query();
	    	if(!empty($usersave)) $usertemp = $db->update(BikeUtil::table_full_name(BikeConstant::TABLE_USERS))->cols($usersave)->where($userwhere)->query();
	    	if($trantemp==null || $bedordertemp==null || $goodsordertemp==null || $usertemp==null) {
	    		$db->rollBackTrans();
	    		$msg = "db处理失败 - ";
	    		if($trantemp==null) $msg .= "trans处理失败，";
	    		if($bedordertemp==null) $msg .= "bedorder处理失败，";
	    		if($goodsordertemp==null) $msg .= "goodsorder处理失败，";
	    		if($usertemp==null) $msg .= "users处理失败，";
	    		WxPayLog::WARN("wxnotify call back - $msg: " . $datajson);
	    		$rtnresult = false;
	    	} else {
	    		$db->commitTrans();
	    		// 订单保存至缓存
// 	    		$orderarr['payment'] = $payment;
	    		if(!empty($bedorderarr)) {
	    		    $redis->hSet(BikeConstant::AIRPLUS_HASH_BEDORDER, $bedorderarr['order_no'], json_encode($bedorderarr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	    		    $redis->hSet(BikeConstant::AIRPLUS_HASH_USER_CURRENT_ORDER, $uid, $bedorderarr['order_no']);
	    		    $redis->hSet(BikeConstant::AIRPLUS_HASH_RENTED_DEVICE, $bedorderarr['dev_id'], $bedorderarr['users_id']);
	    		}
	    		if(!empty($usersave)) $redis->hSet(BikeConstant::AIRPLUS_HASH_USERINFO, $uid, json_encode($userarr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	    		// 设置异步通知处理成功缓存1小时，避免重复处理
	    		$redis -> set(BikeConstant::PROJECT_AIRPLUS_HYPHEN . $data['transaction_id'], 1, 3600);
	    		$rtnresult = true;
	    	}
	    	// 给微信返回结果
	    	return $rtnresult;
	    } catch(PDOException $e) {
	    	$msg = "回调处理发生异常" . $e->getMessage();
	    	WxPayLog::WARN("wxnotify call back - $msg: " . $datajson);
	    	return false;
	    }	//end of try catch
	}	//end of NotifyProcess
}

/**
 * NotifyUrlAction.class.php
 * @copyright           cobike
 * @license             http://www.cobike.cn
 * @lastmodify          2014-11-07
 * */
class WxNotifyUrl {
    
	public static function notify($xml) 
	{
	    $logHandler = new CLogFileHandler(__DIR__.'/../logs/weixin/'.date('Y-m-d').'.log');
	    $log = WxPayLog::Init($logHandler, 15);
	    
	    WxPayLog::DEBUG("begin notify");
	    WxPayLog::DEBUG($xml);
	    $notify = new PayNotifyCallBack();
	    $result = $notify->Handle($xml, false);
	    //echo $result; echo "\n";
	    return $result;
	}
}