<?php
namespace app\index\controller;

use \think\Validate;
use \think\Db;
use \app\index\model\Config;
use \app\admin\model\Article;
class Register extends Base
{
    public function index ()
    {
        /*网站配置*/
        $config = Config::where('id',1)->find();
        $this->assign('config',$config);
        /*最新文章推荐*/
        $newsarticle = Article::where('deletetime', 0)->order('id', 'desc')->find();
        $this->assign('newsarticle',$newsarticle);
        return $this->fetch('/register');
    }


}
