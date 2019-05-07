<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// 获取用户信息
Route::get('api/userinfo','Api\UserController@getUserInfo');

// api使用curl请求apitest(get方法)
Route::get('api/curl','Api\UserController@cURLTest');

// api使用curl请求apitest(post方法)
// form-data格式
Route::get('api/post1','Api\UserController@curlPost1');
// application/x-www-form-urlencoded格式
Route::get('api/post2','Api\UserController@curlPost2');
// raw(字符串文本)
Route::get('api/post3','Api\UserController@curlPost3');

// 使用中间件限制接口每半分钟调用十次
Route::get('api/middle','Api\UserController@middle')->middleware('middle');

// 用户注册
Route::post('api/register','Api\UserController@register');

// 登录
Route::post('api/login','Api\UserController@login');

// 个人中心
Route::get('api/user','Api\UserController@user')->middleware(['check.login','middle']);
