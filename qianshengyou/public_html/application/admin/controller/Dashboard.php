<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Config;
use think\Db;

/**
 * 控制台
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends Backend
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];
    protected $childrenGroupIds = [];
    protected $childrenAdminIds = [];
    
    public function _initialize()
    {
        parent::_initialize();
        $this->twentydayago = \fast\Date::unixtime('day', -20);
        $this->sevendayago = \fast\Date::unixtime('day', -7);
        $this->daybegin = mktime(0,0,0,date('m'),date('d'),date('Y'));
        $this->dayend = mktime(0,0,0,date('m'),date('d')+1,date('Y'));
        $this->childrenAdminIds = $this->auth->getChildrenAdminIds(true);
        $this->childrenGroupIds = $this->auth->getChildrenGroupIds(true);
    }

    /**
     * 查看
     */
    public function index()
    {
        $paylist = $createlist = [];
        for ($i = 0; $i <= 20; $i++)
        {
            $day = date("Y-m-d", $this->twentydayago + ($i * 86400));
            $createlist[$day] = 0;
            $paylist[$day] = 0;
        }
        // 用户数量
        $userlist = Db::name("users")->where("createtime", "egt", $this->twentydayago)->where("createtime", "lt", $this->dayend)->select();
        foreach ($userlist as $ulval) {
            $paylist[date("Y-m-d", $ulval['createtime'])]++;
        }
        // 订单数量
        $orderlist = Db::name("bedorder")->where("status", "egt", -1)->where("createtime", "egt", $this->twentydayago)->where("createtime", "lt", $this->dayend)->select();
        foreach ($orderlist as $olval) {
            $createlist[date("Y-m-d", $olval['createtime'])]++;
        }
        $hooks = config('addons.hooks');
        $uploadmode = isset($hooks['upload_config_init']) && $hooks['upload_config_init'] ? implode(',', $hooks['upload_config_init']) : 'local';
        $addonComposerCfg = ROOT_PATH . '/vendor/karsonzhang/fastadmin-addons/composer.json';
        Config::parse($addonComposerCfg, "json", "composer");
        $config = Config::get("composer");
        $addonVersion = isset($config['version']) ? $config['version'] : __('Unknown');
        $this->view->assign([
            'totaluser'        => 0,
            'totaldevice'      => 0,
            'totalviews'       => 0,
            'totalorder'       => 0,
            'totalorderamount' => 0,
            'todayuserlogin'   => 0,
            'todayusersignup'  => 0,
            'todayorder'       => 0,
            'unsettleorder'    => 0,
            'sevendnu'         => 0,
            'sevendau'         => 0,
            'paylist'          => $paylist,
            'createlist'       => $createlist,
            'addonversion'       => $addonVersion,
            'uploadmode'       => $uploadmode
        ]);

        return $this->view->fetch();
    }
    
    public function usercount()
    {
        $this->success("", "", Db::name("users")->count());
    }
    
    public function devcount()
    {
        //$this->success("", "", Db::name("device")->count());
        $this->success("", "", Db::name("device")->where("admin_id", "in", $this->childrenAdminIds)->count());    
    }
    
    public function orderinfo()
    {
        //$list = Db::name("bedorder")->field("(total-refund) as amount")->where("status", "egt", -1)->select();
        //$list = Db::name("bedorder")->field("(total-refund) as amount")->where("admin_id", "eq", 11)->where("status", "egt", -1)->select();
        $list = Db::name("bedorder")->field("(total-refund) as amount")->where("admin_id", "in", $this->childrenAdminIds)->where("status", "egt", -1)->select();

        $data['totalorder'] = count($list);
        $data['totalorderamount'] = round(array_sum(array_column($list, 'amount')), 2);
        $this->success("", "", $data);
    }
    
    public function todayregister()
    {
        $this->success("", "", Db::name("users")->where("createtime", "egt", $this->daybegin)->where("createtime", "lt", $this->dayend)->count());
    }
    
    public function todaylogin()
    {
        $this->success("", "", Db::name("oauth")->where("createtime", "egt", $this->daybegin)->where("createtime", "lt", $this->dayend)->count());
    }
    
    public function todayorder()
    {
        $this->success("", "", Db::name("bedorder")->where("status", "egt", -1)->where("createtime", "egt", $this->daybegin)->where("createtime", "lt", $this->dayend)->count());
    }
    
    public function unsettleorder()
    {
//         $this->success("", "", Db::name("bedorder")->where("status", "egt", -1)->where("step", "eq", 1500)->where("createtime", "egt", $this->daybegin)->where("createtime", "lt", $this->dayend)->count());
        $this->success("", "", Db::name("bedorder")->where("status", "egt", -1)->where("step", "lt", 9000)->count());
    }
    
    public function sevendnudau()
    {
        $sevenadd = Db::name("users")->where("createtime", "egt", $this->sevendayago)->where("createtime", "lt", $this->dayend)->count();
        $sevenactive = Db::name("oauth")->group("id")->where("createtime", "egt", $this->sevendayago)->where("createtime", "lt", $this->dayend)->count();
        $total = Db::name("users")->count();
        $data['dnu'] = 0;
        $data['dau'] = 0;
        if($total>0 && $sevenadd>0) $data['dnu'] = ceil($sevenadd/$total)."%";
        if($total>0 && $sevenactive>0) $data['dau'] = ceil($sevenactive/$total)."%";
        $this->success("", "", $data);
    }
    
    public function orderdata()
    {
        $data['createdata'] = Db::name("users")->where("createtime", "egt", $this->daybegin)->where("createtime", "lt", $this->dayend)->count();
        // 订单数量
        $data['paydata'] = Db::name("bedorder")->where("status", "egt", -1)->where("createtime", "egt", $this->daybegin)->where("createtime", "lt", $this->dayend)->count();
        $this->success("", "", $data);
    }
    
}
