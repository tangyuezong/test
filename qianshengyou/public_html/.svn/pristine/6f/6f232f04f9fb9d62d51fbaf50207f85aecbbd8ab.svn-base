<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Db;
use think\Exception;
use think\Log;
use app\common\library\RpcClient;
// use PHPExcel_Reader_Excel2007;
// use PHPExcel_Writer_Excel2007;

/**
 * 租车订单接口
 */
class Reporter extends Api
{
    protected $rpcClass = "";
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    
    public function _initialize()
    {
        $this->rpcClass = explode("\\", __CLASS__)[3];
    }
    
    public function fenrunreport()
    {
        
        $params = $this->request->param();
        $date = date("Ymd");
        $historydate = false;
        if(key_exists('curdate', $params) && strcmp($params['curdate'], $date)!=0) {
            $predaystart = strtotime(date("Y-m-d", strtotime($params['curdate'])));
            $predayend = $predaystart + 86400;
            $date = date("Ymd", $predaystart);
            // 历史日期需要检查是否已经分润的
            $historydate = true;
            $histirylist = Db::name("fenrun")->where("fenrundate", "eq", $date)->select();
            $historyarr = array_column($histirylist, 'group_id');
        } else {
            $predayend = strtotime(date("Y-m-d", strtotime($date)));
            $predaystart = $predayend - 86400;
            $date = date("Ymd", $predaystart);
        }
        $orderlist = Db::name("bedorder")->order("netpoint_id")->where("endtime", "egt", $predaystart)->where("endtime", "lt", $predayend)->select();
        $netpointlist = Db::name("netpoint")->select();
        $fenrunarr = array();
        foreach ($netpointlist as $nlval) {
            $netpointobj[$nlval['id']] = $nlval;
            if($nlval['level1']) $fenrunarr[$nlval['level1']] = 0;
            if($nlval['level2']) $fenrunarr[$nlval['level2']] = 0;
            if($nlval['level3']) $fenrunarr[$nlval['level3']] = 0;
            if($nlval['level4']) $fenrunarr[$nlval['level4']] = 0;
        }
        $noupdate = true;
        foreach ($orderlist as $olval) {
            $amount = bcsub($olval['pay'], $olval['refund'], 2);
            if(bccomp($amount, 0, 2)<=0) continue;
            if($netpointobj[$olval['netpoint_id']]['level1'] && $netpointobj[$olval['netpoint_id']]['percent1']>0) {
                $fenrunarr[$netpointobj[$olval['netpoint_id']]['level1']] = bcadd($fenrunarr[$netpointobj[$olval['netpoint_id']]['level1']], $amount * $netpointobj[$olval['netpoint_id']]['percent1'] / 100, 2);
            }
            if($netpointobj[$olval['netpoint_id']]['level2'] && $netpointobj[$olval['netpoint_id']]['percent2']>0) {
                $fenrunarr[$netpointobj[$olval['netpoint_id']]['level2']] = bcadd($fenrunarr[$netpointobj[$olval['netpoint_id']]['level2']], $amount * $netpointobj[$olval['netpoint_id']]['percent2'] / 100, 2);
            }
            if($netpointobj[$olval['netpoint_id']]['level3'] && $netpointobj[$olval['netpoint_id']]['percent3']>0) {
                $fenrunarr[$netpointobj[$olval['netpoint_id']]['level3']] = bcadd($fenrunarr[$netpointobj[$olval['netpoint_id']]['level3']], $amount * $netpointobj[$olval['netpoint_id']]['percent3'] / 100, 2);
            }
            if($netpointobj[$olval['netpoint_id']]['level4'] && $netpointobj[$olval['netpoint_id']]['percent4']>0) {
                $fenrunarr[$netpointobj[$olval['netpoint_id']]['level4']] = bcadd($fenrunarr[$netpointobj[$olval['netpoint_id']]['level4']], $amount * $netpointobj[$olval['netpoint_id']]['percent4'] / 100, 2);
            }
        }
        // 将报表写入db
        Log::init(['path'=>RUNTIME_PATH . 'log' . DS . "fenrunreport". DS, 'max_files'=>3660]);
        $commitind = true;
        $counter = 0;
        try {
            Db::startTrans();
            foreach ($fenrunarr as $frakey=>$fraval) {
                if(bccomp($fraval, 0, 2)==1) {
                    if(!($historydate && in_array($frakey, $historyarr))) {
                        $add['group_id'] = $frakey;
                        $add['amount'] = round($fraval, 2);
                        $add['fenrundate'] = $date;
                        $add['createtime'] = $add['updatetime'] = time();
                        $add['status'] = 0;
                        $result = Db::name("fenrun")->insert($add);
                        if($result==false) {
                            $commitind = false;
                            break;
                        }
                    }
                    $counter++;
                }
            }
            if($commitind) {
                if($counter>0) {
                    Db::commit();
                    Log::write("log begin - 分润报表生成成功 \n");
                    Log::write(date("Y-m-d H:i:s", $predaystart));
                    Log::write(json_encode($fenrunarr));
                    Log::write(date("Y-m-d H:i:s", $predayend));
                    Log::write("log end - 分润报表生成成功 \n");
                    $this->success("分润报表生成成功", $fenrunarr);
                } else {
                    Db::commit();
                    Log::write("log begin - 无分润业绩 \n");
                    Log::write(date("Y-m-d H:i:s", $predaystart));
                    Log::write(json_encode($fenrunarr));
                    Log::write(date("Y-m-d H:i:s", $predayend));
                    Log::write("log end - 无分润业绩 \n");
                    $this->success("无分润业绩", $fenrunarr);
                }
            } else {
                Db::rollback();
                Log::write("log begin - 分润报表生成失败，写Db失败 \n");
                Log::write(date("Y-m-d H:i:s", $predaystart));
                Log::write(json_encode($fenrunarr));
                Log::write(date("Y-m-d H:i:s", $predayend));
                Log::write("log end - 分润报表生成失败，写Db失败 \n");
                $this->error("分润报表生成失败，写Db失败");
            }
        } catch (Exception $e) {
            Log::write("log begin - " . "分润报表生成失败：".$e->getMessage(). " \n");
            Log::write(date("Y-m-d H:i:s", $predaystart));
            Log::write(json_encode($fenrunarr));
            Log::write(date("Y-m-d H:i:s", $predayend));
            Log::write("log end - " . "分润报表生成失败：".$e->getMessage(). " \n");
            $this->error("分润报表生成失败：".$e->getMessage());
        }
    }
    
    public function rtnbednotice()
    {
        $method = __FUNCTION__;
        // 远程过程调用
        try {
            $client = RpcClient::instance($this->rpcClass);
            $array = $client->$method();
        } catch(Exception $e) {
            $array = format_return_array(400, '通知还床发生异常');
        }
        // 结果返回
        echo $array;
    }
    
    public function rtnbedmonitor()
    {
        $method = __FUNCTION__;
        // 远程过程调用
        try {
            $client = RpcClient::instance($this->rpcClass);
            $array = $client->$method();
        } catch(Exception $e) {
            $array = format_return_array(400, '通知还床发生异常');
        }
        // 结果返回
        echo $array;
    }
    
}
