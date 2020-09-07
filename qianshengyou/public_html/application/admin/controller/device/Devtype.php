<?php

namespace app\admin\controller\device;

use app\common\controller\Backend;
use app\common\library\RpcClient;

/**
 * 设备类型管理
 *
 * @icon fa fa-circle-o
 */
class Devtype extends Backend
{
    
    /**
     * Devtype模型对象
     * @var \app\admin\model\Devtype
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Devtype;
        $this->view->assign('statusList', $this->model->getStatusList());
        $this->view->assign('typeList', $this->model->getTypeList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    
    public function add()
    {
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            if ($params)
            {
                $type = 0;
                $typearr = $this->request->post("type/a");
                if(in_array(1, $typearr)) $type |= 1;
                if(in_array(2, $typearr)) $type |= 2;
                if(in_array(4, $typearr)) $type |= 4;
                if(in_array(8, $typearr)) $type |= 8;
                if($type>0) {
                    $params['type'] = $type;
                    $result = $this->model->save($params);
                    $result = true;
                    if($result) {
                        $this->success("新增成功");
                    } else {
                        $this->error("新增失败");
                    }
                } else {
                    $this->error("请选择设备支持的属性！");
                }
            } else {
                $this->error("请选择类型名称和状态！");
            }
        }
        return $this->view->fetch();
    }
    
    public function edit($ids=NULL)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row)
            $this->error(__('No Results were found'));
        if ($this->request->isAjax())
        {
            
            $params = $this->request->post("row/a");
            if ($params)
            {
                $type = 0;
                $typearr = $this->request->post("type/a");
                if(in_array(1, $typearr)) $type |= 1;
                if(in_array(2, $typearr)) $type |= 2;
                if(in_array(4, $typearr)) $type |= 4;
                if(in_array(8, $typearr)) $type |= 8;
                if($type>0) {
                    $params['type'] = $type;
                    $result = $row->save($params);
                    if($result) {
                        // 更新redis
                        $client = RpcClient::instance("RedisHelper");
                        $client -> hDel("airplus_hash_devtype_info", $row['id'], false);
                        $this->success("修改成功");
                    } else {
                        $this->error("修改失败");
                    }
                } else {
                    $this->error("请选择设备支持的属性！");
                }
            } else {
                $this->error("请选择类型名称和状态！");
            }
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

}
