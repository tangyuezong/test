<?php
use Workerman\Lib\Db;
use Workerman\Lib\DbRedis;

/**
 * Bedorder.php
 * @copyright           airplus
 * @license             https://www.air-plus.cn
 * @lastmodify          2018s-7-27
 * */
class Fenrun
{
    /**
     * 分润企业零钱付款
     * @param array $request
     * @return json
     */
    public static function fenrunpay($request)
    {
        $db = Db::instance('cobike_mysql');
        $redis = DbRedis::instance('cobike_redis');
        // 参数
        $input = new WxPayCompanyPay();
        $input->SetMch_appid(WxPayConfig::APPID_JSAPI);
        $input->SetMch_id(WxPayConfig::MCHID_JSAPI);
        $input->SetCheck_name(WxPayConfig::CHECK_NAME_DEFAULT);
        $input->SetPartner_trade_no($request['id']);
        $input->SetOpenid($request['openid']);
        $input->SetRe_user_name($request['realname']);
        $input->SetAmount($request['realamount']*100);
        $input->SetDesc($request['fenrundate']." 分润金额");
        $input->SetSpbill_create_ip($request['ip']);
        try {
            $resp = WxPayApi::companyPay($input);
            var_dump($resp);
            // 微信退款结果判断
            if($resp['return_code']=="SUCCESS") {
                if($resp['result_code']=="SUCCESS") {
                    $save['realamount'] = $request['realamount'];
                    $save['tran_id'] = $resp['payment_no'];
                    $save['tran_time'] = $resp['payment_time'];
                    $save['paynote'] = "付款成功";
                    $save['admin_id'] = $request['admin_id'];
                    $save['updatetime'] = time();
                    $save['status'] = 1;
                } else {
                    $save['paynote'] = "调用微信付款业务结果错误 - 错误码:".$resp['err_code'].", 错误码描述：".$resp['err_code_des'];
                    $save['admin_id'] = $request['admin_id'];
                    $save['updatetime'] = time();
                    $save['status'] = 0;
                }
                // 保存条件
                $cond['id'] = $request['id'];
                $temp = $db->update(BikeUtil::table_full_name(BikeConstant::TABLE_FENRUN))->cols($save)->where("id=:id and status=0")->bindValues($cond)->query();
                // 执行结果判断
                if($temp==null) {
                    if(strcmp($save['paynote'], "付款成功")==0) {
                        return ["code"=>400, "msg"=>"分润转账成功，更新数据库失败，请联系技术支持！"];
                    } else {
                        return ["code"=>400, "msg"=>$save['paynote']."，更新数据库失败，请联系技术支持！"];
                    }
                } else {
		  if($save['status'] == 1){
                       return ["code"=>0, "msg"=>"分润转账成功"];
		  }else{
                       return ["code"=>400, "msg"=>"分润转账失败"];
		  }
                }
            } else {
                return ["code"=>400, "msg"=>"调用微信付款发生错误：".$resp['return_msg']];
            }
        } catch(Exception $e) {
            return ["code"=>BikeConstant::Interface_Error_Code, "msg"=>"微信付款接口调用异常：".$e->getMessage()];
        }
    }
    
}
