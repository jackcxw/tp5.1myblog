<?php
/**
 * Created by PhpStorm.
 * User: 15603
 * Date: 2018/5/31
 * Time: 9:45
 */

namespace app\admin\controller;


use Composer\Config;
use think\Controller;

class Base extends Controller
{
    protected $username;
    protected function initialize()
    {
        parent::initialize();
        //判断登陆
        if(!session('?admin_username')){
            $this->redirect('/admin/login');
        };
        $this->username = session('admin_username');
    }

}