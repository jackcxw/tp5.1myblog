<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('hello/:name', 'index/hello');

//后台
/*登陆 登出*/
Route::get('admin/login','admin/login/index');
Route::get('admin/out$','admin/index/out');
/*后台首页*/
Route::get('admin/index','admin/index/index');
/*后台用户列表*/
Route::get('admin/userlist','admin/index/userlist');
/*新增用户*/
Route::get('admin/useradd','admin/index/useradd');
/*查看用户*/
Route::get('admin/usersee','admin/index/usersee');
/*修改用户*/
Route::get('admin/useredit','admin/index/useredit');

/*后台配置*/
Route::get('admin/config','admin/config/index');
/*后台账号设置*/
Route::get('admin/adminer','admin/config/admin');

/*快递查询*/
Route::get('admin/kdcx','admin/cxw/kdcx');




/*
 *
 * 文章
 *
 * */
/*文章列表*/
Route::get('admin/article$','admin/article/index');
/*新增文章*/
Route::get('admin/articleadd','admin/article/add');
/*修改文章*/
Route::get('admin/articleedit','admin/article/edit');


/*发现美好*/

Route::get('admin/findfine$','admin/article/findfine');
Route::get('admin/findfineadd','admin/article/findfineadd');
Route::get('admin/findfineedit','admin/article/findfineedit');

/*外链信息港*/

Route::get('admin/wailian$','admin/article/outnews');
Route::get('admin/wailianadd','admin/article/outnewsadd');
Route::get('admin/wailianedit','admin/article/outnewsedit');

/*测试*/
Route::get('/test','admin/test/word');

/*
 * 前台
 *
 * */

/*前台首页*/
Route::get('/','index/index/index');
//登陆
Route::get('/login','index/login/index');
Route::get('/out','index/login/loginout');
//注册
Route::get('/register','index/register/index');
//内容页
Route::get('/article/:id','index/index/article');
/*文章列表*/
Route::get('/blog','index/index/articlelist?catid=1');
Route::get('/article','index/index/articlelist?catid=2');
Route::get('/write','index/index/articlelist?catid=7');
Route::get('/search','index/index/articlelist');


return [

];
