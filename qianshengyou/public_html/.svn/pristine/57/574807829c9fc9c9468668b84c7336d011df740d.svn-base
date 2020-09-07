<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Exception;
use app\common\library\RpcClient;

/**
 * 租车订单接口
 */
class Cabinetorder extends Api
{
    protected $rpcClass = "";
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    
    public function _initialize()
    {
        $this->rpcClass = explode("\\", __CLASS__)[3];
    }
    
    public function submit()
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
                $array = format_return_array(400, '下单发生异常');
            }
        }
        // 结果返回
        echo $array;
    }
    
    public function queryorder()
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
                $array = format_return_array(400, '订单查询发生异常');
            }
        }
        // 结果返回
        echo $array;
    }
    
    public function rtncabinet()
    {
        $method = __FUNCTION__;
        // 接口参数
        $request = $this->request->param();
        // 参数校验
        if(empty($request['access_token']) || empty($request['order_no'])) {
            $array = format_return_array(400, '参数校验失败');
        } else {
            // 远程过程调用
            try {
                $client = RpcClient::instance($this->rpcClass);
                $array = $client->$method($request);
            } catch(Exception $e) {
                $array = format_return_array(400, '上锁退款发生异常');
            }
        }
        // 结果返回
        echo $array;
    }
    
    public function orderlist()
    {
        $method = __FUNCTION__;
        // 接口参数
        $request = $this->request->param();
        // 参数校验
        if(empty($request['access_token']) || empty($request['page'])) {
            $array = format_return_array(400, '参数校验失败');
        } else {
            // 远程过程调用
            try {
                $client = RpcClient::instance($this->rpcClass);
                $array = $client->$method($request);
            } catch(Exception $e) {
                $array = format_return_array(400, '获取列表发生异常');
            }
        }
        // 结果返回
        echo $array;
    }
    
    public function assignauth()
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
                $array = format_return_array(400, '授权开锁发生异常');
            }
        }
        // 结果返回
        echo $array;
    }
    
    public function queryauth()
    {
        $method = __FUNCTION__;
        // 接口参数
        $request = $this->request->param();
        // 参数校验
        if(empty($request['mac'])) {
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
