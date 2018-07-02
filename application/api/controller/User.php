<?php
namespace app\api\controller ;
use app\admin\model\Article;
use think\Db;
use app\admin\model\User as Modeluser;

/**
 * Created by PhpStorm.
 * User: 15603
 * Date: 2018/5/22
 * Time: 16:50
 */
class User extends Common
{
    public function index($id){
        echo 'api控制器index方法';
        echo '<br>';
        echo  $id;
    }

    public function login()
    {
        $data  = $this->params;
        $user_type = $this->check_username($data['user_name']);

    }

    /*
     * 注册api
     * */

    public function register(){
       $data = $this->params;
        $this->check_code($data['user_name'],$data['code']);
        //检查用户类型（手机|邮箱）
        $user_type = $this->check_username($data['user_name']);
        //判断数据库里是否存在用户
        switch ($user_type){
            case 'phone':
                $this->check_exist($data['user_name'],$user_type,0);
                $data['phone'] = $data['user_name'];
                break;
            case 'email':
                $this->check_exist($data['user_name'],$user_type,0);
                $data['email'] = $data['user_name'];
                break;
        }
        $data['regtime'] = time();
        unset($data['user_name']);
        $res = Db::name('user')->insert($data);
        if($res){
            return $this->return_msg(200,'用户注册成功');
        }else{
            return $this->return_msg(400,'用户注册失败');
        }

    }

    /*
     * 前台注册
     * */

    public function blogregister(){

        $data = $this->params;

        $this->isUser($data['username'],$data['email']);
        $user = Modeluser::create([
                'username' =>$data['username'],
                'password' =>base64_encode($data['password']),
                'email' =>$data['email'],
                'photo' =>'/public/static/home//images/face/'.rand(1,110).'.png',
                'regtime' =>time(),
                'role' =>0,
                'token' => md5($data['email'].md5($data['password']).time()),
                'ip' => $this->request->ip()
            ], ['username', 'password', 'email','photo', 'regtime', 'role', 'token', 'ip']);
        if(!$user){
            $this->return_msg(400,'注册失败，注册功能暂时不能使用');
        }
        session('home_username',$user['username']);
        $this->return_msg(200,'注册成功,进入首页');


    }

    /*
     * 检验注册用户是否存在数据库中
     * */

    public function isUser($username,$email){
        $res = Modeluser::where('username',$username)->find();
        if($res){
            $this->return_msg(400,"昵称已存在");
        }
        $res1 = Modeluser::where('email',$email)->find();
        if($res1){
            $this->return_msg(400,"邮箱已注册");
        }

    }
    /*
     * 前台登陆
     * */

    public function bloglogin(){

        $data = $this->params;
        $this->getUser($data['username'],$data['password']);
    }

    /*
     * 判断用户名是邮箱还是昵称
     * */

    public function getUser($username,$password){

        $is_email = preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/',$username)?1:0;
        if($is_email==1){
            $user = Modeluser::where('email',$username)->find();
            if(!$user){
                $this->return_msg(400,'邮箱不存在！');
            }
            if($password  != base64_decode($user['password'])){
                $this->return_msg(400,'密码错误！');
            }
            session('home_username',$user['username']);
            $this->return_msg(200,'登陆成功');
        }
        if($is_email==0){
        $user = Modeluser::where('username',$username)->find();
        if(!$user){
            $this->return_msg(400,'昵称不存在！');
        }
        if($password  != base64_decode($user['password'])){
            $this->return_msg(400,'密码错误！');
        }
        session('home_username',$user['username']);
        $this->return_msg(200,'登陆成功');
        }


    }

    /*
     * 后台登陆
     * */

    public function admin_login(){

        $data = $this->params;
        $user = Db::table('api_admin')->where('username',$data['username'])->find();
        if(!$user){
            $this->return_msg(400,'用户不存在!');
        }
        if($data['password'] !== base64_decode($user['password'])){
            $this->return_msg(400,'密码错误');
        }
        //存入session
        session('admin_username',$user['username']);
        session('admin_pic',$user['pic']);
        $this->return_msg(200,'登陆成功');
    }

    /*
     * 前台新增用户
     * */
    public function homeuseradd(){
        $data = $this->params;
        $isusername = Modeluser::where('username',$data['username'])->find();
        if($isusername){
            return $this->return_msg(400,'昵称已存在');
        }
        $isemail = Modeluser::where('email',$data['email'])->find();
        if($isemail){
            return $this->return_msg(400,'邮箱已注册');
        }

        $user = new Modeluser();
        $user->username = $data['username'];
        $user->password = base64_encode($data['password']);
        $user->email =  $data['email']?$data['email']:"";
        $user->regtime = time();
        $user->role = $data['role'];
        $user->ip = $this->request->ip();
        $user->token = md5($data['email'].md5($data['password']).time());

        $res = $user->save();
        if(!$res){
            return $this->return_msg(400,'添加失败');
        }
        return $this->return_msg(200,'添加用户成功');

    }
    /*
     * 后台新增用户
     * */

