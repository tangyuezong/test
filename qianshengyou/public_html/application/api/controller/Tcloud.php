<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Exception;
use app\common\library\RpcClient;
use app\common\library\NbSecurity;
use think\Log;

/**
 * 会员接口
 */
class Tcloud extends Api
{
    protected $rpcClass = "";
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];
    
    public function _initialize()
    {
        $this->rpcClass = explode("\\", __CLASS__)[3];
    }

    public function nbDataTransfer()
    {
        $method = __FUNCTION__;
        // 接口参数
        $request = $this->request->param();
        Log::info("nbDataTransfer request: ");
        Log::info(json_encode($request));
        if(key_exists('notifyType', $request)) {
            $client = RpcClient::instance($this->rpcClass);
            $response['deviceId'] = $request['deviceId'];
            $response['command']['serviceId'] = $request['service']['serviceId'];
            $response['expireTime'] = 0;
            $response['maxRetransmit'] = 3;
            $response['callbackUrl'] = $this->request->domain() . "/api/Tcloud/nbfeedback";
            switch(intval($request['service']['data']['command'])) {
                case 1:     // 时间同步
                    $response['command']['method'] = "syncTimeResponse";
                    $response['command']['paras']['timeStamp'] = time();
                    $response['command']['paras']['time1'] = 255;
                    $response['command']['paras']['time2'] = 255;
                    $response['command']['paras']['alarmPower'] = 20;
                    $response['command']['paras']['serverMsgId'] = $request['service']['data']['serverMsgId'];
                    $array = $client->$method($response);
                    break;
                case 2:
                    // 调用数据保存接口
                    $serviceclient = RpcClient::instance("Device");
                    $result = $serviceclient->rptstat($request['service']['data']);
                    // 通过电信云给设备返回消息接口
                    $response['command']['method'] = "updateStatesResponse";
                    $response['command']['paras']['result'] = $result;
                    $response['command']['paras']['serverMsgId'] = $request['service']['data']['serverMsgId'];
                    $array = $client->$method($response);
                    break;
                case 3:
                    // 调用数据保存接口
                    $serviceclient = RpcClient::instance("Bedorder");
                    $result = $serviceclient->queryauth($request['service']['data']);
                    // 通过电信云给设备返回消息接口
                    $response['command']['method'] = "unlockResponse";
                    $response['command']['paras']['result'] = $result;
                    $response['command']['paras']['serverMsgId'] = $request['service']['data']['serverMsgId'];
                    $array = $client->$method($response);
                    break;
                case 4:
                    $array = json_encode(['result'=>"I'm fourth"]);
                    break;
                default:
                    Log::info("default return");
                    $array = ['timeStamp'=>time(), 'time1'=>1, 'time2'=>2, 'alarmPower'=>20];
                    break;
            }
            Log::info("nbDataTransfer response: ");
            Log::info(json_encode($response));
            Log::info(json_encode($array));
        }
    }
    
    public function nbfeedback()
    {
        $method = __FUNCTION__;
        // 接口参数
        $request = $this->request->param();
        Log::info("nbfeedback request: ");
        Log::info(json_encode($request));
        // 返回值
        echo 0;
    }
    
    public function nbRptActionLogs()
    {
        $method = __FUNCTION__;
        // 接口参数
        $request = $this->request->param();
        // 参数校验
        if(empty($request['loglist']) || empty($request['imei'])) {
            $array = 97;
        } else {
            // 远程过程调用
            try {
                $client = RpcClient::instance($this->rpcClass);
                $array = $client->$method($request);
            } catch(Exception $e) {
                $array = 98;
            }
        }
        // 返回值
        echo $array;
    }
    
    public function nbRptAlarmLogs()
    {
        $method = __FUNCTION__;
        // 接口参数
        $request = $this->request->param();
        // 参数校验
        if(empty($request['loglist']) || empty($request['imei'])) {
            $array = 97;
        } else {
            // 远程过程调用
            try {
                $client = RpcClient::instance($this->rpcClass);
                $array = $client->$method($request);
            } catch(Exception $e) {
                $array = 98;
            }
        }
        // 返回值
        echo $array;
    }
    
    public function nbqueryauth()
    {
        $method = __FUNCTION__;
        // 接口请求参数
        $request = $this->request->param();
        // 参数校验
        if(empty($request['imei']) || !is_string($request['imei'])) {
            $array = 97;
        } else {
            // 远程过程调用
            try {
                $client = RpcClient::instance($this->rpcClass);
                $array = $client->$method($request);
            } catch(Exception $e) {
                $array = 98;
            }
        }
        // 结果返回
        echo $array;
    }
    
    public function nbQueryTempPwd()
    {
        $method = __FUNCTION__;
        // 接口请求参数
        $request = $this->request->param();
        // 参数校验
        if(empty($request['imei'])) {
            $array = 97;
        } else {
            // 远程过程调用
            try {
                $client = RpcClient::instance($this->rpcClass);
                $array = $client->$method($request);
            } catch(Exception $e) {
                $array = 98;
            }
        }
        // 结果返回
        echo $array;
    }
    
    public function nbUpdateTempPwd()
    {
        $method = __FUNCTION__;
        // 接口请求参数
        $request = $this->request->param();
        // 参数校验
        if(empty($request['id']) || !isset($request['flag'])) {
            $array = 97;
        } else {
            // 远程过程调用
            try {
                $client = RpcClient::instance($this->rpcClass);
                $array = $client->$method($request);
            } catch(Exception $e) {
                $array = 98;
            }
        }
        // 结果返回
        echo $array;
    }
    
}
