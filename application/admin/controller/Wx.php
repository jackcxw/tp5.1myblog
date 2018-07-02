<?php
/**
 * Created by PhpStorm.
 * User: 15603
 * Date: 2018/6/25
 * Time: 17:35
 */

namespace app\admin\controller;

class Wx extends Base
{
    protected $config =[
            'token'          => 'weixineto',
            'appid'          => 'wxe1acda67ec03ffe9',
            'appsecret'      => '4d71df324f3691c9024b9f64c4f8b278'
        ];
    /*获取用户列表*/
    public function index(){
        try {

            // 实例对应的接口对象
            $user = \We::WeChatUser($this->config);

            // 调用接口对象方法
            $list = $user->getUserList();

            // 处理返回的结果
            echo '<pre>';
            var_export($list);

        } catch (Exception $e) {

            // 出错啦，处理下吧
            echo $e->getMessage() . PHP_EOL;

        }
    }

    /*微信网页授权*/

    public function oauth(){
        try {

            // 实例对应的接口对象
            $oauth = \We::WeChatOauth($this->config);
            //第一步 网页授权
            $geturl = $oauth->getOauthRedirect('cxw.com','','snsapi_base');
            header('Location:'.$geturl);
            die();
           /* $code = $_GET['code'];
            if( !isset($code) ){
                header('Location:'.$geturl);
            }*/
            //获取用户accesstoken
            $getaccesstoken = $oauth->getOauthAccessToken();
            //检查accesstoken是否有效
            $check = $oauth->checkOauthAccessToken($access_token,$openid);
            //获取用户信息
            $res = $oauth->getUserInfo($access_token,$openid,'');

            // 处理返回的结果
            echo '<pre>';
            var_export($res);

        } catch (Exception $e) {

            // 出错啦，处理下吧
            echo $e->getMessage() . PHP_EOL;

        }
    }
}