<?php

use Illuminate\Http\Request;

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

Route::namespace('Api')->group(function () {


    Route::prefix('auto-add-funds')->group(function (){
        Route::any('member', 'AutoAddFundsController@member');
        Route::post('self', 'AutoAddFundsController@self');
    });


    Route::post('kamen', 'OrderController@KamenOrder');
    Route::any('test', 'OrderController@test');

    // 回调接口
    Route::post('receive/order', 'LevelingController@receiveOrder'); //接单
    
    Route::post('consult', 'LevelingController@consult'); // 协商
    Route::post('cancel/consult', 'LevelingController@cancelConsult'); // 取消协商
    Route::post('agree/consult', 'LevelingController@agreeConsult'); // 同意协商
    

    Route::post('appeal', 'LevelingController@appeal'); // 申诉
    Route::post('cancel/appeal', 'LevelingController@cancelAppeal'); // 取消申诉
    Route::post('agree/appeal', 'LevelingController@agreeAppeal'); // 同意申诉
    Route::post('force/consult', 'LevelingController@forceConsult'); // 强制同意协商
    
    Route::post('unusual/order', 'LevelingController@unusualOrder'); // 异常
    Route::post('cancel/unusual', 'LevelingController@cancelUnusual'); // 取消异常

    Route::post('apply/complete', 'LevelingController@applyComplete'); //申请验收
    Route::post('cancel/complete', 'LevelingController@cancelComplete'); //取消验收
    Route::post('refuse/consult', 'LevelingController@refuseConsult'); //拒绝验收
});

