<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class Index extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $a=session('loginid','','userlogin');
        $data=db('yeji')->where('uid',$a)->order('settime Desc')->select();
        $this->assign('data',$data);
       return view();
    }

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
            $data['uid']=session('loginid','','userlogin');
            $data['updatetime']=time();
            if (!db('yeji')->insert($data)) {
                $this->error('添加失败');
            }
             $this->success('添加成功','index/index/index');   
        }
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $data=db('yeji')->where('id',$id)->find();
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {

        if (!db('yeji')->delete($id)) {
            $this->error('删除失败');
        }else{
            $this->success("删除成功",'index/index/index');
        }
    }
     public function logout()
    {
        session(null,'userlogin');
        $this->success("退出成功",'index/Login/index');
    }
}
