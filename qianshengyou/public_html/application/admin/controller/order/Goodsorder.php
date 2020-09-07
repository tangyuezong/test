<?php

namespace app\admin\controller\order;

use app\common\controller\Backend;
use app\common\library\RpcClient;
use think\Session;
use think\Exception;
use think\Db;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Goodsorder extends Backend
{
    
    /**
     * Goodsorder模型对象
     * @var \app\admin\model\Goodsorder
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->rpcClass = explode("\\", __CLASS__)[4];
        $this->model = new \app\admin\model\Goodsorder;
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
            $childAdminIds = $this->auth->getChildrenAdminIds(true);
            $pointlist = Db::name("netpoint")->field("id")->where("mall_admin_id", "in", implode(",", $childAdminIds))->select();
            if(count($pointlist)>0) {
                $ids = implode(",", array_column($pointlist, 'id'));
                list($where, $sort, $order, $offset, $limit) = $this->buildparams();
                $total = $this->model
                        ->with(['goods','users','netpoint','trans'])
                        ->where("goodsorder.netpoint_id", "in", $ids)
                        ->where($where)
                        ->order($sort, $order)
                        ->count();
                $list = $this->model
                        ->with(['goods','users','netpoint','trans'])
                        ->where("goodsorder.netpoint_id", "in", $ids)
                        ->where($where)
                        ->order($sort, $order)
                        ->limit($offset, $limit)
                        ->select();
                foreach ($list as $row) {
                    $row->getRelation('goods')->visible(['name','goods_image']);
    				$row->getRelation('users')->visible(['phone','nickname']);
    				$row->getRelation('netpoint')->visible(['name','shortname']);
    				$row->getRelation('trans')->visible(['payment','item','tran_id','tran_time']);
                }
                $list = collection($list)->toArray();
                $result = array("total" => $total, "rows" => $list);
            } else {
                $result = array("total" => 0, "rows" => []);
            }
            return json($result);
        }
        return $this->view->fetch();
    }
    
    /**
     * 确认商品已派送
     */
    public function passgood($ids=NULL)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row)
            $this->error(__('No Results were found'));
        if ($this->request->isAjax())
        {
            try {
                $save['status'] = 9;
                $save['admin_id'] = Session::get("admin")['id'];
                $save['updatetime'] = time();
                $result = $row->save($save);
                if($result===false) {
                    $this->error('操作失败');
                } else {
                    $this->success('操作成功');
                }
            } catch(Exception $e) {
                $this->error('确认已派送商品发生异常:'.$e->getMessage());
            }
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
    
    public function cancel()
    {
        $row = $this->model->get(['id' => $this->request->param('id')]);
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
            // 判断金额
            if(bccomp($params['amount'], 0, 2)<=0) {
                $this->error("退款金额必须大于0");
            } else if(bccomp($params['amount'], bcsub($row['total'], $row['refund'], 2), 2)==1) {
                $this->error("退款金额不可超过剩余总金额");
            }
            // 取消订单
            try {
                $save['status'] = 99999;
                $save['refund'] = bcadd($row['refund'], $params['amount'], 2);
                $save['refundnote'] = "订单已取消，并发起退款，待退款成功更新状态";
                $save['admin_id'] = Session::get("admin")['id'];
                $save['updatetime'] = time();
                $result = $row->save($save);
                if($result===false) {
                    $this->error('操作失败');
                } else {
                    $request['backendind'] = 1;
                    $request['order_no'] = $row['order_no'];
                    $request['users_id'] = $row['users_id'];
                    $request['total'] = $row['total'];
                    $request['refundamount'] = $params['amount'];
                    $request['refunded'] = $save['refund'];
                    $client = RpcClient::instance($this->rpcClass);
                    $rpcresult = json_decode($client->refund($request), true);
                    if($rpcresult['code']===0) {
                        $this->success($rpcresult['msg']);
                    } else {
                        $this->error($rpcresult['msg']);
                    }
                }
            } catch(Exception $e) {
                $this->error('取消商品订单发生异常:'.$e->getMessage());
            }
            
//             try {
//                 $['backendind'] = 1;     // 标识后台操作还床
//                 $request['order_no'] = $row['order_no'];
//                 $request['users_id'] = $row['users_id'];
//                 $request['admin_id'] = Session::get("admin")['id'];
//                 $client = RpcClient::instance($this->rpcClass);
//                 $array = json_decode($client->$method($request), true);
//                 if($array['code']==0) {
//                     $this->success($array['msg']);
//                 } else {
//                     $this->error($array['msg']);
//                 }
//             } catch(Exception $e) {
//                 $this->error('取消商品订单发生异常:'.$e->getMessage());
//             }
        }
        $row['phone'] = $this->request->param('phone');
        $row['netpoint'] = $this->request->param('netpoint');
        $row['goodsname'] = $this->request->param('goodsname');
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
    
}