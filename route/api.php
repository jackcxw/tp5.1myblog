<?php
/**
 * Created by PhpStorm.
 * User: 15603
 * Date: 2018/5/22
 * Time: 17:34
 */


/*
 * api路由
*/

Route::post('api/login','api/User/login');
Route::post('api/test','api/Common/test');
Route::post('api/get_code','api/Code/get_code');

Route::post('api/register','api/User/register');
Route::post('api/admin/login','api/User/admin_login');
Route::post('api/admin/useradd','api/User/useradd');
Route::post('api/admin/useredit','api/User/useredit');
/*删除用户*/
Route::post('api/admin/userdelete','api/User/userdelete');
/*修改管理员*/
Route::post('api/admin/adminedit','api/Config/adminedit');
/*文章*/
Route::post('api/admin/articleadd','api/Article/add');
Route::post('api/admin/articleedit','api/Article/edit');
Route::post('api/admin/articledelete','api/Article/delete');

/*外链信息港*/
Route::post('api/admin/wailianadd','api/Article/outnewsadd');
Route::post('api/admin/wailianedit','api/Article/outnewsedit');
Route::post('api/admin/wailiandelete','api/Article/outnewsdelete');


/*发现美好*/
Route::post('api/admin/findfineadd','api/Article/findfineadd');
Route::post('api/admin/findfineedit','api/Article/findfineedit');
Route::post('api/admin/findfinedelete','api/Article/findfinedelete');

/*快递查询*/
Route::post('api/admin/kucxadd','api/Article/kucxadd');

/*网站配置*/

Route::post('api/admin/config','api/Article/webconfig');



/*前台*/
Route::post('api/homeuseradd','api/User/homeuseradd');
//注册
Route::post('api/blogregister','api/User/blogregister');
//登陆
Route::post('api/bloglogin','api/User/bloglogin');
//评论
Route::post('api/comments','api/User/comments');
/*登陆用户二级评论*/
Route::post('api/soncomments','api/User/soncomments');
/*游客二级评论*/
Route::post('api/yksoncomments','api/User/soncommentsyk');
/*反馈提交*/
Route::post('api/feedback','api/Article/feedback');
/*文章列表*/
/*Route::group('api', [
    'login'   => 'api/User/login',
    'index/:id' => 'api/User/index',
])->pattern(['id' => '\d+']);*/



