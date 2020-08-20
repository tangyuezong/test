<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Common extends Controller
{
      protected function initialize()
    {
         $a=session('?loginname','','login');
         if ($a!=1) {
             $this->redirect('admin/login/index');        
         }       
    }
   

    
  
}
