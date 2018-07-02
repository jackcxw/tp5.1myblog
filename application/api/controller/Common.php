<?php
/**
 * Created by PhpStorm.
 * User: 15603
 * Date: 2018/5/23
 * Time: 9:46
 */

namespace app\api\controller;


use think\Controller;
use think\Validate;
use think\Db;

class Common extends Controller
{
    protected $request;//用来处理的参数
    protected $validate;//用来验证参数
    protected $params; //过滤后合格的参数
    protected $rules = array(
        'User'=>array(
            'login' =>
                 array(
                    'user_name' => array('require','chsDash','max:20'),
                    'user_pwd'  => array('require','max:32')
                ),
            'register' =>
                 array(
                    'user_name' => array('require'),
                    'password' => array('require','length:32'),
                    'code' => array('require','number','length:6')
                    ),
            'admin_login'=>
                 array(
                    'username' => array('require'),
                    'password'=> array('require','max:32')
                     ),
            'useradd' =>
                 array(
                    'username'=>array('require'),
                    'password'=> array('require','max:16','alphaDash'),
                    'phone' =>array('require','mobile'),

                 ),
            'useredit'=>
                 array(),
            'userdelete'=>
                array(),
            'homeuseradd'=>
                 array(
                    'username'=>array('require'),
                    'password'=> array('require','max:16','alphaDash'),
                    'email' =>array('require','email'),
                 ),
            'blogregister' =>
                array(
                    'username' => array('require'),
                    'password' => array('require','max:16','alphaDash'),
                    'email' => array('require','email')
                ),
            'bloglogin' =>
                array(
                    'username' => array('require'),
                    'password' => array('require','max:16','alphaDash')
                ),
            'comments' =>
                array(
                     'content' =>array('require')
                ),
            'soncomments'=>
                array(
                    'articleid'=>array('require'),
                    'parentid' =>array('require'),
                    'content' =>array('require')
                ),
            'soncommentsyk'=>
                array(
                    'articleid'=>array('require'),
                    'parentid' =>array('require'),
                    'email' =>array('require','email'),
                    'username' =>array('require'),
                    'content' =>array('require')
                )
        ),
        'Code'=>array(
            'get_code' =>
                array('username' => array('require'),
                    'is_exist'=> array('require','number','length:1'))
        ),
        'Config'=>array(
            'adminedit' =>
               array(
                        'username'=>array('require'),
                        'password'=> array('require','max:16','alphaDash'),
                    )
            )

    );
    protected function initialize(){
        parent::initialize();
       // $this->check_time($this->request->only(['time']));//验证过期时间
        //$this->check_token($this->request->param());//验证token
        $this->params = $this->check_validate($this->request->except(['time','token']));//数据验证排除time和token

    }
    /*
     * 验证过期时间
     * */
    public function check_time($arr){
        if(!isset($arr['time'])||intval($arr['time']<=1)){
            return $this->return_msg('400','时间戳不存在');
        };

        if(time()-$arr['time']>600000){
            return $this->return_msg('400','请求超时');
        }
    }

    /*
     * 验证token
     * */

    public function check_token($arr){
        if(!isset($arr['token'])||empty($arr['token'])){
            return $this->return_msg('401','token不能为空');
        };
        $app_token = $arr['token'];
        unset($arr['token']);
        $token = '';

        foreach ($arr as $value){
            $token.= md5($value);
        }
        $sever_token = md5('api'.$token.'api');
        if($app_token !== $sever_token){
            return $this->return_msg('402','token值不正确');
        }

    }

    /*
     * 数据验证
     * */

    public function check_validate($arr){
      $rule = $this->rules[$this->request->controller()][$this->request->action()];

        $this->validater = new Validate($rule);

        if(!$this->validater->check($arr)){

           return $this->return_msg('400',$this->validater->getError());
        }

        return $arr;
    }
    /*返回信息
    */
    public function return_msg($code,$msg='',$data=[]){
        $res = [];
        $return_msg['code'] = $code;
        $return_msg['msg'] = $msg;
        $return_msg['data'] = $data;
        //返回信息终止
        echo  json_encode($return_msg);die();
    }

    /*
     * 检查用户名|手机号|邮箱
     * */
    public function check_username($username){

        $is_email = preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/',$username)?1:0;
        $is_phone = preg_match('/^1[34578]\d{9}$/',$username)?4:2;
        $flog = $is_email+$is_phone;
        switch($flog){
            case 2:
                return $this->return_msg('400','用户名不符合邮箱和手机号');
            break;

            case 3:
                return 'email';
                break;

            case 4:
                return 'phone';
                break;
        }

    }

    /*
     * 检验用户是否存在数据库中以及用户类型（手机|邮箱）
     * */
    public function check_exist($username,$username_type,$exist){
        $type_num = $username_type == 'phone' ? 2 : 4 ;
        $flog = $type_num+$exist;
        $res_phone = db('user')->where('phone',$username)->find();
        $res_email =  db('user')->where('email',$username)->find();
        switch($flog){
            case 2:
                if($res_phone){
                    return $this->return_msg('400','该手机号已被使用');//注册
                }
            break;

            case 3:
                if(!$res_phone){
                    return $this->return_msg('400','手机号不存在');//登陆 找回密码 绑定邮箱
                }
                break;

            case 4:
                if($res_email){
                    return $this->return_msg('400','邮箱已被使用'); //注册
                }
                break;

            case 5:
                if(!$res_email){
                    return $this->return_msg('400','邮箱不存在');//登陆 找回密码
                }
                break;

        }

    }

    /*
     * 检查验证码
     * */
    public function check_code($user_name,$code){
        //检查是否超时

        $last_time = session($user_name.'_code_lasttime');
        if(time()-$last_time>60){
            $this->return_msg(400,'验证码超时，请重新提交');
        }
        //检查是否正确
        $session_code = session($user_name.'_code');
        if( $md5_code = md5($user_name.'_'.md5($code)) !== $session_code){
            $this->return_msg(400,'验证码错误');
        }


    }

}