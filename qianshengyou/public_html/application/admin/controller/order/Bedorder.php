<?php

namespace app\admin\controller\order;

use app\common\controller\Backend;
use app\common\library\RpcClient;
use think\Session;
use think\Exception;

/**
 * 陪护床订单管理
 *
 * @icon fa fa-circle-o
 */
class Bedorder extends Backend
{
    
    /**
     * Bedorder模型对象
     * @var \app\admin\model\Bedorder
     */
    protected $dataLimit = "auth";
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->rpcClass = explode("\\", __CLASS__)[4];
        $this->model = new \app\admin\model\Bedorder;
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign("lightnightList", $this->model->getLightnightList());
        $this->view->assign("pricetypeList", $this->model->getPricetypeList());
//         $transModel = new Trans();
//         $this->view->assign("paymentList", $transModel->getPaymentList());
//         $this->view->assign("itemList", $transModel->getItemList());
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
//                     ->with(['users','netpoint','entity','device','trans'])
                    ->with(['netpoint','device'])
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
//                     ->with(['users','netpoint','entity','device','trans'])
                    ->with(['netpoint','device'])
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            foreach ($list as $row) {
                
//                 $row->getRelation('users')->visible(['nickname']);
				$row->getRelation('netpoint')->visible(['name','shortname']);
// 				$row->getRelation('entity')->visible(['name']);
				$row->getRelation('device')->visible(['department','room','bed','mac']);
// 				$row->getRelation('trans')->visible(['payment','item','tran_id','tran_time']);
            }
            $list = collection($list)->toArray();
            // 表格计算
//             $calclist = $this->model->field("total, refund, step, pricetype, status, lightnight")->with(['netpoint'=>function($query){$query->withField("name, shortname, status");}, 'device'=>function($query){$query->withField("department, room, bed, mac, iscabinet, status");}])->where($where)->where("bedorder.step", "gt", 1500)->select();
//             $calclist = collection($calclist)->toArray();
//             $data['totalorder'] = count($calclist);
//             $data['totalorderamount'] = round(array_sum(array_column($calclist, 'total')) - array_sum(array_column($calclist, 'refund')), 2);
//             $result = array("total" => $total, "rows" => $list, "extend" => $data);
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }
    
    /**
     * 后台还床
     */
    public function rtnbed()
    {
        $row = $this->model->get(['order_no' => $this->request->param("order_no")]);
        if (!$row)
            $this->error(__('No Results were found'));
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            $method = __FUNCTION__;
            // 远程过程调用
            try {
                $request['backendind'] = 1;     // 标识后台操作还床
                $request['order_no'] = $row['order_no'];
                $request['users_id'] = $row['users_id'];
                $request['rtn_admin_id'] = Session::get("admin")['id'];
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
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
}
