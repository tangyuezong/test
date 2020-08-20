<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class User extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $data=db('user')->select();
        $this->assign('data',$data);
        return view();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function add()
    {
         return view();
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        if (request()->isPost()) {
            $data=input('post.');
            $data['createtime']=time();
            $data['password']='admin';
            if (!db('user')->insert($data)) {
              $this->success('添加失败');
            }
             $this->success('添加成功','admin/user/index');
        }
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $data=db('user')->where('Id',$id)->find();
        $this->assign('data',$data);
         return view();
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request)
    {
       if (Request()->isPost()) {
        $data=$request->post();
         if (!db('user')->update($data)) {
            $this->error('修改失败');
         }
         $this->success('修改成功','user/index');
       }
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if (Request()->isGet()) {
             $res=db('user')->delete($id);
             if (!$res) {
                $this->error('删除失败');
             }
              $this->success('删除成功','user/index');
        }
       
    }
}
