<?php
namespace app\api\controller ;

use app\admin\controller\Cxw;
use app\admin\model\User;
use think\Validate;
use think\Db;
use think\Request;
use app\admin\model\Article as Articlemodel;
use app\index\model\Config;
use \app\admin\model\Findfine;
use \app\admin\model\Outnews;

/**
 * Created by PhpStorm.
 * User: 15603
 * Date: 2018/5/22
 * Time: 16:50
 */
class Article extends Cxw
{

    /*新增文章*/
    public function add(Request $request){
        $data = $request->param();
        $this->ValidateArticle($data);
        $file = request()->file('thumb');
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
            $data['thumb'] = "/public/uploads/". $info->getSaveName();
            $data['thumb'] = str_replace("\\","/",$data['thumb']);
        }
        $article = new Articlemodel();
        $data['addtime'] = time();
        $res = $article->save($data);

        if($res){
            $this->return_msg(200,'添加文章成功');
        }

    }

    /*修改文章*/
    public function edit(Request $request){
        $data = $request->param();
        $file = request()->file('thumbimg');
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

            $data['thumb'] = "/public/uploads/". $info->getSaveName();
        }
        $thumb =  $data['thumb'];
        $id = $data['id'];
        $data =  $this->request->except(['id']);
        $this->ValidateArticle($data);
        $article = new Articlemodel();
        $data['updatetime'] = time();
        $data['thumb'] = $thumb;
        $res = $article->save($data,['id'=>$id]);
        if($res){
            $this->return_msg(200,'修改文章成功');
        }else{
            $this->return_msg(400,'修改失败');
        }
    }

    /*删除文章*/
    public function delete($id)
    {
        $article = Articlemodel::where('id', $id)->find();
        if(!$article){
            $this->return_msg(400,'文章不存在') ;
        }
        $article['deletetime'] = time();
        $res = $article->save();
        if($res){
            $this->return_msg(200,'删除成功');
        }
    }

    /*网站配置*/
    public function webconfig(Request $request){
        $data = $request->param();
        $logo = request()->file('logoimg');
        $banner = request()->file('bannerimg');
        $icon = request()->file('iconimg');
        if($logo){
            // 移动到框架应用根目录/uploads/ 目录下
            $info = $logo->move( 'public/uploads');
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
                return $this->return_msg(400,$logo->getError());
            }

            $data['logo'] = "/public/uploads/". $info->getSaveName();
            $data['logo'] = str_replace("\\","/", $data['logo']);
        }
        if($icon){
            // 移动到框架应用根目录/uploads/ 目录下
            $info = $icon->move( 'public/uploads');
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
                return $this->return_msg(400,$icon->getError());
            }

            $data['icon'] = "/public/uploads/". $info->getSaveName();
            $data['icon'] = str_replace("\\","/", $data['icon']);
        }
        if($banner){
            // 移动到框架应用根目录/uploads/ 目录下
            $info = $banner->move( 'public/uploads');
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
                return $this->return_msg(400,$banner->getError());
            }

            $data['banner'] = "/public/uploads/". $info->getSaveName();
            $data['banner'] = str_replace("\\","/", $data['banner']);
        }
        unset($data['iconimg']);
        unset($data['bannerimg']);
        unset($data['logoimg']);
        $this->ValidateWebconfig($data);
        $config = new Config();
        $res = $config->save($data,['id'=>1]);

        if($res){
            $this->return_msg(200,'修改网站配置成功');
        }else{
            $this->return_msg(400,'修改失败');
        }

    }

    /*
     * 快递查询提交
     * */
    public function kucxadd(Request $request){
        $data = $request->param();
        $typeCom = $data['companyname'];
        $typeNu = $data['kdnum'];
        $AppKey='3900c5d944242e21';//请将XXXXXX替换成您在http://kuaidi100.com/app/reg.html申请到的KEY
        $url ='http://api.kuaidi100.com/api?id='.$AppKey.'&com='.$typeCom.'&nu='.$typeNu.'&show=2&muti=1&order=asc';
//请勿删除变量$powered 的信息，否者本站将不再为你提供快递接口服务。
        $powered = '查询数据由：<a href="http://kuaidi100.com" target="_blank">KuaiDi100.Com （快递100）</a> 网站提供 ';
//优先使用curl模式发送数据
        if (function_exists('curl_init') == 1){
            $curl = curl_init();
            curl_setopt ($curl, CURLOPT_URL, $url);
            curl_setopt ($curl, CURLOPT_HEADER,0);
            curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($curl, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
            curl_setopt ($curl, CURLOPT_TIMEOUT,5);
            $get_content = curl_exec($curl);
            curl_close ($curl);
            $this->return_msg(200,$get_content);
        }else{
            include("./snoopy.php");
            $snoopy = new snoopy();
            $snoopy->referer = 'http://www.google.com/';//伪装来源
            $snoopy->fetch($url);
            $get_content = $snoopy->results;
        }
        print_r($get_content . '<br/>' . $powered);
        exit();
    }
    /*反馈*/
    public function feedback(Request $request){
        $data = $request->param();
        $data['ip'] = $request->ip();
        if(session('home_username')){
            $user = User::where('username',session('home_username'))->find();
            $data['userid'] =$user['id'];
        }
        $data['createtime'] = time();
        $feedback = Db::table('api_feedback')->insert($data);
        if(!$feedback){
            $this->return_msg('提交失败，网站暂停服务');
        }
        $this->return_msg(200,'提交成功');
    }



    /*新增外链信息港*/
    public function outnewsadd(Request $request){
        $data = $request->param();
        $this->ValidateOutnews($data);
        $file = request()->file('thumb');
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
            $data['thumb'] = "/public/uploads/". $info->getSaveName();
            $data['thumb'] = str_replace("\\","/",$data['thumb']);
        }
        $Outnews = new Outnews();
        $data['addtime'] = time();
        $res = $Outnews->save($data);

        if($res){
            $this->return_msg(200,'添加外链成功');
        }else{
            $this->return_msg(400,'添加外链失败');
        }

    }

    /*修改外链信息港*/
    public function outnewsedit(Request $request){
        $data = $request->param();
        $file = request()->file('thumbimg');
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

            $data['thumb'] = "/public/uploads/". $info->getSaveName();
            $data['thumb'] = str_replace("\\","/",$data['thumb']);
        }
        $thumb =  $data['thumb'];
        $id = $data['id'];
        $data =  $this->request->except(['id']);
        $this->ValidateOutnews($data);
        $Outnews = new Outnews();
        $data['updatetime'] = time();
        $data['thumb'] = $thumb;
        $res = $Outnews->save($data,['id'=>$id]);
        if($res){
            $this->return_msg(200,'修改外链成功');
        }else{
            $this->return_msg(400,'修改失败');
        }
    }

    /*删除外链信息港*/
    public function outnewsdelete($id)
    {
        $outnews = Outnews::where('id', $id)->find();
        if(!$outnews){
            $this->return_msg(400,'外链不存在') ;
        }
        $outnews['deletetime'] = time();
        $res = $outnews->save();
        if($res){
            $this->return_msg(200,'删除成功');
        }
    }



    /*新增发现美好*/
    public function findfineadd(Request $request){
        $data = $request->param();
        $this->ValidateFindfine($data);
        $file = request()->file('thumb');
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

            $data['thumb'] = "/public/uploads/". $info->getSaveName();
            $data['thumb'] = str_replace("\\","/",$data['thumb']);
        }
        $findfine = new Findfine();
        $data['addtime'] = time();
        $res = $findfine->save($data);

        if($res){
            $this->return_msg(200,'添加成功');
        }else{
            $this->return_msg(400,'添加失败');
        }

    }

    /*修改发现美好*/
    public function findfineedit(Request $request){
        $data = $request->param();
        $file = request()->file('thumbimg');
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
            $data['thumb'] = "/public/uploads/". $info->getSaveName();
            $data['thumb'] = str_replace("\\","/",$data['thumb']);
        }
        $thumb =  $data['thumb'];
        $id = $data['id'];
        $data =  $this->request->except(['id']);
        $this->ValidateFindfine($data);
        $Findfine = new Findfine();
        $data['updatetime'] = time();
        $data['thumb'] = $thumb;
        $res = $Findfine->save($data,['id'=>$id]);
        if($res){
            $this->return_msg(200,'修改成功');
        }else{
            $this->return_msg(400,'修改失败');
        }
    }

    /*删除发现美好*/
    public function findfinedelete($id)
    {
        $findfine = Findfine::where('id', $id)->find();
        if(!$findfine){
            $this->return_msg(400,'该美好不存在') ;
        }
        $findfine['deletetime'] = time();
        $res = $findfine->save();
        if($res){
            $this->return_msg(200,'删除成功');
        }
    }
}