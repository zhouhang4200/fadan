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

Route::any('getOrder', 'SteamOrderController@getOrder');
Route::any('returnOrderData', 'SteamOrderController@returnOrderData');


// 代练妈妈回调
Route::post('order/change', 'DailianMamaController@orderChange');

// 代练对下游合作商接口
Route::prefix('partner')->middleware('api.partner')->namespace('Partner')->group(function () {
    // 订单相关接口
    Route::prefix('order')->group(function () {
        // 订单查询
        Route::post('query', 'OrderController@receiving');
        // 接单
        Route::post('receive', 'OrderController@receive');
        // 申请验收
        Route::post('apply-complete', 'OrderController@applyComplete');
        // 取消验收
        Route::post('cancel-complete', 'OrderController@cancelComplete');
        // 撤销
        Route::post('revoke', 'OrderController@revoke');
        // 取消撤销
        Route::post('cancel-revoke', 'OrderController@cancelRevoke');
        // 不同意撤销
        Route::post('refuse-revoke', 'OrderController@refuseRevoke');
        // 同意撤销
        Route::post('agree-revoke', 'OrderController@agreeRevoke');
        // 强制撤销
        Route::post('force-revoke', 'OrderController@forceRevoke');
        // 申请仲裁
        Route::post('apply-arbitration', 'OrderController@applyArbitration');
        // 取消仲裁
        Route::post('cancel-arbitration', 'OrderController@cancelArbitration');
        // 强制仲裁
        Route::post('force-arbitration', 'OrderController@forceArbitration');
        // 异常
        Route::post('abnormal', 'OrderController@abnormal');
        // 取消异常
        Route::post('cancel-abnormal', 'OrderController@cancelAbnormal');
    });

    // 查询区服接口
    Route::prefix('game')->group(function () {
        // 游戏列表
        Route::post('/', 'GameController@games');
        // 区列表
        Route::post('region', 'GameController@regions');
        // 服列表
        Route::post('server', 'GameController@servers');
        // 代练游戏类型
        Route::post('type', 'GameController@gameTypes');
    });
});
// 淘宝抓取订单
Route::middleware('taobao.api')->group(function () {
    Route::post('taobao/store', 'TaobaoController@store');
    Route::post('taobao/trade-success', 'TaobaoController@tradeSuccess');
    Route::post('taobao/refund-created', 'TaobaoController@refundCreated');
});

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

/**
 * 房卡充值接口
 */
Route::middleware('internal.api')->prefix('room-card-recharge')->group(function (){
    Route::get('/', 'RoomCardRecharge@index');
    Route::post('update', 'RoomCardRecharge@update');
});

// 公司 商户信息查询接口
Route::middleware('internal.api')->prefix('user-info')->namespace('Fulu')->group(function (){
    Route::get('/', 'UserInformationQuery@index');
});

Route::prefix('uplay')->group(function (){
    Route::any('account-verification', 'UplayController@accountVerification');
});
