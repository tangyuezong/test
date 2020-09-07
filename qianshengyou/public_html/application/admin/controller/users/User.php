<?php

namespace app\admin\controller\users;

use app\common\controller\Backend;
use app\common\library\RpcClient;

/**
 * 
 *
 * @icon fa fa-users
 */
class User extends Backend
{
    
    /**
     * Users模型对象
     * @var \app\admin\model\Users
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Users;
        $this->view->assign("statusList", $this->model->getStatusList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    
    public function del($ids=null)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row)
            $this->error(__('No Results were found'));
        if ($this->request->isPost())
        {
            // 删除缓存
            $client = RpcClient::instance("User");
            $result = $client -> clearusercache($row);
            if($result) {
                // 删除记录
                $count = $this->model->where('id', 'in', $ids)->delete();
                if ($count)
                {
                    $this->success();
                } else {
                    $this->error();
                }
            } else {
                $this->error("用户存在绑定的设备，不可删除");
            }
        }
    }

}
