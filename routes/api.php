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

    Route::post('kamen', 'OrderController@KamenOrder');
    Route::any('test', 'OrderController@test');

    // 回调接口
    Route::post('receive', 'LevelingController@receive'); //接单
    Route::post('agree/consult', 'LevelingController@receive'); // 同意协商
    Route::post('agree/revoke', 'LevelingController@receive'); // 同意撤销
});

