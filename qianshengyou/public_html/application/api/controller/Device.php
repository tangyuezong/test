<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Exception;
use app\common\library\RpcClient;

/**
 * 会员接口
 */
class Device extends Api
{
    protected $rpcClass = "";
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];
    
    public function _initialize()
    {
        $this->rpcClass = explode("\\", __CLASS__)[3];
    }
    
    public function devdetail()
    {
        $method = __FUNCTION__;
        // 接口参数
        $request = $this->request->param();
        // 参数校验
        if(empty($request['deviceId']) || empty($request['access_token']) || !isset($request['iscabinet'])) {
            $array = format_return_array(400, '参数校验失败');
        } else if(!in_array(intval($request['iscabinet']), [1,2])) {
            $array = format_return_array(400, '扫码类型不支持');
        } else {
            // 远程过程调用
            try {
                $client = RpcClient::instance($this->rpcClass);
                $array = $client->$method($request);
            } catch(Exception $e) {
                $array = format_return_array(400, '获取设备信息异常');
            }
        }
        // 返回值
        echo $array;
    }
    
    public function bedIntoRepo()
    {
        $method = __FUNCTION__;
        // 接口请求参数
        $request = $this->request->param();
        // 参数校验
        if(empty($request['deviceId']) || empty($request['mac']) || empty($request['blekey']) || empty($request['blepwd'])) {
            $array = format_return_array(400, '参数校验失败');
        } else {
            try {
                $client = RpcClient::instance($this->rpcClass);
                $array = $client->$method($request);
            } catch(Exception $e) {
                $array = format_return_array(400, '陪护床设备入库发生异常');
            }
        }
        // 结果返回
        echo $array;
    }
    
    public function cabinetIntoRepo()
    {
        $method = __FUNCTION__;
        // 接口请求参数
        $request = $this->request->param();
        // 参数校验
        if(empty($request['deviceId']) || empty($request['mac']) || empty($request['blekey']) || empty($request['blepwd'])) {
            $array = format_return_array(400, '参数校验失败');
        } else {
            try {
                $client = RpcClient::instance($this->rpcClass);
                $array = $client->$method($request);
            } catch(Exception $e) {
                $array = format_return_array(400, '储物柜设备入库发生异常');
            }
        }
        // 结果返回
        echo $array;
    }
    
    public function repoDevDetail()
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
                $array = format_return_array(400, '获取入库设备发生异常');
            }
        }
        // 返回值
        echo $array;
    }
    
    public function rptstat()
    {
        $method = __FUNCTION__;
        // 接口参数
        $request = $this->request->param();
        // 参数校验
        if(empty($request['mac']) || empty($request['imei']) || empty($request['imsi']) || !isset($request['lockstat']) || !isset($request['power'])) {
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

}
