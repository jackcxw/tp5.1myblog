<?php
/**
 * Created by PhpStorm.
 * User: 15603
 * Date: 2018/5/30
 * Time: 14:18
 */

namespace app\admin\controller;


use think\Controller;
use \app\admin\model\Admin;


class Config extends Controller
{

    //配置页
    public function index(){

        $config = \app\index\model\Config::get(1);
        $this->assign('config',$config);
        return $this->fetch('/config');
    }

    /*
     * 管理员修改页
     * */

    public function admin(){
       $admin = Admin::where('username' ,session('admin_username'))->find();
       $admin['password'] = base64_decode($admin['password']);
       $this->assign('admin',$admin);
       return $this->fetch('/adminer');

    }


}