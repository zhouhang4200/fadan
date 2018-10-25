<?php
/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('v2')->namespace('Frontend\V2')->group(function () {

    Route::middleware(['auth'])->group(function () {

        // 订单
        Route::prefix('order')->namespace('Order')->group(function () {

            // 代练订单
            Route::prefix('game-leveling')->group(function () {
                // 订单列表视图
                Route::get('/', 'GameLevelingController@index')->name('order.game-leveling.index');
                // 获取订单列表数据
                Route::post('data-list', 'GameLevelingController@dataList')->name('order.game-leveling.data-list');
                // 接单
                Route::post('take', 'GameLevelingController@take')->name('order.game-leveling.take');
                // 撤单
                Route::post('delete', 'GameLevelingController@delete')->name('order.game-leveling.delete');
                // 申请验收
                Route::post('apply-complete', 'GameLevelingController@applyComplete')->name('order.game-leveling.apply-complete');
                // 取消验收
                Route::post('cancel-complete', 'GameLevelingController@cancelComplete')->name('order.game-leveling.cancel-complete');
                // 完成
                Route::post('complete', 'GameLevelingController@complete')->name('order.game-leveling.complete');
                // 上架
                Route::post('on-sale', 'GameLevelingController@onSale')->name('order.game-leveling.on-sale');
                // 下架
                Route::post('off-sale', 'GameLevelingController@offSale')->name('order.game-leveling.off-sale');
                // 锁定
                Route::post('lock', 'GameLevelingController@lock')->name('order.game-leveling.lock');
                // 取消锁定
                Route::post('cancel-lock', 'GameLevelingController@cancelLock')->name('order.game-leveling.cancel-lock');
                // 提交异常
                Route::post('anomaly', 'GameLevelingController@anomaly')->name('order.game-leveling.anomaly');
                // 取消异常
                Route::post('cancel-anomaly', 'GameLevelingController@cancelAnomaly')->name('order.game-leveling.cancel-anomaly');
                // 申请撤销
                Route::post('apply-consult', 'GameLevelingController@applyConsult')->name('order.game-leveling.apply-consult');
                // 取消撤销
                Route::post('cancel-consult', 'GameLevelingController@cancelConsult')->name('order.game-leveling.cancel-consult');
                // 同意撤销
                Route::post('agree-consult', 'GameLevelingController@agreeConsult')->name('order.game-leveling.agree-consult');
                // 不同意撤销
                Route::post('reject-consult', 'GameLevelingController@rejectConsult')->name('order.game-leveling.reject-consult');
                // 强制撤单
                Route::post('force-delete', 'GameLevelingController@forceDelete')->name('order.game-leveling.force-delete');
                // 申请仲裁
                Route::post('apply-complain', 'GameLevelingController@applyComplain')->name('order.game-leveling.apply-complain');
                // 取消仲裁
                Route::post('cancel-complain', 'GameLevelingController@cancelComplain')->name('order.game-leveling.cancel-complain');
                // 客服仲裁
                Route::post('arbitration', 'GameLevelingController@arbitration')->name('order.game-leveling.arbitration');
                // 申请验收图片
                Route::get('apply-complete-image/{trade_no}', 'GameLevelingController@applyCompleteImage')->name('order.game-leveling.apply-complete-image');
                // 订单操作日志
                Route::get('log/{trade_no}', 'GameLevelingController@log')->name('order.game-leveling.log');
                // 仲裁信息
                Route::get('complain-info/{trade_no}', 'GameLevelingController@complainInfo')->name('order.game-leveling.complain-info');
                // 发送仲裁留言
                Route::post('send-complain-message', 'GameLevelingController@sendComplainMessage')->name('order.game-leveling.send-complain-message');
                // 获取订单留言
                Route::get('message/{trade_no}', 'GameLevelingController@message')->name('order.game-leveling.message');
                // 发送订单留言
                Route::post('send-message/{trade_no}', 'GameLevelingController@sendMessage')->name('order.game-leveling.send-message');
                // 留言列表
                Route::get('message-list', 'GameLevelingController@messageList')->name('order.game-leveling.message-list');
                // 删除留言
                Route::post('delete-message', 'GameLevelingController@deleteMessage')->name('order.game-leveling.delete-message');
                // 删除所有留言
                Route::post('delete-all-message', 'GameLevelingController@deleteAllMessage')->name('order.game-leveling.delete-all-message');
            });

        });

        // 财务订单
        Route::prefix('finance')->namespace('Finance')->group(function () {
            // 我的资产
            Route::get('my-asset', 'FinanceController@myAsset')->name('v2.finance.my-asset');
            Route::post('my-asset/data-list', 'FinanceController@myAssetDataList')->name('v2.finance.my-asset.data-list');
            // 资金流水
            Route::get('amount-flow', 'FinanceController@amountFlow')->name('v2.finance.amount-flow');
            Route::post('amount-flow/data-list', 'FinanceController@amountFlowDataList')->name('v2.finance.amount-flow.data-list');
        });

    });

//    Route::namespace('Auth')->group(function () {
//        // 登录
//        Route::get('/login', 'LoginController@showLoginForm')->name('login');
//        Route::post('/login', 'LoginController@login');
//        Route::post('/logout', 'LoginController@logout')->name('logout');
//        // 注册
//        Route::get('/register', 'RegisterController@showRegistrationForm')->name('register');
//        Route::post('/register', 'RegisterController@register');
//        // 密码找回
//        Route::get('/password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
//        Route::post('/password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
//        Route::get('/password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
//        Route::post('/password/reset', 'ResetPasswordController@reset');
//    });

});



