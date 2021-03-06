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
    Route::any('member-info', 'AutoAddFundsController@memberInfo');
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
        Route::post('query', 'OperationDistributeController@query');
        // 接单
        Route::post('receive', 'OperationDistributeController@receive');
        // 申请验收
        Route::post('apply-complete', 'OperationDistributeController@applyComplete');
        // 取消验收
        Route::post('cancel-complete', 'OperationDistributeController@cancelComplete');
        // 撤销
        Route::post('revoke', 'OperationDistributeController@revoke');
        // 取消撤销
        Route::post('cancel-revoke', 'OperationDistributeController@cancelRevoke');
        // 不同意撤销
        Route::post('refuse-revoke', 'OperationDistributeController@refuseRevoke');
        // 同意撤销
        Route::post('agree-revoke', 'OperationDistributeController@agreeRevoke');
        // 强制撤销
        Route::post('force-revoke', 'OperationDistributeController@forceRevoke');
        // 申请仲裁
        Route::post('apply-arbitration', 'OperationDistributeController@applyArbitration');
        // 取消仲裁
        Route::post('cancel-arbitration', 'OperationDistributeController@cancelArbitration');
        // 强制仲裁
        Route::post('force-arbitration', 'OperationDistributeController@forceArbitration');
        // 异常
        Route::post('abnormal', 'OperationDistributeController@abnormal');
        // 取消异常
        Route::post('cancel-abnormal', 'OperationDistributeController@cancelAbnormal');
        // 回传
        Route::post('callback', 'OperationDistributeController@callback');
        // 完成
        Route::post('complete', 'OperationDistributeController@complete');
        // 新留言通知接口
        Route::post('new-message', 'OperationDistributeController@newMessage');
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
        // 游戏，区服信息总览
        Route::post('data', 'GameController@datas');
    });
});
// 淘宝抓取订单
Route::middleware('taobao.api')->group(function () {
    // 买家下单并付款
    Route::post('taobao/store', 'TaobaoController@store');
    // 交易成功
    Route::post('taobao/trade-success', 'TaobaoController@tradeSuccess');
    // 买家发起退款
    Route::post('taobao/refund-created', 'TaobaoController@refundCreated');
    // 卖家发货
    Route::post('taobao/trade-ship', 'TaobaoController@tradeShip');
    // 卖家同意退款
    Route::post('taobao/refund-agree', 'TaobaoController@refundAgree');
    // 卖家拒绝退款
    Route::post('taobao/refund-refuse', 'TaobaoController@refundRefuse');
    // 退款成功
    Route::post('taobao/refund-success', 'TaobaoController@refundSuccess');
    // 退款关闭
    Route::post('taobao/refund-closed', 'TaobaoController@refundClosed');
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

// 财务提现回调
Route::post('fulu-pay/withdraw-notify', 'FuluPayController@withdrawNotify')->name('api.fulu-pay.withdraw-notify');

# v1 api 路由文件
//require  __DIR__ . '/v1-api.php';
Route::prefix('v1')->namespace('V1')->group(function($router) {
//    Route::prefix('auth')->group(function($router) {
//        $router->post('login', 'Auth\AuthController@login');
//        $router->post('logout', 'OpenApi\Auth\AuthController@logout');
//    });

    Route::middleware('open.api')->group(function($router) {

        # 游戏
        Route::namespace('Game')->group(function($router) {
            # 获取所有游戏
            $router->post('games','IndexController@index');
            # 根据指定游戏ID获取所有区
            $router->post('regions','IndexController@index');
            # 根据指定游戏ID获取所有区
            $router->post('servers','IndexController@index');
        });

        # 订单
        Route::prefix('order')->namespace('Order')->group(function($router) {

            # 游戏代练
            Route::prefix('game-leveling')->group(function($router) {
                # 下单
                $router->post('create','GameLevelingOrderController@create');
                # 查看订单
                $router->post('show','GameLevelingOrderController@show');
                # 更新订单
                $router->post('update','GameLevelingOrderController@update');
                # 下架
                $router->post('off-sale','GameLevelingOrderController@offSale');
                # 上架
                $router->post('on-sale','GameLevelingOrderController@onSale');
                # 撤单
                $router->post('delete','GameLevelingOrderController@delete');
                # 撤销
                $router->post('apply-consult','GameLevelingOrderController@applyConsult');
                # 取消撤销
                $router->post('cancel-consult','GameLevelingOrderController@cancelConsult');
                # 同意撤销
                $router->post('agree-consult','GameLevelingOrderController@agreeConsult');
                # 拒绝撤销
                $router->post('reject-consult','GameLevelingOrderController@rejectConsult');
                # 申请仲裁
                $router->post('apply-complain','GameLevelingOrderController@applyComplain');
                # 取消仲裁
                $router->post('cancel-complain','GameLevelingOrderController@cancelComplain');
                # 锁定
                $router->post('lock','GameLevelingOrderController@lock');
                # 取消锁定
                $router->post('cancel-lock','GameLevelingOrderController@cancelLock');
            });
        });
    });
});