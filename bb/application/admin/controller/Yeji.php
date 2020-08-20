<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Yeji extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $data=db('yeji')->where('state',0)->order('updatetime Desc')->select();
        $this->assign('data',$data);
        return view();
    }

    /**
     * 显示待审核资源表单页.
     *
     * @return \think\Response
     */
    public function shenhe(Request $request)
    {
        $id=input('Id');
         $sta=input('sta');
         $settime=time();
        $res=db('yeji')->where('Id',$id)->update(['state'=>$sta, 'settime'=> $settime]);
        return;
       
    }

     /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function lst()
    {
        $data=db('yeji')->order('updatetime Desc')->select();
        $this->assign('data',$data);
        return view();
       
    }
    public function gzff()
    {
         
        $data=db('yeji')->alias('a')
                        ->join('user b','a.uid=b.Id')
                        ->group('a.uid')
                        ->where('a.state','in',[1,2])
                        ->field('a.*,a.id as yejiid,a.state as yejistate,b.name,b.state as userstate,b.Id as userid,b.age,sum(a.yeji)*b.ticheng/100 as heji')
                        ->order('a.updatetime Desc')
                        
                        ->select();

        $this->assign('data',$data);
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
        //
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
        //
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
        //
    }
}
