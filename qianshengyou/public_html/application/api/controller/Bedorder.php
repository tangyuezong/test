<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Exception;
use app\common\library\RpcClient;
use think\Log;

/**
 * 租车订单接口
 */
class Bedorder extends Api
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
        $pricetype = intval($request['pricetype']);
        $pricetypearr = array(1,2,3);
        // 参数校验
        if(empty($request['access_token']) || empty($request['deviceId']) || empty($request['payment']) || empty($request['pricetype'])) {
            $array = format_return_array(400, '参数校验失败');
        } else if(!in_array($pricetype, $pricetypearr)) {
            $array = format_return_array(400, '计费类型不支持');
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
    
    public function rtnbed()
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
    
    public function pendlist()
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
                $array = format_return_array(400, '获取列表发生异常');
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
        if(empty($request['access_token'])) {
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
    
    public function assignauthtest()
    {
        $method = __FUNCTION__;
        // 接口参数
        $request = $this->request->param();
        // 参数校验
        if(empty($request['deviceId'])) {
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
        Log::info("queryauth request: ");
        Log::info(json_encode($request));
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
        Log::info("queryauth response: ");
        Log::info($array);
        // 结果返回
        echo $array;
    }
    
}