<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Exception;
use app\common\library\RpcClient;
use think\Db;

/**
 * 会员接口
 */
class Mall extends Api
{
    protected $rpcClass = "";
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];
    
    public function _initialize()
    {
        $this->rpcClass = explode("\\", __CLASS__)[3];
    }
    
    public function goodslist()
    {
        $method = __FUNCTION__;
        // 接口参数
        $request = $this->request->param();
        // 参数校验
        if(empty($request['access_token'])) {
            $array = format_return_array(400, '参数校验失败');
        } else {
            // 远程过程调用
            $list = Db::name("goods")->field("id, name, goods_image, price, notes")->where("status","eq",1)->select();
            if(empty($list)) {
                $array = format_return_array(400, '列表为空');
            } else {
                $array = format_return_array(0, '获取列表成功', $list);
            }
        }
        // 返回值
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
        // 返回值
        echo $array;
    }
    
    public function netpointlist()
    {
        $method = __FUNCTION__;
        // 接口参数
        $request = $this->request->param();
        // 参数校验
        if(empty($request['access_token'])) {
            $array = format_return_array(400, '参数校验失败');
        } else {
            // 远程过程调用
            $list = Db::name("netpoint")->field("id, name, shortname")->where("status","eq",1)->select();
            if(empty($list)) {
                $array = format_return_array(400, '列表为空');
            } else {
                $array = format_return_array(0, '获取列表成功', $list);
            }
        }
        // 返回值
        echo $array;
    }
    
    public function departmentlist()
    {
        $method = __FUNCTION__;
        // 接口参数
        $request = $this->request->param();
        // 参数校验
        if(empty($request['access_token']) || empty($request['netpoint_id'])) {
            $array = format_return_array(400, '参数校验失败');
        } else {
            // 远程过程调用
            $list = Db::name("device")->group("department")->field("netpoint_id, department")->where("netpoint_id","eq",intval($request['netpoint_id']))->where("status","eq",1)->where("department","neq","")->select();
            if(empty($list)) {
                $array = format_return_array(400, '列表为空');
            } else {
                $array = format_return_array(0, '获取列表成功', $list);
            }
        }
        // 返回值
        echo $array;
    }
    
    public function roomlist()
    {
        $method = __FUNCTION__;
        // 接口参数
        $request = $this->request->param();
        // 参数校验
        if(empty($request['access_token']) || empty($request['netpoint_id']) || empty($request['department'])) {
            $array = format_return_array(400, '参数校验失败');
        } else {
            // 远程过程调用
            $list = Db::name("device")->group("room")->field("room")->where("netpoint_id","eq",intval($request['netpoint_id']))->where("department","eq",$request['department'])->where("status","eq",1)->where("room","neq","")->select();
            if(empty($list)) {
                $array = format_return_array(400, '列表为空');
            } else {
                $array = format_return_array(0, '获取列表成功', $list);
            }
        }
        // 返回值
        echo $array;
    }
    
}