    public function useradd(){

        $data = $this->params;
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('photo');
        if(!$file){
            return $this->return_msg(400,'请上传头像');
        }
        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->move( '../public/uploads');
        if($info){
            // 成功上传后 获取上传信息
            // 输出 jpg
            $info->getExtension();
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            $info->getSaveName();
            // 输出 42a79759f284b767dfcb2a0197904287.jpg
            $info->getFilename();
        }else{
            // 上传失败获取错误信息
            return $this->return_msg(400,$file->getError());
        }

        $photo = "/uploads/". $info->getSaveName();

        $isusername = Modeluser::where('username',$data['username'])->find();
        if($isusername){
            return $this->return_msg(400,'用户名已存在');
        }
        $isemail = Modeluser::where('email',$data['email'])->find();
        if($isemail){
            return $this->return_msg(400,'邮箱已存在');
        }
        $isphone = Modeluser::where('phone',$data['phone'])->find();
        if($isphone){
            return $this->return_msg(400,'手机号已存在');
        }
        $user = new Modeluser();
        $user->username = $data['username']?$data['username']:"";
        $user->password = base64_encode($data['password'])?base64_encode($data['password']):"";
        $user->email =  $data['email']?$data['email']:"";
        $user->phone =  $data['phone']? $data['phone']:"";
        $user->regtime = time();
        $user->photo = $photo?$photo:"";
        $user->role = $data['role'];
        $user->ip = $this->request->ip();
        if($data['phone']){
            $user->token = md5($data['phone'].md5($data['password']).time());
        }else{
            $user->token = md5($data['email'].md5($data['password']).time());
        }
        $res = $user->save();
        if(!$res){
            return $this->return_msg(400,'添加失败');
        }
        return $this->return_msg(200,'添加用户成功');




    }


    /*
     * 修改用户
     * */

    public function useredit(){

        $data = $this->params;
        $file = request()->file('newpic');
        if($file){
            // 移动到框架应用根目录/uploads/ 目录下
            $info = $file->move( '../public/uploads');
            if($info){
                // 成功上传后 获取上传信息
                // 输出 jpg
                $info->getExtension();
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                $info->getSaveName();
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                $info->getFilename();
            }else{
                // 上传失败获取错误信息
                return $this->return_msg(400,$file->getError());
            }

            $data['photo'] = "/uploads/". $info->getSaveName();
        }
        $user = new Modeluser();
        $where_username = "username = '".$data['username']."' and id != ".$data['id'];

        $isusername = $user->where($where_username)->find();
        if($isusername){
            return $this->return_msg(400,'用户名已存在');
        }
        $where_email = "email = '".$data['email']."' and id != ".$data['id'];
        $isemail =  $user->where($where_email)->find();
        if($isemail){
            return $this->return_msg(400,'邮箱已存在');
        }
        $where_phone = "phone = ".$data['phone']." and id != ".$data['id'];
        $isphone =  $user->where($where_phone)->find();
        if($isphone){
            return $this->return_msg(400,'手机号已存在');
        }
        $data['password'] = base64_encode($data['password']);

        $res = $user->save($data,['id' => $data['id']]);
        if(!$res){
            return $this->return_msg(400,'修改失败');
        }
        return $this->return_msg(200,'修改成功');
    }

    /*
       * 用户删除
       * */

    public function userdelete($id){
        $user = Modeluser::find($id);
        if(!$user){
            $this->return_msg(400,'用户不存在');
        }
        $user->isdel = time();
        $res = $user->save();
        if($res){
           $this->return_msg(200,'删除成功');
        }
    }
    /*
     * 用户评论
     * */

    public function comments(){
        $data = $this->params;
        if(session('home_username')){
            $user = Modeluser::where('username',session('home_username'))->find();
            $d = ['articleid' => $data['articleid'], 'parent' => 0,'content' => $data['content'],'addtime' => time(),'uid'=>$user['id'],'email'=>$user['email'],'username'=>$user['username'] ];
        }else{
            $user = Modeluser::where('username',$data['username'])->find();
            if($user){
                $this->return_msg(400,'昵称已存在');
            }
            $d = ['articleid' => $data['articleid'], 'parent' => 0,'content' => $data['content'],'addtime' => time(),'email'=>$data['email'],'username'=>$data['username'],'headimg'=>rand(1,110) ];

        }
        $comments = Db::name('comments')->insert($d);
        if(!$comments){
            $this->return_msg(400,"留言失败");
        }
        $article = Article::get($data['articleid']);
        $pinglun = $article['pinglun']+1;
        $article['pinglun'] = $pinglun;
        $res = $article->save();
        if($res){
            $this->return_msg(200,'留言成功');
        }

    }

    /*二级评论*/
    public function soncomments(){
        $data = $this->params;
        $user = Modeluser::where('username',session('home_username'))->find();
        $d = ['articleid' => $data['articleid'], 'parent' => $data['parentid'],'content' => $data['content'],'addtime' => time(),'uid'=>$user['id'],'email'=>$user['email'],'username'=>$user['username'] ];
        $soncomments = Db::name('comments')->insert($d);
        if(!$soncomments){
            $this->return_msg(400,"回复失败");
        }
        $article = Article::get($data['articleid']);
        $pinglun = $article['pinglun']+1;
        $article['pinglun'] = $pinglun;
        $res = $article->save();
        if($res){
            $this->return_msg(200,'回复成功');
        }
    }
    /*游客二级评论*/
    public function soncommentsyk(){
        $data = $this->params;
        $d = ['articleid' => $data['articleid'], 'parent' => $data['parentid'],'content' => $data['content'],'addtime' => time(),'email'=>$data['email'],'username'=>$data['username'],'headimg'=>rand(1,110) ];
        $soncomments = Db::name('comments')->insert($d);
        if(!$soncomments){
            $this->return_msg(400,"回复失败");
        }
        $article = Article::get($data['articleid']);
        $pinglun = $article['pinglun']+1;
        $article['pinglun'] = $pinglun;
        $res = $article->save();
        if($res){
            $this->return_msg(200,'回复成功');
        }

    }

}