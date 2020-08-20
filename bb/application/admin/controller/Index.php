<?php

namespace app\admin\controller;
use think\Db;

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

        return view();
    }
     public function loginout()
    {
        session(null,'login');
        $this->success('退出成功','admin/login/index');
        return view();
    }

   
        //加载欢迎界面
    public function  welcome(){
            $server=[
                'HTTP_HOST'=>$_SERVER['HTTP_HOST'],
                'SERVER_SOFTWARE'=>$_SERVER['SERVER_SOFTWARE'],
                'osname'=>php_uname(),
                'HTTP_ACCEPT_LANGUAGE'=>$_SERVER['HTTP_ACCEPT_LANGUAGE'],
                'SERVER_PORT'=>$_SERVER['SERVER_PORT'],
                'SERVER_NAME'=>$_SERVER['SERVER_NAME'],
            ];
            $version=Db::query("select version()");
            $server['mysqlversion']=$version[0]['version()'];
            $server['databasename'] =config('database')['database'];
            $server['phpversion']=phpversion();
            $server['maxupload']=ini_get('max_file_uploads');
            $this->assign('server',$server);
            return view();
    }

   
    public function modify()
    {
      if (Request()->isPost()) {
        $data=input('post.');

        if ($data['repassword']!=$data['password']) {
            $this->error('俩次密码输入不一致');
        }
        $userid=session('loginid','','login');
        $res=db('manager')->where('Id',$userid)->field('Id,password')->find();
        if ($res['password']!=$data['oldpassword']) {
            $this->error('旧密码输入错误');
        }
        $result=db('manager')->where('Id',$res['Id'])->update(['password'=>$data['password']]);
        if (!$result) {
            $this->error('俩次密码相同');
        }
        $this->success('修改密码成功');

      }
       
        return view();
    }
}
