<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Exception;
use app\common\library\RpcClient;

/**
 * 会员接口
 */
class User extends Api
{
    protected $rpcClass= "";
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];
    
    public function _initialize()
    {
        $this->rpcClass = explode("\\", __CLASS__)[3];
    }
    
    /*
     * 发送验证码
     */
    public function authCode()
    {
        $method = __FUNCTION__;
        // 接口参数
        $request = $this->request->param();
        // 参数校验
        if(empty($request['mobile']) || empty($request['clientId']) || empty($request['sign']) || empty($request['timestamp'])) {
            $array = format_return_array(400, '参数校验失败');
        } else {
            // 远程过程调用
            try {
                $client = RpcClient::instance($this->rpcClass);
                $array = $client->$method($request);
            } catch(Exception $e) {
                $array = format_return_array(400, '验证码发生异常');
            }
        }
        // 结果返回
        echo $array;
    }
    
    /**
     * 用户注册
     */
    public function wxlogin()
    {
        $method = __FUNCTION__;
        // 接口请求参数
        $request = $this->request->param();
        $request['clientip'] = $this->request->ip();
        // 参数校验
        if(empty($request['code'])) {
            $array = format_return_array(400, '参数校验失败');
        } else {
            // 远程过程调用
            try {
                $client = RpcClient::instance($this->rpcClass);
                $array = $client->$method($request);
            } catch(Exception $e) {
                $array = format_return_array(400, '注册发生异常');
            }
        }
        // 结果返回
        echo $array;
    }
    
    /**
     * 发送验证码
     */
    public function vericode()
    {
        $method = __FUNCTION__;
        // 接口请求参数
        $request = $this->request->param();
        $request['ip'] = $this->request->ip();
        // 参数校验
        if(empty($request['access_token']) || empty($request['phone'])) {
            $array = format_return_array(400, '参数校验失败');
        } else {
            // 远程过程调用
            try {
                $client = RpcClient::instance($this->rpcClass);
                $array = $client->$method($request);
            } catch(Exception $e) {
                $array = format_return_array(400, '发送验证码异常');
            }
        }
        // 结果返回
        echo $array;
    }
    
    /**
     * 绑定手机
     */
    public function bindphone()
    {
        $method = __FUNCTION__;
        // 接口请求参数
        $request = $this->request->param();
        // 参数校验
        if(empty($request['access_token']) || empty($request['phone']) || empty($request['vericode'])) {
            $array = format_return_array(400, '参数校验失败');
        } else {
            // 远程过程调用
            try {
                $client = RpcClient::instance($this->rpcClass);
                $array = $client->$method($request);
            } catch(Exception $e) {
                $array = format_return_array(400, '绑定手机发生异常');
            }
        }
        // 结果返回
        echo $array;
    }
    
    /**
     * 绑定手机
     */
    public function wxbindphone()
    {
        $method = __FUNCTION__;
        // 接口请求参数
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
                $array = format_return_array(400, '绑定手机发生异常');
            }
        }
        // 结果返回
        echo $array;
    }
    
    /**
     * 更新用户信息
     */
    public function updateinfo()
    {
        $method = __FUNCTION__;
        // 接口请求参数
        $request = $this->request->param();
        $request['domain'] = $this->request->domain();
        // 参数校验
        if(empty($request['access_token'])) {
            $array = format_return_array(400, '参数校验失败');
        } else {
            // 远程过程调用
            try {
                $client = RpcClient::instance($this->rpcClass);
                $array = $client->$method($request);
            } catch(Exception $e) {
                $array = format_return_array(400, '注册发生异常');
            }
        }
        // 结果返回
        echo $array;
    }
    
    public function userInfo()
    {
        $method = __FUNCTION__;
        // 接口请求参数
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
                $array = format_return_array(400, '获取用户信息发生异常');
            }
        }
        // 结果返回
        echo $array;
    }
    
    public function logout()
    {
        $method = __FUNCTION__;
        // 接口请求参数
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
                $array = format_return_array(400, '登录退出发生异常');
            }
        }
        // 结果返回
        echo $array;
    }

}
