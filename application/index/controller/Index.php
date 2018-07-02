<?php
namespace app\index\controller;

use \think\Validate;
use \think\Db;
use \app\index\model\Config;
use \app\admin\model\Article;
use \think\Request;
class Index extends Base
{
    /*首页*/
    public function index ()
    {
        /*网站配置*/
        $config = Config::where('id',1)->find();

        /*最新文章推荐*/
        $newsarticle = Article::where('deletetime', 0)->order('id', 'desc')->find();
        $article = Article::where('deletetime',0)->order('addtime','desc')->paginate(5);
        $this->assign('config',$config);
        $this->assign('article',$article);
        $this->assign('newsarticle',$newsarticle);
        return $this->fetch('/index');
    }

    /*文章内容页*/

    public function article($id){
        /*网站配置*/
        $config = Config::where('id',1)->find();
        $article = Article::where(['id'=>$id,'deletetime'=>0])->find();
        if(!$article){
            $this->error('文章不存在');
        }
        $categrey = Db::table('api_articlecategory')->where('id',$article['catid'])->find();
        $article['catname'] = $categrey['catname'];
        /*评论*/
        $this->assign('config',$config);
        $this->assign('article',$article);

        /*浏览量加1*/
        $thisarticle = new Article();
        $see =  $article['see']+1;
        $thisarticle->save([
            'see'  => $see
        ],['id' => $id]);
        return $this->fetch('/article');
    }

    /*文章列表*/
    public function articlelist(Request $request){
        /*网站配置*/
        $config = Config::where('id',1)->find();

        if($request->catid){
            $where = [
                ['deletetime','=',0],
                ['catid','=',$request->catid]
            ];
        }
        if($request->search){
            if($request->search='搜你想要...'){
                $where = [
                    ['deletetime','=',0],
                ];
            }else{
                $where = [
                    ['deletetime','=',0],
                    ['content','like',"%".$request->search."%"]
                ];
            }
        }
        $article = Db::table('api_article')->where($where)->limit('addtime','desc')->paginate(10);
        $this->assign('article',$article);
        $this->assign('config',$config);
        return $this->fetch('/articlelist');

    }

}
