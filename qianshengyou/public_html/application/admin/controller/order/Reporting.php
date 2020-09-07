<?php

namespace app\admin\controller\order;

use app\common\controller\Backend;
use app\admin\controller\order\ReportInfo;
use think\Db;

/**
 * 陪护床订单管理
 *
 * @icon fa fa-circle-o
 */
class Reporting extends Backend
{
    
    /**
     * Reporting模型对象
     * @var \app\admin\model\Reporting
     */
    protected $model = null;
    protected $dataLimit = "auth";
    protected $noNeedRight = ['nepointsearch'];
    
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Reporting;
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    public function index()
    {
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            $days = 0;
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
//             $total = $this->model
//                 ->where($where)
//                 ->order($sort, $order)
//                 ->count();
            $list = $this->model
                ->where("netpoint_id", "gt", 0)
                ->where($where)
                ->order($sort, $order)
                ->select();
            $list = collection($list)->toArray();
            if(empty($list)) {
                $result = array("total" => 0, "rows" => []);
            } else {
                if(key_exists('filter', $this->request->param())) {
                    $filterarr = json_decode($this->request->param('filter'), true);
                    if(key_exists('endtime', $filterarr)) {
                        $timearr = explode(" - ", $filterarr['endtime']);
                        $days = ceil((strtotime($timearr[1]) - strtotime($timearr[0]))/86400);
                    }
                }
                if($days==0) $days = ceil(abs($list[0]['endtime'] - $list[count($list)-1]['endtime'])/86400);
                $netpointidarr = array_unique(array_column($list, 'netpoint_id'));
                $netpointarr = Db::name("netpoint")->field("id, name")->where("id", "in", $netpointidarr)->select();
                $reportarr = [];
                $reportinfo = new ReportInfo();
                $report = get_object_vars($reportinfo);
                foreach ($netpointarr as $val) {
                    $report['netpoint_id'] = $val['id'];
                    $report['netpoint_name'] = $val['name'];
                    $report['devicenums'] = Db::name("device")->where(['netpoint_id'=>$val['id']])->count();
                    $reportarr[$val['id']] = $report;
                }
                foreach ($list as $val) {
                    if($val['netpoint_id']==$reportarr[$val['netpoint_id']]['netpoint_id']) {
                        if(bccomp($val['pay'], $val['refund'], 2)==0) {
                            $reportarr[$val['netpoint_id']]['freeorders']++;
                        } else {
                            $income = bcsub($val['pay'], $val['refund'], 2);
                            $reportarr[$val['netpoint_id']]['totalorders']++;
                            $reportarr[$val['netpoint_id']]['totalamount'] = bcadd($reportarr[$val['netpoint_id']]['totalamount'], $income, 2);
                            switch($val['pricetype']) {
                                case 1:
                                    $reportarr[$val['netpoint_id']]['hourorders']++;
                                    $reportarr[$val['netpoint_id']]['houramount'] = bcadd($reportarr[$val['netpoint_id']]['houramount'], $income, 2);
                                    break;
                                case 2:
                                    $reportarr[$val['netpoint_id']]['timesorders']++;
                                    $reportarr[$val['netpoint_id']]['timesamount'] = bcadd($reportarr[$val['netpoint_id']]['timesamount'], $income, 2);
                                    break;
                                case 3:
                                    $reportarr[$val['netpoint_id']]['dayorders']++;
                                    $reportarr[$val['netpoint_id']]['dayamount'] = bcadd($reportarr[$val['netpoint_id']]['dayamount'], $income, 2);
                                    break;
                            }
                            if($val['lightnight']==1) {      // 日间
                                $reportarr[$val['netpoint_id']]['lightorders']++;
                                $reportarr[$val['netpoint_id']]['lightamount'] = bcadd($reportarr[$val['netpoint_id']]['lightamount'], $income, 2);
                            } else {
                                $reportarr[$val['netpoint_id']]['nightorders']++;
                                $reportarr[$val['netpoint_id']]['nightamount'] = bcadd($reportarr[$val['netpoint_id']]['nightamount'], $income, 2);
                            }
                        }
                    }
                }
                foreach ($reportarr as $val) {
                    if($val['devicenums']==0 || $days==0) {
                        if($val['devicenums']==0) {
                            $val['userate'] = "无效，设备数为0";
                            $val['incomerate'] = "无效，设备数为0";
                        } else {
                            $val['userate'] = "无效，天数为0";
                            $val['incomerate'] = "无效，天数为0";
                        }
                    } else {
                        $val['userate'] = round($val['totalorders'] / $val['devicenums'] / $days, 4) * 100 . "%";
                        $val['incomerate'] = round($val['totalamount'] / $val['devicenums'] / $days, 3) . "元/台/天";
                    }
                    $disparr[] = $val;
                }
                $result = array("total" => count($disparr), "rows" => $disparr, "days"=>$days);
            }
            return json($result);
        }
        return $this->view->fetch();
    }
    
    public function nepointsearch()
    {
        $netpointarr = [];
        $netpointlist = Db::name("netpoint")->field("id, name")->where("admin_id", "in", $this->auth->getChildrenAdminIds(true))->select();
        foreach ($netpointlist as $val) {
            $netpointarr[$val['id']] = $val['name'];
        }
        return $netpointarr;
    }

}