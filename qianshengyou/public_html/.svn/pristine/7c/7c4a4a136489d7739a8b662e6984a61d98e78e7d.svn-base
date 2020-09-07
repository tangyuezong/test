<?php

namespace app\admin\controller\device;

use app\common\controller\Backend;
use think\Db;
use app\common\library\RpcClient;

/**
 * 设备管理
 *
 * @icon fa fa-circle-o
 */
class Devinrepo extends Backend
{
    
    /**
     * Devinrepo模型对象
     * @var \app\admin\model\Devinrepo
     */
    protected $model = null;
    protected $dataLimit = "auth";
    protected $noNeedRight = array("roletree");

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Devinrepo;
        $this->view->assign('statusList', $this->model->getStatusList());
        $this->view->assign("iscabinetList", $this->model->getIsCabinetList());
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
                    ->with(['netpoint','devtype'])
                    ->where($where)
                    ->where('devinrepo.status', 'eq', 0)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with(['netpoint','devtype'])
                    ->where($where)
                    ->where('devinrepo.status', 'eq', 0)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            foreach ($list as $row) {
                $row->getRelation('devtype')->visible(['name']);
                $row->getRelation('netpoint')->visible(['admin_id','name']);
//                 $row->visible(['id','dev_id','name','mac','blekey','blepwd','mac2','blekey2','blepwd2','createtime','updatetime','status']);
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
            if(strcmp($row['mac'], $params['mac'])!=0) {
                if(!empty($params['mac'])) {
                    $pmacrow = $this->model->get(['mac'=>$params['mac']]);
                    $pmac2row = $this->model->get(['mac2'=>$params['mac']]);
                    if($pmacrow || $pmac2row) $this->error("修改的设备mac：".$params['mac']."，已和设备ID".$pmacrow['dev_id']."绑定");
                }
            }
            if($row['dev_id']!=$params['dev_id']) {
                $pdevidcrow = $this->model->get(['dev_id'=>$params['dev_id']]);
                if($pdevidcrow) $this->error("修改的dev_id：".$params['dev_id']."，已和mac".$pdevidcrow['mac']."绑定");
            }
            $result = $row->save($params);
            if($result===false) {
                $this->error("修改失败");
            } else {
                // 更新redis
                $client = RpcClient::instance("RedisHelper");
                $client -> hDel("airplus_hash_device_info", $row['dev_id'], false);
                $client -> hDel("airplus_hash_mac_indexto_devid", $row['mac'], false);
                $this->success("修改成功");
            }
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
    
    /**
     * 删除
     */
    public function del($ids=NULL)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row)
            $this->error(__('No Results were found'));
        if ($this->request->isAjax())
        {
            $result = $this->model->destroy(['id' => $ids]);
            if($result===false) {
                $this->error("删除失败");
            } else {
                // 更新redis
                $client = RpcClient::instance("RedisHelper");
                $client -> hDel("airplus_hash_device_info", $row['dev_id'], false);
                $client -> hDel("airplus_hash_mac_indexto_devid", $row['mac'], false);
                $this->success("删除成功");
            }
        }
    }
    
    /**
     * 批量上线
     */
    public function multionline($ids=NULL)
    {
        if ($this->request->isAjax())
        {
            $params = $this->request->post("row/a");
            $idarr = explode(",", $params['ids']);
            // 获取网点的admin_id
            $netpointModel = new \app\admin\model\Netpoint;
            $netpoint = $netpointModel->where("id", "eq", intval($params['netpoint_id']))->limit(1)->select();
            // 开始处理批量上线
            $save['price_id'] = $params['price_id'];
            $save['netpoint_id'] = $params['netpoint_id'];
//             $save['admin_id'] = $netpoint[0]['admin_id'];
            $save['admin_id'] = $params['admin_id'];
            $save['entity_id'] = $params['entity_id'];
            $save['devtype_id'] = $params['devtype_id'];
            $save['status'] = 1;
            $wherestr = "dev_id in (" . $params['ids'] .")";
            $result = Db::name('device')->where($wherestr)->update($save);
            if($result) {
                // 更新redis
                $client = RpcClient::instance("RedisHelper");
                // 删除设备缓存
                $idarr = explode(",", $params['ids']);
                foreach ($idarr as $idval) {
                    $client -> hDel("airplus_hash_device_info", $idval, false);
                }
                // 删除mac映射设备ID
                $devlist = Db::name('device')->where($wherestr)->select();
                foreach ($devlist as $mval) {
                    $client -> hDel("airplus_hash_mac_indexto_devid", $mval['mac'], false);
                }
                // 返回结果
                $this->success("批量上线成功");
            } else {
                $this->error("批量上线失败");
            }
        }
        return $this->view->fetch();
    }
    
    /**
     * 读取入库设备列表
     *
     * @internal
     */
    public function roletree()
    {
        $list = $this->model->where('status', 'eq', 0)->order(array("dev_id"))->select();
        if (count($list)>0)
        {
            foreach ($list as $v) {
                $nodeList[] = array('id' => $v['dev_id'], 'parent' => '#', 'text' => strval($v['dev_id']), 'type' => 'menu', 'state' => array('selected' => false));
            }
            $this->success('', null, $nodeList);
        }
        else
        {
            $this->error(__('Devinrepo not found'));
        }
    }
}
