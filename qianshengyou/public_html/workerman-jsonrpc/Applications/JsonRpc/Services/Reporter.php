<?php
use Workerman\Lib\Db;

/**
 * Bedorder.php
 * @copyright           airplus
 * @license             https://www.air-plus.cn
 * @lastmodify          2018s-7-27
 * */
class Reporter
{
    /**
     * 分润企业零钱付款
     * @param array $request
     * @return json
     */
    public static function rtnbednotice()
    {
        $db = Db::instance('cobike_mysql');
        // 参数
        $veritemplate = "SMS_151991979";
        $msgparam['afternoontime'] = "17:00";
        $msgparam['morningtime'] = "7:30";
        $msgparam['midtime'] = "12:00 - 14:00";
        $msgparam['endtime'] = "下午的14:10";
        $msgparam['phone'] = "13510473607";
        try {
            $failcount = 0;
            $wherestr = sprintf("step=%d and status=1 and canuseend<=%d", BikeConstant::ORDER_PAID, time()+1800);
            $list = $db->select(["phone"])->from(BikeUtil::table_full_name(BikeConstant::TABLE_BEDORDER))->where($wherestr)->query();
            if(!empty($list)) {
                foreach ($list as $val) {
                    if(!empty($val['phone'])) {
                        $result = DaYuShortMsg::sendSms($val['phone'], $veritemplate, $msgparam);
                        $result = get_object_vars($result);
                        if(key_exists('Code', $result) && $result['Code']!="OK") $failcount++;
                    }
                }
                if($failcount>0) {
                    $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "关锁提醒失败：".$failcount." 个");
                } else {
                    $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "关锁提醒成功！总计发送".count($list)."个");
                }
            } else {
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "无关锁提醒记录！");
            }
        } catch(Exception $e) {
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "关锁提醒调用异常：".$e->getMessage());
        }
        return $array;
    }
    
    public static function rtnbedmonitor()
    {
        $db = Db::instance('cobike_mysql');
        // 参数
        $prealarmtemplate = "SMS_164508535";
        $alarmtemplate = "SMS_164513546";
        $msgparam['contact'] = "4008062128";
        try {
            $time = time();
            $failcount = 0;
            $misscount = 0;
            $nophonecount = 0;
            $wherestr = sprintf("step=%d and status=1", BikeConstant::ORDER_PAID);
            $list = $db->select(["phone", "pricetype", "canusestart", "canuseend", "createtime"])->from(BikeUtil::table_full_name(BikeConstant::TABLE_BEDORDER))->where($wherestr)->query();
            if(!empty($list)) {
                foreach ($list as $val) {
                    if($val['pricetype']==1) {
                        if($time - $val['createtime'] >= 36000) {      // 按时使用了10个小时
                            if(!empty($val['phone'])) {
                                $result = DaYuShortMsg::sendSms($val['phone'], $alarmtemplate, $msgparam);
                                $result = get_object_vars($result);
                                if(key_exists('Code', $result) && $result['Code']!="OK") $failcount++;
                            } else {
                                $nophonecount++;
                            }
                        } else {
                            $misscount++;       // 按时使用10小时内
                        }
                    } else {
                        if($time >= $val['canuseend']) {        // 超时
                            if(!empty($val['phone'])) {
                                $result = DaYuShortMsg::sendSms($val['phone'], $alarmtemplate, $msgparam);
                                $result = get_object_vars($result);
                                if(key_exists('Code', $result) && $result['Code']!="OK") $failcount++;
                            } else {
                                $nophonecount++;
                            }
                        } else if($val['canuseend'] - $time <= 3660) {       // 1小时内
                            if(!empty($val['phone'])) {
                                $result = DaYuShortMsg::sendSms($val['phone'], $prealarmtemplate, $msgparam);
                                $result = get_object_vars($result);
                                if(key_exists('Code', $result) && $result['Code']!="OK") $failcount++;
                            } else {
                                $nophonecount++;
                            }
                        } else {
                            $misscount++;       // 未到1小时内
                        }
                    }
                }
                if($failcount>0) {
                    $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "关锁提醒失败：".$failcount." 个，成功提醒" . (count($list) - $misscount - $failcount - $nophonecount) . "个", $list);
                } else {
                    $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "关锁提醒成功！总计".(count($list))."个，" . "未到期" . $misscount. "个，无手机".$nophonecount."个", $list);
                }
            } else {
                $array = BikeUtil::format_return_array(BikeConstant::Interface_Pass_Code, "无关锁提醒记录！");
            }
        } catch(Exception $e) {
            $array = BikeUtil::format_return_array(BikeConstant::Interface_Error_Code, "关锁提醒调用异常：".$e->getMessage());
        }
        return $array;
    }
    
}
