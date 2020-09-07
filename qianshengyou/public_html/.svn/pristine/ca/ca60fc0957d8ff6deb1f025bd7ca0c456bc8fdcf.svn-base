<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Exception;
use app\common\library\RpcClient;

/**
 * 故障接口
 */
class Issue extends Api
{
    protected $rpcClass = "";
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];
    
    public function _initialize()
    {
        $this->rpcClass = explode("\\", __CLASS__)[3];
    }
    
    /**
     * 故障上报
     */
    public function rptissue()
    {
        $method = __FUNCTION__;
        // 接口参数
        $request = $this->request->param();
        if(empty($request['deviceId'])) {
            $array = format_return_array(400, '参数校验失败');
        } else {
            // 远程过程调用
            try {
                $client = RpcClient::instance($this->rpcClass);
                $array = $client->$method($request);
            } catch(Exception $e) {
                $array = format_return_array(400, '故障上报发生异常');
            }
        }
        // 结果返回
        echo $array;
    }
    
    /**
     * 低电量上报
     */
    public function rptlowpower()
    {
        $method = __FUNCTION__;
        // 接口参数
        $request = $this->request->param();
        if(empty($request['access_token']) || empty($request['deviceId']) || empty($request['power'])) {
            $array = format_return_array(400, '参数校验失败');
        } else {
            // 远程过程调用
            try {
                $client = RpcClient::instance($this->rpcClass);
                $array = $client->$method($request);
            } catch(Exception $e) {
                $array = format_return_array(400, '低电量上报发生异常');
            }
        }
        // 结果返回
        echo $array;
    }
    
    /**
     * 故障反馈列表
     */
    public function issuelist()
    {
        $method = __FUNCTION__;
        // 接口参数
        $request = $this->request->param();
        if(empty($request['access_token'])) {
            $array = format_return_array(400, '参数校验失败');
        } else {
            // 远程过程调用
            try {
                $client = RpcClient::instance($this->rpcClass);
                $array = $client->$method($request);
            } catch(Exception $e) {
                $array = format_return_array(400, '获取故障列表异常');
            }
        }
        // 结果返回
        echo $array;
    }

}
