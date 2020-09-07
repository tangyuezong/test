<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Exception;
use app\common\library\RpcClient;

/**
 * 广告接口
 */
class Advertise extends Api
{
    protected $rpcClass = "";
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];
    
    public function _initialize()
    {
        $this->rpcClass = explode("\\", __CLASS__)[3];
    }
    
    /**
     * 广告页活动接口
     */
    public function adverlist()
    {
        $method = __FUNCTION__;
        // 接口参数
        $request = $this->request->param();
        $request['domain'] = $this->request->domain();
        // 远程过程调用
        try {
            $client = RpcClient::instance($this->rpcClass);
            $array = $client->$method($request);
        } catch(Exception $e) {
            $array = format_return_array(400, '广告活动发生异常');
        }
        // 结果返回
        echo $array;
    }

}
