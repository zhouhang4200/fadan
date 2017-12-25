<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('kamen', 'OrderController@KamenOrder');
Route::any('test', 'OrderController@test');

/* App 接口 */
Route::namespace('App')->middleware('api.decode')->group(function () {
    // 用户登陆
    Route::post('auth/login', 'AuthController@login');


    Route::any('test', 'TestController@index'); // 临时测试，想删就删

    // 登陆后接口
    Route::middleware('api.auth')->group(function () {
        // 用户认证
        Route::post('auth/logout', 'AuthController@logout');

        // 用户信息
        Route::get('user', 'UserController@index');

        // 订单列表
        Route::get('order', 'OrderController@index');
        // 退回集市
        Route::post('order/turn-back', 'OrderController@turnBack');
    });
});

