<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Exception;
use app\common\library\RpcClient;

/**
 * 订单接口
 */
class Goodsorder extends Api
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
        if(empty($request['access_token']) || empty($request['goodsId']) || empty($request['num']) || empty($request['netpoint_id']) || empty($request['department']) || empty($request['room'])) {
            $array = format_return_array(400, '参数校验失败');
        } else {
            // 远程过程调用
            try {
                $client = RpcClient::instance($this->rpcClass);
                $array = $client->$method($request);
            } catch(Exception $e) {
                $array = format_return_array(400, '购买商品发生异常');
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
    
}
