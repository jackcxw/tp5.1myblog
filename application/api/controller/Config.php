<?php
namespace app\api\controller ;
use think\Db;
use app\admin\model\Admin;

/**
 * Created by PhpStorm.
 * User: 15603
 * Date: 2018/5/22
 * Time: 16:50
 */
class Config extends Common
{

    /*管理员修改*/
    public function adminedit(){
        $data = $this->params;
        $file = request()->file('newpic');
        if($file){
            // 移动到框架应用根目录/uploads/ 目录下
            $info = $file->move( 'public/uploads');
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

            $data['pic'] = "/public/uploads/". $info->getSaveName();
            $data['pic'] = str_replace("\\","/",$data['pic']);
        }
        $user = new Admin();
        $where_username = "username = '".$data['username']."' and id != ".$data['id'];

        $isusername = $user->where($where_username)->find();
        if($isusername){
            return $this->return_msg(400,'用户名已存在');
        }
        $data['password'] = base64_encode($data['password']);
        $data['updatetime'] = time();
        $data['ip'] = $this->request->ip();
        $data['token'] =  md5($data['username'].md5($data['password']).time());
        $res = $user->save($data,['id' => $data['id']]);
        if(!$res){
            return $this->return_msg(400,'修改失败');
        }
        return $this->return_msg(200,'修改成功');
    }

}