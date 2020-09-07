<?php

namespace app\admin\controller\auth;

use app\common\controller\Backend;
use think\Session;
use think\Db;

/**
 * 管理员管理
 *
 * @icon fa fa-circle-o
 */
class Accountset extends Backend
{
    
    /**
     * Accountset模型对象
     * @var \app\admin\model\Accountset
     */
    protected $model = null;
    protected $noNeedLogin = "getopenid";
    protected $noNeedRight = "getopenid";

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Accountset;
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
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            $adminId = Session::get("admin")['id'];
            $groupIds = $this->auth->getGroupIds($adminId);
            $groupId = $groupIds[0];
            $groupIdArr = array_column(Db::name("auth_group_access")->where("group_id", "eq", $groupId)->select(), 'group_id');
            asort($groupIdArr, SORT_NUMERIC);
            if($groupId==$groupIdArr[0]) {
                $total = $this->model
                    ->where("id", "eq", $groupId)
                    ->count();
                $list = $this->model
                    ->where("id", "eq", $groupId)
                    ->limit(1)
                    ->select();
                $list = collection($list)->toArray();
                $result = array("total" => $total, "rows" => $list);
            } else {
                $result = array("total" => 0, "rows" => array());
            }
            return json($result);
        }
        return $this->view->fetch();
    }
    
    public function setaccount($ids=NULL)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row)
            $this->error(__('No Results were found'));
        if ($this->request->isAjax())
        {
            $params = $this->request->post("row/a");
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
    
    public function getopenid()
    {
        $phone = $this->request->param("phone");
        $list = Db::name("users")->where("phone", "eq", $phone)->limit(1)->select();
        if(empty($list)) {
            $this->error("该手机号未在小程序注册绑定，请先使用个人微信在小程序注册绑定该手机号");
        } else if(empty($list[0]['openid'])) {
            $this->error("该手机号管理的openid为空，请联系技术支持处理！");
        } else {
            $this->success("成功", null, $list[0]['openid']);
        }
    }
}
