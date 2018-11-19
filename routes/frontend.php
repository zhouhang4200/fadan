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

Route::get('/captcha/geetest', '\Germey\Geetest\GeetestController@getGeetest');

//Route::get('/{vue?}', function (){
//    return view('frontend.spa');
//})->where('vue', '[\/\w\.-]*');

Route::prefix('v2')->namespace('Frontend\V2')->group(function () {
    # 需登录后可访问
    Route::middleware(['auth'])->group(function () {
        # 获取所有游戏
        Route::any('games', 'GameController@index')->name('games');
        # 游戏区服数据
        Route::post('game-region-server', 'GameController@gameRegionServer')->name('game-region-server');
        # 游戏代练类型
        Route::post('game-leveling-types', 'GameLevelingTypeController@index')->name('game-leveling-types');
        # 订单
        Route::prefix('order')->namespace('Order')->group(function () {
            // 代练订单
            # 代练订单
            Route::prefix('game-leveling')->namespace('GameLeveling')->group(function () {
                // 渠道订单
                Route::post('channel', 'ChannelController@index')->name('order.channel');
                Route::post('channel/agree-refund', 'ChannelController@agreeRefund')->name('order.channel.agree-refund');
                Route::post('channel/refuse-refund', 'ChannelController@refuseRefund')->name('order.channel.refuse-refund');
                Route::post('channel/status-count', 'ChannelController@statusCount')->name('order.channel.status-count');
                Route::post('channel/game', 'ChannelController@game')->name('order.channel.game');
                Route::post('channel/status', 'ChannelController@status')->name('order.channel.status');
                Route::post('channel/refund', 'ChannelController@refund')->name('order.channel.refund');
                // 获取订单
                # 获取订单
                Route::post('/', 'IndexController@index')->name('order.game-leveling');
                # 订单状态数量
                Route::post('status-quantity', 'IndexController@statusQuantity')->name('order.game-leveling.status-quantity');
                # 获取待编辑数据
                Route::post('edit', 'IndexController@edit')->name('order.game-leveling.edit');
                # 下单  重新下单
                Route::post('store', 'IndexController@store')->name('order.game-leveling.store');
                # 更新
                Route::post('update', 'IndexController@update')->name('order.game-leveling.update');
                # 加价
                Route::post('add-amount', 'IndexController@addAmount')->name('order.game-leveling.add-amount');
                # 加代练天与小时
                Route::post('add-day-hour', 'IndexController@addDayHour')->name('order.game-leveling.add-day-hour');
                # 接单
                Route::post('take', 'IndexController@take')->name('order.game-leveling.take');
                # 撤单
                Route::post('delete', 'IndexController@delete')->name('order.game-leveling.delete');
                # 申请验收
                Route::post('apply-complete', 'IndexController@applyComplete')->name('order.game-leveling.apply-complete');
                # 取消验收
                Route::post('cancel-complete', 'IndexController@cancelComplete')->name('order.game-leveling.cancel-complete');
                # 完成
                Route::post('complete', 'IndexController@complete')->name('order.game-leveling.complete');
                # 上架
                Route::post('on-sale', 'IndexController@onSale')->name('order.game-leveling.on-sale');
                # 下架
                Route::post('off-sale', 'IndexController@offSale')->name('order.game-leveling.off-sale');
                # 锁定
                Route::post('lock', 'IndexController@lock')->name('order.game-leveling.lock');
                # 取消锁定
                Route::post('cancel-lock', 'IndexController@cancelLock')->name('order.game-leveling.cancel-lock');
                # 提交异常
                Route::post('anomaly', 'IndexController@anomaly')->name('order.game-leveling.anomaly');
                # 取消异常
                Route::post('cancel-anomaly', 'IndexController@cancelAnomaly')->name('order.game-leveling.cancel-anomaly');
                # 申请撤销
                Route::post('apply-consult', 'IndexController@applyConsult')->name('order.game-leveling.apply-consult');
                # 取消撤销
                Route::post('cancel-consult', 'IndexController@cancelConsult')->name('order.game-leveling.cancel-consult');
                # 同意撤销
                Route::post('agree-consult', 'IndexController@agreeConsult')->name('order.game-leveling.agree-consult');
                # 不同意撤销
                Route::post('reject-consult', 'IndexController@rejectConsult')->name('order.game-leveling.reject-consult');
                # 强制撤单
                Route::post('force-delete', 'IndexController@forceDelete')->name('order.game-leveling.force-delete');
                # 申请仲裁
                Route::post('apply-complain', 'IndexController@applyComplain')->name('order.game-leveling.apply-complain');
                # 取消仲裁
                Route::post('cancel-complain', 'IndexController@cancelComplain')->name('order.game-leveling.cancel-complain');
                # 客服仲裁
                Route::post('arbitration', 'IndexController@arbitration')->name('order.game-leveling.arbitration');
                # 申请验收图片
                Route::post('apply-complete-image?', 'IndexController@applyCompleteImage')->name('order.game-leveling.apply-complete-image');
                # 订单操作日志
                Route::post('log', 'IndexController@log')->name('order.game-leveling.log');
                # 仲裁信息
                Route::post('complain-info', 'IndexController@complainInfo')->name('order.game-leveling.complain-info');
                # 发送仲裁留言
                Route::post('add-complain-info', 'IndexController@addComplainInfo')->name('order.game-leveling.add-complain-info');
                # 获取订单留言
                Route::post('message', 'IndexController@message')->name('order.game-leveling.message');
                # 发送订单留言
                Route::post('send-message', 'IndexController@sendMessage')->name('order.game-leveling.send-message');
                # 删除留言
                Route::post('delete-message', 'IndexController@deleteMessage')->name('order.game-leveling.delete-message');
                # 删除所有留言
                Route::post('delete-all-message', 'IndexController@deleteAllMessage')->name('order.game-leveling.delete-all-message');
                # 淘宝订单
                Route::prefix('taobao')->group(function () {
                    Route::post('/', 'TaobaoController@index')->name('order.game-leveling.taobao');
                    Route::post('show', 'TaobaoController@show')->name('order.game-leveling.taobao.show');
                    Route::post('update', 'TaobaoController@update')->name('order.game-leveling.taobao.update');
                    Route::post('status-quantity', 'TaobaoController@statusQuantity')->name('order.game-leveling.taobao.status-quantity');
                });
                # 商户投诉
                Route::prefix('businessman-complain')->group(function () {
                    Route::post('/', 'BusinessmanComplainController@index')->name('order.game-leveling.businessman-complain.data-list');
                    Route::post('images', 'BusinessmanComplainController@images')->name('order.game-leveling.businessman-complain.images');
                    Route::post('cancel', 'BusinessmanComplainController@cancel')->name('order.game-leveling.businessman-complain.cancel');
                    Route::post('store', 'BusinessmanComplainController@store')->name('order.game-leveling.businessman-complain.store');
                    Route::post('status-quantity', 'BusinessmanComplainController@statusQuantity')->name('order.game-leveling.businessman-complain.status-quantity');
                });
            });
        });
        # 财务订单
        Route::prefix('finance')->namespace('Finance')->group(function () {
            # 我的资产
            Route::get('my-asset', 'FinanceController@myAsset')->name('v2.finance.my-asset');
            Route::post('my-asset-data-list', 'FinanceController@myAssetDataList')->name('v2.finance.my-asset-data-list');
            # 资金流水
            Route::get('amount-flow', 'FinanceController@amountFlow')->name('v2.finance.amount-flow');
            Route::post('amount-flow-data-list', 'FinanceController@amountFlowDataList')->name('v2.finance.amount-flow-data-list');
            # 资产日报
            Route::get('daily-asset', 'FinanceController@dailyAsset')->name('v2.finance.daily-asset');
            Route::post('daily-asset-data-list', 'FinanceController@dailyAssetDataList')->name('v2.finance.daily-asset-data-list');
            # 我的提现
            Route::get('withdraw', 'FinanceController@myWithdraw')->name('v2.finance.withdraw');
            Route::post('withdraw-data-list', 'FinanceController@myWithdrawDataList')->name('v2.finance.withdraw-data-list');
            Route::post('withdraw', 'FinanceController@withdraw')->name('v2.finance.withdraw');
            Route::post('withdraw-can', 'FinanceController@canWithdraw')->name('v2.finance.withdraw-can');
            Route::post('withdraw-add', 'FinanceController@createWithdraw')->name('v2.finance.withdraw-add');
            # 财务订单
            Route::get('order', 'FinanceController@order')->name('v2.finance.order');
            Route::post('game', 'FinanceController@game')->name('v2.finance.game');
            Route::post('order-data-list', 'FinanceController@orderDataList')->name('v2.finance.order-data-list');
        });
        # 统计
        Route::prefix('statistic')->namespace('Statistic')->group(function () {
            # 订单
            Route::get('order', 'StatisticController@order')->name('v2.statistic.order');
            Route::post('order-data-list', 'StatisticController@orderDataList')->name('v2.statistic.order-data-list');
            # 员工
            Route::get('employee', 'StatisticController@employee')->name('v2.statistic.employee');
            Route::post('employee-user', 'StatisticController@employeeUser')->name('v2.statistic.employee-user');
            Route::post('employee-data-list', 'StatisticController@employeeDataList')->name('v2.statistic.employee-data-list');
            # 短信
            Route::get('message', 'StatisticController@message')->name('v2.statistic.message');
            Route::get('message-show', 'StatisticController@messageShow')->name('v2.statistic.message-show');
            Route::post('message-data-list', 'StatisticController@messageDataList')->name('v2.statistic.message-data-list');
            Route::post('message-show-data-list', 'StatisticController@messageShowDataList')->name('v2.statistic.message-show-data-list');
        });
        # 账号
        Route::prefix('account')->namespace('Account')->group(function () {
            # 我的账号
            Route::get('mine', 'AccountController@mine')->name('v2.account.mine');
            Route::post('mine-form', 'AccountController@mineForm')->name('v2.account.mine-form');
            Route::post('mine-update', 'AccountController@mineUpdate')->name('v2.account.mine-update');
            # 登录记录
            Route::get('login-history', 'AccountController@loginHistory')->name('v2.account.login-history');
            Route::post('login-history-data-list', 'AccountController@loginHistoryDataList')->name('v2.account.login-history-data-list');
            Route::post('login-history-user', 'AccountController@loginHistoryUser')->name('v2.account.login-history-user');
            # 员工管理
            Route::get('employee', 'AccountController@employee')->name('v2.account.employee');
            Route::post('employee-user', 'AccountController@employeeUser')->name('v2.account.employee-user');
            Route::post('employee-switch', 'AccountController@employeeSwitch')->name('v2.account.employee-switch');
            Route::post('employee-station', 'AccountController@employeeStation')->name('v2.account.employee-station');
            Route::post('employee-data-list', 'AccountController@employeeDataList')->name('v2.account.employee-data-list');
            Route::post('employee-update', 'AccountController@employeeUpdate')->name('v2.account.employee-update');
            Route::post('employee-delete', 'AccountController@employeeDelete')->name('v2.account.employee-delete');
            Route::get('employee-create', 'AccountController@employeeCreate')->name('v2.account.employee-create');
            Route::post('employee-add', 'AccountController@employeeAdd')->name('v2.account.employee-add');
            Route::post('employee-form', 'AccountController@employeeForm')->name('v2.account.employee-form');
            # 打手黑名单
            Route::get('black-list', 'AccountController@blackList')->name('v2.account.black-list');
            Route::post('black-list-data-list', 'AccountController@blackListDataList')->name('v2.account.black-list-data-list');
            Route::post('black-list-add', 'AccountController@blackListAdd')->name('v2.account.black-list-add');
            Route::post('black-list-update', 'AccountController@blackListUpdate')->name('v2.account.black-list-update');
            Route::post('black-list-delete', 'AccountController@blackListDelete')->name('v2.account.black-list-delete');
            Route::post('black-list-name', 'AccountController@blackListName')->name('v2.account.black-list-name');
            # 实名认证
            Route::get('authentication', 'AccountController@authentication')->name('v2.account.authentication');
            Route::post('authentication-form', 'AccountController@authenticationForm')->name('v2.account.authentication-form');
            Route::post('authentication-add', 'AccountController@authenticationAdd')->name('v2.account.authentication-add');
            Route::post('authentication-update', 'AccountController@authenticationUpdate')->name('v2.account.authentication-update');
            Route::post('authentication-upload', 'AccountController@authenticationUpLoad')->name('v2.account.authentication-upload');
            # 岗位管理
            Route::get('station', 'AccountController@station')->name('v2.account.station');
            Route::post('station-data-list', 'AccountController@stationDataList')->name('v2.account.station-data-list');
            Route::post('station-update', 'AccountController@stationUpdate')->name('v2.account.station-update');
            Route::post('station-delete', 'AccountController@stationDelete')->name('v2.account.station-delete');
            Route::post('station-add', 'AccountController@stationAdd')->name('v2.account.station-add');
            Route::post('station-form', 'AccountController@stationForm')->name('v2.account.station-form');
            Route::post('station-permission', 'AccountController@stationPermission')->name('v2.account.station-permission');
        });
        # 设置
        Route::prefix('setting')->namespace('Setting')->group(function () {
            # 抓取商品配置
            Route::get('goods', 'SettingController@goods')->name('v2.setting.goods');
            Route::post('goods-delivery', 'SettingController@goodsDelivery')->name('v2.setting.goods-delivery');
            Route::post('goods-game', 'SettingController@goodsGame')->name('v2.setting.goods-game');
            Route::post('goods-seller-nick', 'SettingController@goodsSellerNick')->name('v2.setting.goods-seller-nick');
            Route::post('goods-add', 'SettingController@goodsAdd')->name('v2.setting.goods-add');
            Route::post('goods-update', 'SettingController@goodsUpdate')->name('v2.setting.goods-update');
            Route::post('goods-delete', 'SettingController@goodsDelete')->name('v2.setting.goods-delete');
            Route::post('goods-data-list', 'SettingController@goodsDataList')->name('v2.setting.goods-data-list');
            # 短信管理
            Route::get('message', 'SettingController@message')->name('v2.setting.message');
            Route::post('message-status', 'SettingController@messageStatus')->name('v2.setting.message-status');
            Route::post('message-update', 'SettingController@messageUpdate')->name('v2.setting.message-update');
            Route::post('message-data-list', 'SettingController@messageDataList')->name('v2.setting.message-data-list');
            # 店铺授权
            Route::get('authorize', 'SettingController@authorizeIndex')->name('v2.setting.authorize');
            Route::post('authorize-delete', 'SettingController@authorizeDelete')->name('v2.setting.authorize-delete');
            Route::post('authorize-url', 'SettingController@authorizeUrl')->name('v2.setting.authorize-url');
            Route::post('authorize-data-list', 'SettingController@authorizeDataList')->name('v2.setting.authorize-data-list');
            # 代练发单辅助加价
            Route::get('auxiliary', 'SettingController@auxiliary')->name('v2.setting.auxiliary');
            Route::post('markup-add', 'SettingController@markupAdd')->name('v2.setting.markup-add');
            Route::post('markup-update', 'SettingController@markupUpdate')->name('v2.setting.markup-update');
            Route::post('markup-delete', 'SettingController@markupDelete')->name('v2.setting.markup-delete');
            Route::post('markup-data-list', 'SettingController@markupDataList')->name('v2.setting.markup-data-list');
            # 代练发单辅助渠道设置
            Route::post('channel-switch', 'SettingController@channelSwitch')->name('v2.setting.channel-switch');
            Route::post('channel-data-list', 'SettingController@channelDataList')->name('v2.setting.channel-data-list');
        });
    });
});

