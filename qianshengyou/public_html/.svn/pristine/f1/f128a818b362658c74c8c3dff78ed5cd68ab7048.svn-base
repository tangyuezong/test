<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Exception;
use app\common\library\RpcClient;

/**
 * 押金接口
 */
class Deposit extends Api
{
    protected $rpcClass = "";
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    
    public function _initialize()
    {
        $this->rpcClass = explode("\\", __CLASS__)[3];
    }
    
    public function pay()
    {
        $method = __FUNCTION__;
        // 接口参数
        $request = $this->request->param();
        $request['domain'] = $this->request->domain();
        // 参数校验
        if(empty($request['access_token']) || empty($request['deviceId'])) {
            $array = format_return_array(400, '参数校验失败');
        } else {
            // 远程过程调用
            try {
                $client = RpcClient::instance($this->rpcClass);
                $array = $client->$method($request);
            } catch(Exception $e) {
                $array = format_return_array(400, '支付押金发生异常');
            }
        }
        // 结果返回
        echo $array;
    }
    
    public function refund()
    {
        $method = __FUNCTION__;
        // 接口参数
        $request = $this->request->param();
        // 参数校验
        if(empty($request['access_token'])) {
            $array = format_return_array(400, '参数校验失败');
        } else {
            // 远程过程调用
            try {
                $client = RpcClient::instance($this->rpcClass);
                $array = $client->$method($request);
            } catch(Exception $e) {
                $array = format_return_array(400, '押金退款发生异常');
            }
        }
        // 结果返回
        echo $array;
    }
    
    public function querydeposit()
    {
        $method = __FUNCTION__;
        // 接口参数
        $request = $this->request->param();
        // 参数校验
        if(empty($request['access_token'])) {
            $array = format_return_array(400, '参数校验失败');
        } else {
            // 远程过程调用
            try {
                $client = RpcClient::instance($this->rpcClass);
                $array = $client->$method($request);
            } catch(Exception $e) {
                $array = format_return_array(400, '查询押金发生异常');
            }
        }
        // 结果返回
        echo $array;
    }
    
}
