<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Login extends Controller
{

    public function index()
    {
         $a=session('?loginname','','login');
         if ($a!=1) {
             return view();        
         } 
         $this->redirect('admin/index/index');          
    }

   
    public function login()
    {
        $data=input('post.');
        $res=db('Manager')->where('name',$data['name'])->field('Id,name,password')->find();
        if ($data['name']!=$res['name']) {
            $this->error('用户名不存在');
        }
        if ($data['password']!=$res['password']) {
             $this->error('密码错误');
        }
        session('loginname',$res['name'],'login');
        session('loginid',$res['Id'],'login');
        db('Manager')->where('id',$res['Id'])->update(['logintime'=>time()]);
        $this->success('登录成功','admin/index/index',null,1);
    }
}
