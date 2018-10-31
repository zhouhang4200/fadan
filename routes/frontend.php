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

        // 获取所有游戏
        Route::any('games', 'GameController@index')->name('games');
        // 游戏区服数据
        Route::any('game-region-server', 'GameController@gameRegionServer')->name('game-region-server');
        // 游戏代练类型
        Route::post('game-leveling-types', 'GameLevelingTypeController@index')->name('game-leveling-types');

        // 订单
        Route::prefix('order')->namespace('Order')->group(function () {

            // 代练订单
            Route::prefix('game-leveling')->group(function () {
                // 订单列表视图
                Route::get('/', 'GameLevelingController@index')->name('order.game-leveling.index');
                // 获取订单列表数据
                Route::post('data-list', 'GameLevelingController@dataList')->name('order.game-leveling.data-list');
                // 订单状态数量
                Route::post('status-quantity', 'GameLevelingController@statusQuantity')->name('order.game-leveling.status-quantity');
                // 订单详情视图
                Route::get('show/{trade_no?}', 'GameLevelingController@show')->name('order.game-leveling.show');
                // 获取待编辑数据
                Route::post('edit', 'GameLevelingController@edit')->name('order.game-leveling.edit');
                // 更新
                Route::post('update', 'GameLevelingController@update')->name('order.game-leveling.update');
                // 获取淘宝订单
                Route::post('taobao-order', 'GameLevelingController@taobaoOrder')->name('order.game-leveling.taobao-order');

                // 加价
                Route::post('add-amount', 'GameLevelingController@addAmount')->name('order.game-leveling.add-amount');
                // 加代练天与小时
                Route::post('add-day-hour', 'GameLevelingController@addDayHour')->name('order.game-leveling.add-day-hour');
                // 下单  重新下单
                Route::get('create', 'GameLevelingController@create')->name('order.game-leveling.create');
                Route::get('repeat/{trade_no?}', 'GameLevelingController@repeat')->name('order.game-leveling.repeat');
                Route::post('create', 'GameLevelingController@doCreate');
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
                Route::post('log', 'GameLevelingController@log')->name('order.game-leveling.log');
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
            // 资产日报
            Route::get('daily-asset', 'FinanceController@dailyAsset')->name('v2.finance.daily-asset');
            Route::post('daily-asset/data-list', 'FinanceController@dailyAssetDataList')->name('v2.finance.daily-asset.data-list');
            // 我的提现
            Route::get('my-withdraw', 'FinanceController@myWithdraw')->name('v2.finance.my-withdraw');
            Route::post('my-withdraw/data-list', 'FinanceController@myWithdrawDataList')->name('v2.finance.my-withdraw.data-list');
            Route::post('withdraw', 'FinanceController@withdraw')->name('v2.finance.withdraw');
            Route::post('can-withdraw', 'FinanceController@canWithdraw')->name('v2.finance.can-withdraw');
            Route::post('create-withdraw', 'FinanceController@createWithdraw')->name('v2.finance.create-withdraw');
            // 财务订单
            Route::get('order', 'FinanceController@order')->name('v2.finance.order');
            Route::post('game', 'FinanceController@game')->name('v2.finance.game');
            Route::post('order-data-list', 'FinanceController@orderDataList')->name('v2.finance.order-data-list');
        });

        // 统计
        Route::prefix('statistic')->namespace('Statistic')->group(function () {
            // 订单
            Route::get('order', 'StatisticController@order')->name('v2.statistic.order');
            Route::post('order-data-list', 'StatisticController@orderDataList')->name('v2.statistic.order-data-list');
            // 员工
            Route::get('employee', 'StatisticController@employee')->name('v2.statistic.employee');
            Route::post('employee-user', 'StatisticController@employeeUser')->name('v2.statistic.employee-user');
            Route::post('employee-data-list', 'StatisticController@employeeDataList')->name('v2.statistic.employee-data-list');
            // 短信
            Route::get('message', 'StatisticController@message')->name('v2.statistic.message');
            Route::get('message-show', 'StatisticController@messageShow')->name('v2.statistic.message-show');
            Route::post('message-data-list', 'StatisticController@messageDataList')->name('v2.statistic.message-data-list');
            Route::post('message-show-data-list', 'StatisticController@messageShowDataList')->name('v2.statistic.message-show-data-list');
        });

        // 账号
        Route::prefix('account')->namespace('Account')->group(function () {
            // 我的账号
            Route::get('mine', 'AccountController@mine')->name('v2.account.mine');
            Route::post('mine-form', 'AccountController@mineForm')->name('v2.account.mine-form');
            Route::post('mine-update', 'AccountController@mineUpdate')->name('v2.account.mine-update');
            // 登录记录
            Route::get('login-history', 'AccountController@loginHistory')->name('v2.account.login-history');
            Route::post('login-history-data-list', 'AccountController@loginHistoryDataList')->name('v2.account.login-history-data-list');
            Route::post('login-history-user', 'AccountController@loginHistoryUser')->name('v2.account.login-history-user');
            // 员工管理
            Route::get('employee', 'AccountController@employee')->name('v2.account.employee');
            Route::post('employee-user', 'AccountController@employeeUser')->name('v2.account.employee-user');
            Route::post('employee-switch', 'AccountController@employeeSwitch')->name('v2.account.employee-switch');
            Route::post('employee-station', 'AccountController@employeeStation')->name('v2.account.employee-station');
            Route::post('employee-data-list', 'AccountController@employeeDataList')->name('v2.account.employee-data-list');
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



