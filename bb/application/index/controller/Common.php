<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class Common extends Controller
{
  
    protected function initialize()
    {
        $a=session('loginname','','userlogin');

        if (!$a) {
            $this->redirect('index/Login/index');
        }
    }

 
}
