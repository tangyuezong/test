<?php

namespace app\admin\controller\users;

use app\common\controller\Backend;
use app\common\library\RpcClient;
use think\Exception;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Deposit extends Backend
{
    
    /**
     * Deposit模型对象
     * @var \app\admin\model\Deposit
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->rpcClass = explode("\\", __CLASS__)[4];
        $this->model = new \app\admin\model\Deposit;
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
                    ->with(['users'])
                    ->where("item", "eq", 3)
                    ->where($where)
                    ->order($sort, $order)
                    ->count();
            $list = $this->model
                    ->with(['users'])
                    ->where("item", "eq", 3)
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            foreach ($list as $row) {
                $row->getRelation('users')->visible(['phone', 'deposit']);
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }
    
    public function refund($ids=NULL)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row)
            $this->error(__('No Results were found'));
        if ($this->request->isAjax())
        {
            // 远程过程调用
            try {
                $method = __FUNCTION__;
                $request['backendind'] = 1;
                $request['uid'] = $row['uid'];
                $client = RpcClient::instance($this->rpcClass);
                $array = json_decode($client->$method($request), true);
                if($array['code']==0) {
                    $this->success($array['msg']);
                } else {
                    $this->error($array['msg']);
                }
            } catch(Exception $e) {
                $this->error('后台还床发生异常:'.$e->getMessage());
            }
        }
    }
}
