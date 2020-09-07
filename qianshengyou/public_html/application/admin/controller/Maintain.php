<?php

namespace app\admin\controller;

use app\admin\model\AdminLog;
use app\common\controller\Backend;
use think\Exception;
use think\Db;

/**
 * 后台首页
 * @internal
 */
class Maintain extends Backend
{

    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
    }
    
    /**
     * 问题维护列表
     */
    public function issuelist()
    {
        if ($this->request->isPost()) {
            try {
                $params = $this->request->param();
                if(empty($params['token']) || empty($params['timestamp']) || empty($params['netpointid'])) $this->error("参数校验错误");
                AdminLog::setTitle(__('Maintain/issuelist'));
                $uid = $this->token_validation($this->request->post('token'), $this->request->post('timestamp'));
                if($uid===false) $this->error("token校验失败");
                $netpointid = intval($this->request->post('netpointid'));
                $netpointlist = Db::name("netpoint")->field("id, shortname, netaddr, netlng, netlat")->where("mt_admin_id", "eq", $uid)->where("id", "eq", $netpointid)->limit(1)->select();
                if(empty($netpointlist)) {
                    $this->error("您没有权限");
                } else {
                    $issuelist = Db::name("issue")->alias("a")->join("device b", "a.device_id = b.dev_id")->field("a.*, b.department, b.room, b.bed")->where("a.netpoint_id", "eq", $netpointid)->where("a.status", "neq", 9)->select();
                    $lowpowerlist = Db::name("lowpower")->where("netpoint_id", "eq", $netpointid)->where("status", "neq", 9)->where("power","lt",30)->select();
                    $this->success("获取成功", "", ["issue"=>$issuelist, "lowpower"=>$lowpowerlist], $params['netpointid']);
                }
            } catch(Exception $e) {
                $this->error("获取网点故障列表发生异常：".$e->getMessage());
            }
        }
    }
    
    public function devdetail()
    {
        if ($this->request->isPost()) {
            try {
                $params = $this->request->param();
                if(empty($params['token']) || empty($params['deviceId']) || empty($params['timestamp'])) $this->error("参数校验错误");
                AdminLog::setTitle(__('Maintain/devdetail'));
                $uid = $this->token_validation($this->request->post('token'), $this->request->post('timestamp'));
                if($uid===false) $this->error("token校验失败");
                $list = Db::name("device")->where("dev_id", "eq", intval($params['deviceId']))->field("mac, blekey, blepwd, department, room, bed")->limit(1)->select();
                if(empty($list)) {
                    $this->error("获取设备信息为空");
                } else {
                    $this->success("获取设备信息成功", "", $list[0]);
                }
            } catch(Exception $e) {
                $this->error("故障处理提交发生异常：".$e->getMessage());
            }
        }
    }
    
    /**
     * 问题处理
     */
    public function fixissue()
    {
        if ($this->request->isPost()) {
            try {
                $params = $this->request->param();
                if(empty($params['token']) || empty($params['issueid']) || empty($params['netpointid']) || empty($params['status']) || empty($params['timestamp'])) $this->error("参数校验错误");
                AdminLog::setTitle(__('Maintain/fixissue'));
                $uid = $this->token_validation($this->request->post('token'), $this->request->post('timestamp'));
                if($uid===false) $this->error("token校验失败");
                $save['status'] = intval($params['status']);
                if(key_exists('fixdesc', $params) && !empty($params['fixdesc'])) $save['fixdesc'] = $params['fixdesc'];
                $save['admin_id'] = $uid;
                $save['updatetime'] = time();
                $temp = Db::name("issue")->where("id", "eq", intval($params['issueid']))->where("netpoint_id", "eq", intval($params['netpointid']))->update($save);
                if($temp==false) {
                    if($temp===0) {
                        $this->error("故障记录不存在");
                    } else {
                        $this->error("故障处理提交失败");
                    }
                } else {
                    $this->success("故障处理提交成功", "");
                }
            } catch(Exception $e) {
                $this->error("故障处理提交发生异常：".$e->getMessage());
            }
        }
    }
    
    /**
     * 低电量处理
     */
    public function fixlowpower()
    {
        if ($this->request->isPost()) {
            try {
                $params = $this->request->param();
                if(empty($params['token']) || empty($params['lowpowerid']) || empty($params['netpointid']) || empty($params['status']) || empty($params['timestamp'])) $this->error("参数校验错误");
                AdminLog::setTitle(__('Maintain/fixlowpower'));
                $uid = $this->token_validation($this->request->post('token'), $this->request->post('timestamp'));
                if($uid===false) $this->error("token校验失败");
                $save['status'] = intval($params['status']);
                if(key_exists('fixdesc', $params) && !empty($params['fixdesc'])) $save['fixdesc'] = $params['fixdesc'];
                $save['admin_id'] = $uid;
                $save['updatetime'] = time();
                $temp = Db::name("lowpower")->where("id", "eq", intval($params['lowpowerid']))->where("netpoint_id", "eq", intval($params['netpointid']))->update($save);
                if($temp==false) {
                    if($temp===0) {
                        $this->error("低电量记录不存在");
                    } else {
                        $this->error("低电量处理提交失败");
                    }
                } else {
                    $this->success("低电量处理提交成功", "");
                }
            } catch(Exception $e) {
                $this->error("低电量处理提交发生异常：".$e->getMessage());
            }
        }
    }
    
    private function token_validation($token, $timestamp)
    {
        $length = substr($token, 32);
        $uid = substr($token, 14, $length);
        $backtoken = md5($uid.$timestamp);
        if(strcmp($token, substr($backtoken, 0 ,14).$uid.substr($backtoken, 14+$length).$length)==0) return $uid;
        return false;
    }

}
