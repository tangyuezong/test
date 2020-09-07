<?php

namespace app\admin\controller\device;

use app\common\controller\Backend;
use app\common\library\RpcClient;

/**
 * 价格管理
 *
 * @icon fa fa-circle-o
 */
class Price extends Backend
{
    
    /**
     * Price模型对象
     * @var \app\admin\model\Price
     */
    protected $model = null;
    protected $dataLimit = "auth";

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Price;
        $this->view->assign("statusList", $this->model->getStatusList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    
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
                $client -> hDel("airplus_hash_price_info", $row['id'], false);
                $this->success("修改成功");
            }
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

}
