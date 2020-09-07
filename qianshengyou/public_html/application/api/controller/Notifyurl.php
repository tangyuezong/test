<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Exception;
use app\common\library\RpcClient;

/**
 * 支付接口
 */
class Notifyurl extends Api
{
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    
    /**
     * weixin pay resync notice receiver
     */
    public function wxnotify()
    {
        // 微信支付通知xml
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        // rpc调用
        try {
            $client = RpcClient::instance("WxNotifyUrl");
            $result = $client -> notify($xml);
        } catch(Exception $e) {
            $array = format_return_array(400, '登录发生异常');
        }
        // 返回结果
        echo $result;
    }
    
}
