<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class Login extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $a=session('?loginname','','userlogin');
         if ($a!=1) {
             return view();        
         } 
         $this->redirect('index/login/index');       
      
    }

   public function login()
    {
        $data=input('post.');
        $res=db('user')->where('name',$data['name'])->field('Id,name,password')->find();
        if ($data['name']!=$res['name']) {
            $this->error('用户名不存在');
        }
        if ($data['password']!=$res['password']) {
             $this->error('密码错误');
        }
        session('loginname',$res['name'],'userlogin');
        session('loginid',$res['Id'],'userlogin');
        $this->success('登录成功','index/index/index',null,1);
    }
}
