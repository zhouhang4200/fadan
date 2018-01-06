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
Route::any('test', 'TestController@test');

/* App 接口 */
Route::namespace('App')->middleware('api.decode')->group(function () {
    // 用户登陆
    Route::post('auth/login', 'AuthController@login');

    // 版本检查
    Route::get('version/check', 'VersionController@check');

    // 临时测试，想删就删
    Route::any('test', 'TestController@index');

    // 登陆后接口
    Route::middleware('api.auth')->group(function () {
        // 用户认证
        Route::post('auth/logout', 'AuthController@logout');

        // 用户信息
        Route::get('user', 'UserController@index');

        // 订单列表
        Route::get('order', 'OrderController@index');
        // 订单详情
        Route::get('order/detail', 'OrderController@detail');
        // 退回集市
        Route::post('order/turn-back', 'OrderController@turnBack');
        // 发货
        Route::post('order/delivery', 'OrderController@delivery');
        // 发货失败
        Route::post('order/delivery-failure', 'OrderController@deliveryFailure');
    });

    // 充值结果回调
    Route::post('order-charge/notify', 'OrderChargeController@notify');
});
