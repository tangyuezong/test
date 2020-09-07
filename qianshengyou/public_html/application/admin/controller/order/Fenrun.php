<?php

namespace app\admin\controller\order;

use app\common\controller\Backend;
use app\common\library\RpcClient;
use app\admin\library\Auth;
use think\Db;
use think\Exception;
use think\Session;

/**
 * 分润报管理
 *
 * @icon fa fa-circle-o
 */
class Fenrun extends Backend
{
    
    /**
     * Fenrun模型对象
     * @var \app\admin\model\Fenrun
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->rpcClass = explode("\\", __CLASS__)[4];
        $this->model = new \app\admin\model\Fenrun;
        $this->view->assign('statusList', $this->model->getStatusList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            $groupids = $this->auth->getChildrenGroupIds(true);
            $groupwhere['group_id'] = array('in', implode(",", $groupids));
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                    ->with(['accountset'])
                    ->where($groupwhere)
                    ->where($where)
                    ->order($sort, $order)
                    ->count();
            $list = $this->model
                    ->with(['accountset'])
                    ->where($groupwhere)
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            foreach ($list as $row) {
                $row->getRelation('accountset')->visible(['name','phone','openid','bank_name','bank_no','true_name']);
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }
    
    public function fenrunpay($ids=NULL)
    {
        $row = $this->model->get(['id' => $this->request->param("id")]);
        if (!$row)
            $this->error(__('No Results were found'));
        if ($this->request->isAjax())
        {
            $method = __FUNCTION__;
            $params = $this->request->post("row/a");
            // 远程过程调用
            try {
                $client = RpcClient::instance($this->rpcClass);
                $row['realamount'] = $params['realamount'];
                $grouplist = Db::name("auth_group")->where("id", "eq", $row['group_id'])->select();
                if(empty($grouplist) || empty($grouplist[0]['openid'])) {
                    $this->error("获取分润账号信息为空！");
                } else if(bccomp($params['realamount'], $row['amount'], 2)==1) {
                    $this->error("实际转账金额不能比分润金额大！");
                } else {
                    $row['openid'] = $grouplist[0]['openid'];
                    $row['realname'] = $grouplist[0]['realname'];
                    $row['ip'] = $this->request->ip();
                    $row['admin_id'] = Session::get("admin")['id'];
                    $array = $client->$method($row);
                    if($array['code']==0) {
                        $this->success($array['msg']);
                    } else {
                        $this->error($array['msg']);
                    }
                }
            } catch(Exception $e) {
                $this->error('分润发生异常:'.$e->getMessage());
            }
        }
        $this->view->assign("row", $row);
        $this->view->assign('groupinfo', $this->request->param());
        return $this->view->fetch();
    }
    
    public function fenrunmultipay($ids=NULL)
    {
        if ($this->request->isAjax())
        {
            $method = __FUNCTION__;
            $ip = $this->request->ip();
            $adminId = Session::get("admin")['id'];
            $params = $this->request->post("row/a");
            // 远程过程调用
            try {
                $list = Db::table("fa_fenrun")->alias("fr")->join("fa_auth_group ag", "fr.group_id = ag.id", "LEFT")->field("fr.id, fr.group_id, fr.fenrundate, fr.amount as realamount, ag.name, ag.openid, ag.realname")->where("fr.id", "in", $params['ids'])->select();
                $client = RpcClient::instance($this->rpcClass);
                $count = 0;
                foreach ($list as $row) {
                    $row['admin_id'] = $adminId;
                    $row['ip'] = $ip;
                    $array = $client->fenrunpay($row);
                    if($array['code']==0) $count++;
                }
                if($count==count($list)) {
                    $this->success("批量转账成功");
                } else {
                    $this->error($count."个转账成功，".(count($list)-$count)."个转账失败，详细查看转账列表转账备注说明");
                }
            } catch(Exception $e) {
                $this->error('分润发生异常:'.$e->getMessage());
            }
        }
        $num = 0;
        $content = "";
        $list = Db::table("fa_fenrun")->alias("fr")->join("fa_auth_group ag", "fr.group_id = ag.id", "LEFT")->field("fr.id, fr.fenrundate, fr.amount, ag.name, ag.openid, ag.realname")->where("fr.status", "eq", 0)->where("ag.openid", "neq", "")->select();
        foreach ($list as $val) {
            $content .= "分润代理：".$val['name']."，零钱账号：".$val['openid']."，实名：".$val['realname'].", 金额：".$val['amount']."元\r\n\r\n";
            $num++;
        }
        $this->view->assign("ids", implode(",", array_column($list, "id")));
        $this->view->assign("num", $num);
        $this->view->assign("content", rtrim($content, "\r\n\r\n"));
        return $this->view->fetch();
    }
}
