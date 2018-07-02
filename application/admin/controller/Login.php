<?php
/**
 * Created by PhpStorm.
 * User: 15603
 * Date: 2018/5/30
 * Time: 14:18
 */

namespace app\admin\controller;


use think\Controller;


class Login extends Controller
{

    //登陆页
    public function index(){
        return $this->fetch('/login');
    }


}