<?php
/**
 * Created by PhpStorm.
 * User: 15603
 * Date: 2018/6/14
 * Time: 11:14
 */

namespace app\admin\controller;

use think\Validate;
use think\Controller;
use think\Db;
use think\Request;
use \app\api\controller\common;
class Cxw extends Controller
{

        public function return_msg($code,$msg='',$data=[]){
            $res = [];
            $return_msg['code'] = $code;
            $return_msg['msg'] = $msg;
            $return_msg['data'] = $data;
            //返回信息终止
            echo  json_encode($return_msg);die();
        }
        /*文章验证*/
        public function ValidateArticle($data){

            $validate = Validate::make([
                'title'  => 'require|max:30',
                'englishname' => 'require|max:30',
                'keywords'  => 'require|max:15',
                'abstract' => 'require|max:200',
                'author'  => 'require|max:25',
                'catid' => 'number',
                'content'  => 'require|min:50',
            ]);
            $data = $data;
            if (!$validate->check($data)) {
                $this->return_msg(400,$validate->getError());
            }
        }


        /*网站配置验证*/
        public function ValidateWebconfig($data){
            $validate = Validate::make([
                'webname'  => 'require|max:30',
                'remarks' => 'require',
                'phone' => 'number',
                'motto'  => 'require',
            ]);
            $data = $data;
            if (!$validate->check($data)) {
                $this->return_msg(400,$validate->getError());
            }
        }


        /*发现美好验证*/
        public function ValidateFindfine($data){

            $validate = Validate::make([
                'title'  => 'require|max:30',
                'descripition' => 'require|max:60',
            ]);
            $data = $data;
            if (!$validate->check($data)) {
                $this->return_msg(400,$validate->getError());
            }
        }

    /*外链信息港验证*/
    public function ValidateOutnews($data){

        $validate = Validate::make([
            'title'  => 'require|max:30',
            'descripition' => 'require|max:60',
            'url' => 'require|max:300',
        ]);
        $data = $data;
        if (!$validate->check($data)) {
            $this->return_msg(400,$validate->getError());
        }
    }

        /*
         * 快递查询
        */
        public function kdcx(){

            return $this->fetch('/cxw/kdcx');

        }

}