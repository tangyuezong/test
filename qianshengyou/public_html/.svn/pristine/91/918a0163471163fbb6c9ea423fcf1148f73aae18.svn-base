<?php

namespace app\admin\controller\device;

use app\common\controller\Backend;
use app\common\library\RpcClient;
use think\Db;

/**
 * 网点管理
 *
 * @icon fa fa-circle-o
 */
class Netpoint extends Backend
{
    
    /**
     * Netpoint模型对象
     * @var \app\admin\model\Netpoint
     */
    protected $model = null;
    protected $dataLimit = "auth";
    protected $noNeedLogin = "groupinfo";
    protected $noNeedRight = "groupinfo";

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Netpoint;
        $this->view->assign("statusList", $this->model->getStatusList());
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
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                    ->with(['admin', 'mtadmin', 'malladmin'])
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with(['admin', 'mtadmin', 'malladmin'])
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            foreach ($list as $row) {
                
                $row->getRelation('admin')->visible(['username','nickname','email']);
                $row->getRelation('mtadmin')->visible(['nickname']);
                $row->getRelation('malladmin')->visible(['nickname']);
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }
    
    /**
     * 修改
     */
    public function edit($ids=NULL)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row)
            $this->error(__('No Results were found'));
        if ($this->request->isAjax())
        {
            $params = $this->request->post("row/a");
            $result = $row->save($params);
            if($result===false) {
                $this->error("修改失败");
            } else {
                // 更新redis
                $client = RpcClient::instance("RedisHelper");
                $client -> hDel("airplus_hash_netpoint_info", $row['id'], false);
                $this->success("修改成功");
            }
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
    
    public function setpercent($ids=NULL)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row)
            $this->error(__('No Results were found'));
        if ($this->request->isAjax())
        {
            $params = $this->request->param();
            if(!is_numeric($params['percent1']) || !is_numeric($params['percent2'])) $this->error("分润比例必须为数字");
            $totalpercent = intval($params['percent1']) + intval($params['percent2']);
            if(key_exists('level3', $params) && !empty($params['percent3'])) {
                if(!is_numeric($params['percent3'])) $this->error("分润比例必须为数字");
                $totalpercent += intval($params['percent3']);
                if(key_exists('level4', $params) && !empty($params['percent4'])) {
                    if(!is_numeric($params['percent4'])) $this->error("分润比例必须为数字");
                    $totalpercent += intval($params['percent4']);
                } else {
                    unset($params['level4']);
                    unset($params['percent4']);
                }
            } else {
                unset($params['level3']);
                unset($params['level4']);
                unset($params['percent3']);
                unset($params['percent4']);
            }
            if($totalpercent!=100) $this->error("分润比例总和必须为100");
            unset($params['dialog']);
            unset($params['ids']);
            $result = $row->save($params);
            if($result) {
                $this->success("设置成功");
            } else {
                $this->error("设置失败");
            }
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
    
    public function setmaintain($ids=NULL)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row)
            $this->error(__('No Results were found'));
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            $params = $this->request->post("row/a");
            $result = $row->save($params);
            if($result || $result===0) {
                $this->success("设置成功");
            } else {
                $this->error("设置失败");
            }
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
    
    public function setmall($ids=NULL)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row)
            $this->error(__('No Results were found'));
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            $params = $this->request->post("row/a");
            $result = $row->save($params);
            if($result || $result===0) {
                $this->success("设置成功");
            } else {
                $this->error("设置失败");
            }
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
    
    public function groupinfo()
    {
        $this->success($this->request->param("id"), null, Db::name("auth_group")->where("id", "eq", $this->request->param("id"))->limit(1)->select());
    }
    
}
