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

Route::middleware(['auth'])->namespace('Frontend')->group(function () {
	// 首页
	Route::get('/', 'HomeController@index')->name('frontend.index');
    // 代练留言
    Route::prefix('message')->group(function (){
        Route::get('/', 'LevelingMessageController@index')->name('frontend.message-list');
        Route::post('del', 'LevelingMessageController@del')->name('frontend.message-del');
        Route::post('del-all', 'LevelingMessageController@delAll')->name('frontend.message-del-all');
    });

    // 修改资料，上传头像
    Route::prefix('users')->namespace('User')->group(function () {
        Route::get('persional', 'UserController@persional')->name('users.persional');
        Route::post('persional', 'UserController@updatePersional')->name('users.update-persional');
        Route::post('voucher', 'UserController@updateVoucher')->name('users.update-voucher');
        Route::post('upload-images', 'UserController@uploadImages')->name('users.upload-images');
    });

    // 奖惩记录
    Route::prefix('punish')->namespace('Punish')->group(function () {
        Route::get('home-punishes', 'PunishController@index')->name('home-punishes.index')->middleware('new.permission:home-punishes.index');
        Route::post('home-punishes/payment', 'PunishController@payment')->name('home-punishes.payment'); // 付款
        Route::post('home-punishes/complain', 'PunishController@complain')->name('home-punishes.complain'); //申诉
    });

	// 实名认证
	Route::get('idents', 'IdentController@index')->name('idents.index');
	Route::get('idents/create', 'IdentController@create')->name('idents.create');
	Route::post('idents', 'IdentController@store')->name('idents.store');
	Route::get('idents/{id}/edit', 'IdentController@edit')->name('idents.edit');
	Route::put('idents/{id}', 'IdentController@update')->name('idents.update');
	Route::post('upload-images', 'IdentController@uploadImages')->name('ident.upload-images');

	// 系统日志
	Route::get('home-system-logs', 'SystemLogController@index')->name('home-system-logs.index')->middleware('new.permission:home-system-logs.index');
    // 登录记录
    Route::get('login/history', 'LoginController@history')->name('login.history');
    // 我的账号
    Route::get('home-accounts', 'AccountController@index')->name('home-accounts.index');
    Route::get('home-accounts/{id}/edit', 'AccountController@edit')->name('home-accounts.edit');
    Route::post('home-accounts', 'AccountController@update')->name('home-accounts.update');

    // 代练员工管理
    Route::prefix('staff-management')->namespace('Account')->group(function () {
        Route::get('index', 'StaffManagementController@index')->name('staff-management.index')->middleware('new.permission:staff-management.index'); // 员工列表
        Route::post('forbidden', 'StaffManagementController@forbidden')->name('staff-management.forbidden'); // 子账号禁用
        Route::get('edit/{id}', 'StaffManagementController@edit')->name('staff-management.edit')->where('id', '[0-9]+'); // 员工编辑
        Route::post('update', 'StaffManagementController@update')->name('staff-management.update'); // 提交员工编辑
        Route::delete('delete', 'StaffManagementController@delete')->name('staff-management.delete'); // 删除员工
        Route::get('create', 'StaffManagementController@create')->name('staff-management.create');
        Route::post('store', 'StaffManagementController@store')->name('staff-management.store');
    });

    // 岗位管理
    Route::prefix('station')->namespace('Account')->group(function () {
        Route::get('/', 'StationController@index')->name('station.index')->middleware('new.permission:station.index');
        Route::get('create', 'StationController@create')->name('station.create');
        Route::post('/', 'StationController@store')->name('station.store');
        Route::get('edit/{id}', 'StationController@edit')->name('station.edit');
        Route::post('update', 'StationController@update')->name('station.update');
        Route::post('destroy', 'StationController@destroy')->name('station.destroy');
    });

    // 打手黑名单
    Route::namespace('Account')->group(function () {
        Route::prefix('hatchet-man-blacklist')->group(function () {
            Route::get('/', 'HatchetManBlacklistController@index')->name('hatchet-man-blacklist.index')->middleware('new.permission:hatchet-man-blacklist.index');
            Route::get('create', 'HatchetManBlacklistController@create')->name('hatchet-man-blacklist.create');
            Route::post('/', 'HatchetManBlacklistController@store')->name('hatchet-man-blacklist.store');
            Route::get('edit/{id}', 'HatchetManBlacklistController@edit')->name('hatchet-man-blacklist.edit');
            Route::post('update', 'HatchetManBlacklistController@update')->name('hatchet-man-blacklist.update');
            Route::post('delete', 'HatchetManBlacklistController@delete')->name('hatchet-man-blacklist.delete');
        });
    });
    // 统计
    Route::prefix('statistic')->namespace('Statistic')->group(function () {
        // 员工数据统计
        Route::get('employee', 'StatisticController@todayData')->name('frontend.statistic.employee')->middleware('new.permission:frontend.statistic.employee');
        // 订单统计
        Route::get('order', 'StatisticController@order')->name('frontend.statistic.order')->middleware('new.permission:frontend.statistic.order');
        // 短信
        Route::prefix('sms')->group(function (){
            Route::get('/', 'SmsController@index')->name('frontend.statistic.sms')->middleware('new.permission:frontend.statistic.sms');
            Route::get('show/{date}', 'SmsController@show')->name('frontend.statistic.show');
        });
        // 当日
        Route::get('today', 'StatisticController@todayData')->name('frontend.statistic.today');
    });

    // 商品
    Route::prefix('goods')->namespace('Goods')->group(function () {
        // 商品列表
        Route::get('/', 'GoodsController@index')->name('frontend.goods.index')->middleware('new.permission:frontend.goods.index');
        // 添加视图
        Route::get('create', 'GoodsController@create')->name('frontend.goods.create');
        // 保存商品
        Route::post('store', 'GoodsController@store')->name('frontend.goods.store');
        // 编辑视图
        Route::get('edit/{id}', 'GoodsController@edit')->name('frontend.goods.edit');
        // 修改商品
        Route::post('update', 'GoodsController@update')->name('frontend.goods.update');
        // 删除商品
        Route::post('destroy', 'GoodsController@destroy')->name('frontend.goods.destroy');
    });

    // 订单
    Route::prefix('order')->namespace('Order')->group(function () {
        // 接单列表
        Route::get('receive', 'OrderController@receive')->name('frontend.order.receive')->middleware('new.permission:frontend.order.receive');
        // 发单列表
        Route::get('send', 'OrderController@send')->name('frontend.order.send')->middleware('new.permission:frontend.order.send');
    });

    // 用户设置
    Route::namespace('Setting')->prefix('setting')->group(function () {

        // 设置 - 商户联系方式模板
        Route::prefix('businessman-contact')->group(function () {
            Route::get('/', 'BusinessmanController@index')->name('frontend.setting.setting.businessman-contact.index');
            Route::post('store', 'BusinessmanController@store')->name('frontend.setting.businessman-contact.store');
            Route::post('destroy', 'BusinessmanController@destroy')->name('frontend.setting.businessman-contact.destroy');
        });

        // 接单权限
        Route::prefix('receiving-control')->group(function () {
            Route::get('/', 'ReceivingControlController@index')->name('frontend.setting.receiving-control.index')->middleware('new.permission:frontend.setting.receiving-control.index');
            Route::get('get-control-user', 'ReceivingControlController@getControlUser')->name('frontend.setting.receiving-control.get-control-user');
            Route::get('get-control-category', 'ReceivingControlController@getControlCategory')->name('frontend.setting.receiving-control.get-control-category');
            Route::get('get-control-goods', 'ReceivingControlController@getControlGoods')->name('frontend.setting.receiving-control.get-control-goods');
            Route::post('add-user', 'ReceivingControlController@addUser')->name('frontend.setting.receiving-control.add-user');
            Route::post('add-category', 'ReceivingControlController@addCategory')->name('frontend.setting.receiving-control.add-category');
            Route::post('add-goods', 'ReceivingControlController@addGoods')->name('frontend.setting.receiving-control.add-goods');
            Route::post('delete-control-user', 'ReceivingControlController@deleteControlUser')->name('frontend.setting.receiving-control.delete-control-user');
            Route::post('delete-control-category', 'ReceivingControlController@deleteControlCategory')->name('frontend.setting.receiving-control.delete-control-category');
            Route::post('delete-control-goods', 'ReceivingControlController@deleteControlGoods')->name('frontend.setting.receiving-control.delete-control-goods');
            Route::post('control-mode', 'ReceivingControlController@controlMode')->name('frontend.setting.receiving-control.control-mode');
        });
        // 设置 - 发单设置
        Route::prefix('sending-control')->group(function () {
            Route::get('/', 'SendingController@index')->name('frontend.setting.sending-control.index')->middleware('new.permission:frontend.setting.sending-control.index');
            Route::post('change', 'SendingController@change')->name('frontend.setting.sending-control.change');
        });
        // 设置-发单辅助设置
        Route::prefix('sending-assist')->group(function () {
            // 代练要求模板
            Route::prefix('require')->group(function () {
                Route::get('/', 'SendingAssistController@require')->name('frontend.setting.sending-assist.require');
                Route::get('create', 'SendingAssistController@requireCreate')->name('frontend.setting.sending-assist.require.create');
                Route::post('store', 'SendingAssistController@requireStore')->name('frontend.setting.sending-assist.require.store');
                Route::get('edit/{id}', 'SendingAssistController@requireEdit')->name('frontend.setting.sending-assist.require.edit');
                Route::post('update', 'SendingAssistController@requireUpdate')->name('frontend.setting.sending-assist.require.update');
                Route::post('destroy', 'SendingAssistController@requireDestroy')->name('frontend.setting.sending-assist.require.destroy');
                Route::post('set', 'SendingAssistController@requireSet')->name('frontend.setting.sending-assist.require.set');
                // 获取代练模版弹窗
                Route::get('pop', 'SendingAssistController@requirePop')->name('frontend.setting.sending-assist.require.pop');
                Route::post('pop-store', 'SendingAssistController@requirePopStore')->name('frontend.setting.sending-assist.require.pop.store');
            });
            // 自动加价配置
            Route::prefix('auto-markup')->group(function () {
                Route::get('', 'SendingAssistController@autoMarkup')->name('frontend.setting.sending-assist.auto-markup')->middleware('new.permission:frontend.setting.sending-assist.auto-markup');
                Route::get('create', 'SendingAssistController@autoMarkupCreate')->name('frontend.setting.sending-assist.auto-markup.create');
                Route::post('store', 'SendingAssistController@autoMarkupStore')->name('frontend.setting.sending-assist.auto-markup.store');
                Route::get('edit/{id}', 'SendingAssistController@autoMarkupEdit')->name('frontend.setting.sending-assist.auto-markup.edit');
                Route::post('update', 'SendingAssistController@autoMarkupUpdate')->name('frontend.setting.sending-assist.auto-markup.update');
                Route::post('destroy', 'SendingAssistController@autoMarkupDestroy')->name('frontend.setting.sending-assist.auto-markup.destroy');
            });
            // 发单渠道设置
            Route::prefix('order-send-channel')->group(function () {
                Route::get('/', 'OrderSendChannelController@index')->name('frontend.setting.order-send-channel.index')->middleware('new.permission:frontend.setting.order-send-channel.index');
                Route::post('set', 'OrderSendChannelController@set')->name('frontend.setting.order-send-channel.set');
            });
        });
        // api 风控设置
        Route::prefix('api-risk-management')->group(function () {
            Route::get('/', 'ApiRiskManagementController@index')->name('frontend.setting.api-risk-management.index')->middleware('new.permission:frontend.setting.api-risk-management.index');
            Route::post('set', 'ApiRiskManagementController@set')->name('frontend.setting.api-risk-management.set');
        });
        // 皮肤交易设置
        Route::prefix('skin')->group(function () {
            Route::get('/', 'SkinController@index')->name('frontend.setting.skin.index')->middleware('new.permission:frontend.setting.skin.index');
            Route::post('set', 'SkinController@set')->name('frontend.setting.skin.set');
        });
        // 自动抓取订单配置
        Route::prefix('automatically-grab')->group(function (){
            Route::get('goods', 'AutomaticallyGrabController@goods')->name('frontend.setting.automatically-grab.goods')->middleware('new.permission:frontend.setting.automatically-grab.goods');
            Route::post('add', 'AutomaticallyGrabController@add')->name('frontend.setting.automatically-grab.add');
            Route::post('delete', 'AutomaticallyGrabController@delete')->name('frontend.setting.automatically-grab.delete');
            Route::post('show', 'AutomaticallyGrabController@show')->name('frontend.setting.automatically-grab.show');
            Route::post('edit', 'AutomaticallyGrabController@edit')->name('frontend.setting.automatically-grab.edit');
            Route::post('delivery', 'AutomaticallyGrabController@delivery')->name('frontend.setting.automatically-grab.delivery');
        });
        // 短信管理
        Route::prefix('sms')->group(function () {
            Route::get('/', 'SmsController@index')->name('frontend.setting.sms.index')->middleware('new.permission:frontend.setting.sms.index');
            Route::post('show', 'SmsController@show')->name('frontend.setting.sms.show');
            Route::post('add', 'SmsController@add')->name('frontend.setting.sms.add');
            Route::post('edit', 'SmsController@edit')->name('frontend.setting.sms.edit');
            Route::post('delete', 'SmsController@delete')->name('frontend.setting.sms.delete');
            Route::post('status', 'SmsController@status')->name('frontend.setting.sms.status');
        });
        // 店铺抓取订单授权
        Route::prefix('tb-auth')->group(function () {
            Route::get('/', 'TbAuthController@index')->name('frontend.setting.tb-auth.index')->middleware('new.permission:frontend.setting.tb-auth.index');
            Route::get('store', 'TbAuthController@store')->name('frontend.setting.tb-auth.store');
            Route::post('destroy', 'TbAuthController@destroy')->name('frontend.setting.tb-auth.destroy');
        });
    });

	// 财务
	Route::namespace('Finance')->prefix('finance')->group(function () {
		// 我的资产
	    Route::get('asset', 'AssetController@index')->name('frontend.finance.asset')->middleware('new.permission:frontend.finance.asset');
	    // 资金流水
	    Route::get('amount-flow', 'AmountFlowController@index')->name('frontend.finance.amount-flow')->middleware('new.permission:frontend.finance.amount-flow');
	    // 资金流水导出
	    Route::get('amount-flow/export', 'AmountFlowController@export')->name('frontend.finance.amount-flow.export');
	    // 资产日报
        Route::get('asset-daily', 'AssetDailyController@index')->name('frontend.finance.asset-daily')->middleware('new.permission:frontend.finance.asset-daily');
        // 余额提现
        Route::post('withdraw-order/store', 'WithdrawOrderController@store')->name('frontend.finance.withdraw-order.store')->middleware('new.permission:frontend.finance.withdraw-order.store');
		// 我的提现
        Route::get('withdraw-order', 'WithdrawOrderController@index')->name('frontend.finance.withdraw-order')->middleware('new.permission:frontend.finance.withdraw-order');
        // 财务订单报表
        Route::get('order-report', 'OrderReportController@index')->name('frontend.finance.order-report.index');
        Route::get('order-report/export', 'OrderReportController@export')->name('frontend.finance.order-report.export');
        // 内部欠款订单
        Route::get('month-settlement-orders', 'MonthSettlementOrdersController@index')->name('frontend.finance.month-settlement-orders.index');
        Route::get('month-settlement-orders/export', 'MonthSettlementOrdersController@export')->name('frontend.finance.month-settlement-orders.export');
        Route::post('month-settlement-orders/settlement', 'MonthSettlementOrdersController@settlement')->name('frontend.finance.month-settlement-orders.settlement');
        // 短信充值
        Route::prefix('sms')->group(function () {
            Route::post('recharge', 'SmsController@recharge')->name('frontend.finance.sms.recharge');
        });
	});

	// 工作台
	Route::namespace('Workbench')->prefix('workbench')->group(function () {

        // 首页
        Route::get('/', 'IndexController@index')->name('frontend.workbench.index')->middleware('new.permission:frontend.workbench.index');


        // 清空角标
        Route::post('clear-count', 'IndexController@clearCount')->name('frontend.workbench.clear-count');
        // 代充
        Route::namespace('Recharge')->prefix('recharge')->group(function (){
            // 首页
            Route::get('/', 'IndexController@index')->name('frontend.workbench.recharge.index')->middleware('new.permission:frontend.workbench.recharge.index');
            // 订单操作
            Route::prefix('order-operation')->group(function (){
                // 订单详情
                Route::get('detail', 'OrderOperationController@detail')->name('frontend.workbench.order-operation.detail')->middleware('new.permission:frontend.workbench.order-operation.detail');
                // 订单发货
                Route::post('delivery', 'OrderOperationController@delivery')->name('frontend.workbench.order-operation.delivery')->middleware('new.permission:frontend.workbench.order-operation.delivery');
                // 失败订单
                Route::post('fail', 'OrderOperationController@fail')->name('frontend.workbench.order-operation.fail')->middleware('new.permission:frontend.workbench.order-operation.fail');
                // 取消订单
                Route::post('cancel', 'OrderOperationController@cancel')->name('frontend.workbench.order-operation.cancel')->middleware('new.permission:frontend.workbench.order-operation.cancel');
                // 确认收货
                Route::post('confirm', 'OrderOperationController@confirm')->name('frontend.workbench.order-operation.confirm')->middleware('new.permission:frontend.workbench.order-operation.confirm');
                // 返回集市
                Route::post('turn-back', 'OrderOperationController@turnBack')->name('frontend.workbench.order-operation.turnBack')->middleware('new.permission:frontend.workbench.order-operation.turnBack');
                // 申请售后
                Route::post('after-sales', 'OrderOperationController@afterSales')->name('frontend.workbench.order-operation.after-sales')->middleware('new.permission:frontend.workbench.order-operation.after-sales');
                // 接单
                Route::group(['middleware'=>'throttle:40'],function(){
                    Route::post('receiving', 'OrderOperationController@receiving')->name('frontend.workbench.order-operation.receiving')->middleware('new.permission:frontend.workbench.order-operation.receiving');
                });
                // 支付
                Route::post('payment', 'OrderOperationController@payment')->name('frontend.workbench.order-operation.payment')->middleware('new.permission:frontend.workbench.order-operation.payment');
            });
        });
        // 代练
        Route::namespace('Leveling')->prefix('leveling')->group(function (){


            Route::get('new-index', 'IndexController@newIndex')->name('frontend.workbench.leveling.new-index');


            // 获取下单项的子菜单
            Route::post('get-select-child', 'IndexController@getSelectChild')->name('frontend.workbench.get-select-child');
            // 首页
            Route::get('/', 'IndexController@indexNew')->name('frontend.workbench.leveling.index')->middleware('new.permission:frontend.workbench.leveling.index');
            Route::any('test', 'IndexController@indexNew')->name('frontend.workbench.leveling.v1.index');
            // 根据订单状态获取订单数据
            Route::any('order-list', 'IndexController@orderList')->name('frontend.workbench.leveling.order-list');
            // 获取订单各状态的数量
            Route::post('order-status-count', 'IndexController@orderStatusCount')->name('frontend.workbench.leveling.order-status-count');
            // 创建订单
            Route::get('create', 'IndexController@create')->name('frontend.workbench.leveling.create')->middleware('new.permission:frontend.workbench.leveling.create');
            // 确认下单
            Route::post('create', 'IndexController@order')->name('frontend.workbench.leveling.create');
            // 获取模版
            Route::post('get-template', 'IndexController@getTemplate')->name('frontend.workbench.leveling.get-template');
            // 获取订单详情
            Route::any('detail', 'IndexController@detail')->name('frontend.workbench.leveling.detail');
            // 订单操作记录
            Route::get('history/{order_no}', 'IndexController@history')->name('frontend.workbench.leveling.history');
            // 订单留言
            Route::get('leave-message/{order_no}', 'IndexController@leaveMessage')->name('frontend.workbench.leveling.leave-message');
            // 发送新订单留言
            Route::post('send-message', 'IndexController@sendMessage')->name('frontend.workbench.leveling.send-message');
            // 获取订单截图
            Route::get('leave-image/{order_no?}', 'IndexController@leaveImage')->name('frontend.workbench.leveling.leave-image');
            // 获取订单截图
            Route::post('upload-image', 'IndexController@uploadImage')->name('frontend.workbench.leveling.upload-image');
            // 修改订单
            Route::post('update', 'IndexController@update')->name('frontend.workbench.leveling.update');
            // 改状态操作
            Route::post('status', 'IndexController@changeStatus')->name('frontend.workbench.leveling.status');
            // 撤销
            Route::post('consult', 'IndexController@consult')->name('frontend.workbench.leveling.consult');
            // 申诉
            Route::post('complain', 'IndexController@complain')->name('frontend.workbench.leveling.complain');
            // 跳转到新页面导出 excel
            Route::get('excel', 'IndexController@excel')->name('frontend.workbench.leveling.excel');
            // 修改订单备注
            Route::post('remark', 'IndexController@remark')->name('frontend.workbench.leveling.remark');
            // 订单操作记录
            Route::post('operation-record', 'IndexController@operationRecord')->name('frontend.workbench.leveling.operation-record');

            // 重发
            Route::get('repeat/{id?}', 'IndexController@repeat')->name('frontend.workbench.leveling.repeat');
            // 发送短信
            Route::post('send-sms', 'IndexController@sendSms')->name('frontend.workbench.leveling.send-sms');
            // 获取代练模版 
            Route::post('game-leveling-template', 'IndexController@getGameLevelingTemplate')->name('frontend.workbench.leveling.game-leveling-template');
            // 获取来源价格
            Route::post('source-price', 'IndexController@sourcePrice')->name('frontend.workbench.leveling.source-price');
            // 获取区服
            Route::post('get-region-type', 'IndexController@getRegionType')->name('frontend.workbench.leveling.get-region-type');
            // 加价
            Route::post('add-amount', 'IndexController@addAmount')->name('frontend.workbench.leveling.add-amount');
            // 加时
            Route::post('add-time', 'IndexController@addTime')->name('frontend.workbench.leveling.add-time');
            // 置顶
            Route::post('set-top', 'IndexController@setTop')->name('frontend.workbench.leveling.set-top');
            // 获取仲裁证据
            Route::get('arbitration-info', 'IndexController@getArbitrationInfo')->name('frontend.workbench.leveling.arbitration-info');
            // 发送仲裁证据
            Route::post('add-arbitration', 'IndexController@addArbitrationInfo')->name('frontend.workbench.leveling.add-arbitration');

            // 获取游戏代练类型
            Route::post('get-game-leveling-type', 'IndexController@getGameLevelingType')->name('frontend.workbench.get-game-leveling-type');
            // DNF 区服解析
            Route::post('dnf-serve/{region?}', 'IndexController@dnfRegionServe')->name('frontend.workbench.leveling.dnf-serve');
            // 待发订单
            Route::prefix('wait')->group(function(){
                // 待发单列表
                Route::get('/', 'WaitController@index')->name('frontend.workbench.leveling.wait')->middleware('new.permission:frontend.workbench.leveling.wait');
                // 待发单数据
                Route::post('order-list', 'WaitController@orderList')->name('frontend.workbench.leveling.wait-order-list');
                // 待接单数据更新
                Route::post('update', 'WaitController@update')->name('frontend.workbench.leveling.wait-update');
                // 待接单备注修改
                Route::post('remark', 'WaitController@remark')->name('frontend.workbench.leveling.wait-remark');
                // 待发单增加处理倒计时
                Route::post('time', 'WaitController@time')->name('frontend.workbench.leveling.wait-time');
                // 排序
                Route::post('sort', 'WaitController@sort')->name('frontend.workbench.leveling.wait-sort');
            });
            // 订单投诉
            Route::prefix('complaints')->group(function(){
                // 列表
                Route::get('/', 'ComplaintsController@index')->name('frontend.workbench.leveling.complaints');
                // 发起投诉
                Route::post('/', 'ComplaintsController@store')->name('frontend.workbench.leveling.complaints');
                // 查看
                Route::post('show', 'ComplaintsController@show')->name('frontend.workbench.leveling.complaints-show');
                // 取消投诉
                Route::post('cancel', 'ComplaintsController@cancel')->name('frontend.workbench.leveling.complaints-cancel');
                // 数据
                Route::post('list-data', 'ComplaintsController@listData')->name('frontend.workbench.leveling.complaints-list-data');
                // 投诉图片
                Route::post('images', 'ComplaintsController@images')->name('frontend.workbench.leveling.complaints.images');
            });

            // 通过游戏获取区/服/代练类型
            Route::post('regions', 'IndexController@regions')->name('frontend.workbench.regions');
            Route::post('servers', 'IndexController@servers')->name('frontend.workbench.servers');
            // 新的下单
            Route::get('new-create', 'IndexController@newCreate')->name('frontend.workbench.leveling.new-create');
            Route::post('new-order', 'IndexController@newOrder')->name('frontend.workbench.leveling.new-order');
         });
        // 获取用户所有前台可显示的商品
        Route::post('goods', 'IndexController@goods')->name('frontend.workbench.goods');
        // 商品模版
        Route::post('template', 'IndexController@template')->name('frontend.workbench.template');
        // 获取子级的值
        Route::post('child', 'IndexController@widgetChild')->name('frontend.workbench.widget.child');
        // 下单
        Route::post('order', 'IndexController@order')->name('frontend.workbench.order');
        // 订单列表
        Route::group(['middleware'=>'throttle:40'],function(){
            Route::post('order-list', 'IndexController@orderList')->name('frontend.workbench.order-list');
        });
        // 清空急需处理数量角标
        Route::post('clear-wait-handle-quantity', 'IndexController@waitHandleQuantityClear')->name('frontend.workbench.clear-wait-handle-quantity');
        // 修改当前账号状态
        Route::post('set-status', 'IndexController@setStatus')->name('frontend.workbench.set-status');
    });

    Route::namespace('Data')->prefix('data')->group(function () {
        // 日常数据
        Route::get('index', 'DataController@index')->name('data.index');
    });

    //上传excel
    Route::group(['prefix' => 'file'], function () {
        Route::post('fileExcel', 'FileController@fileExcel')->name('file.fileExcel');
    });

    // steam
    Route::namespace('Steam')->prefix('steam')->group(function () {
        // 商品
        Route::prefix('goods')->group(function () {
            // 商品列表
            Route::get('/', 'GoodsController@index')->name('frontend.steam.goods.index')->middleware('new.permission:frontend.steam.goods.index');;
            // 添加视图
            Route::get('create', 'GoodsController@create')->name('frontend.steam.goods.create');
            // 审核商品
            Route::get('examine-goods', 'GoodsController@examineGoods')->name('frontend.steam.examine.examine-goods');
            // 保存商品
            Route::post('store', 'GoodsController@store')->name('frontend.steam.goods.store');
            // 编辑视图
            Route::get('edit/{id}', 'GoodsController@edit')->name('frontend.steam.goods.edit');
            // 修改商品
            Route::post('update', 'GoodsController@update')->name('frontend.steam.goods.update');
            // 删除商品
            Route::post('destroy', 'GoodsController@destroy')->name('frontend.steam.goods.destroy');
            //文件上传
            Route::post('upload-images', 'GoodsController@uploadImages')->name('frontend.steam.goods.upload-images');
        });

        // 平台卡
        Route::prefix('cdkey')->group(function () {
            // 平台卡列表
            Route::get('/', 'CdkeyController@index')->name('frontend.steam.cdkey.index');
            // 添加视图
            Route::get('create', 'CdkeyController@create')->name('frontend.steam.cdkey.create');
            // 查看
            Route::get('/{id}', 'CdkeyController@show')->name('frontend.steam.cdkey.show');
            // 保存商品
            Route::post('store', 'CdkeyController@store')->name('frontend.steam.cdkey.store');
            // 编辑视图
            Route::get('edit/{id}', 'CdkeyController@edit')->name('frontend.steam.cdkey.edit');
            // 修改商品
            Route::post('update', 'CdkeyController@update')->name('frontend.steam.cdkey.update');
            // 删除商品
            Route::post('destroy', 'CdkeyController@destroy')->name('frontend.steam.cdkey.destroy');
            // 是否冻结
            Route::patch('is-frozen', 'CdkeyController@isFrozen')->name('frontend.steam.cdkey.isrozen');

            Route::post('remarks', 'CdkeyController@remarks')->name('frontend.steam.cdkey.remarks');
        });

        //steam充值
        Route::group(['prefix' => 'card'], function () {
            Route::get('recharge', 'BatchCardController@getAccountList')->name('frontend.steam.card.recharge');
            Route::post('import-card', 'BatchCardController@importCard')->name('frontend.steam.card.import-card');
            Route::get('send/status', 'BatchCardController@updateStatus');
            Route::post('all', 'BatchCardController@all')->name('frontend.steam.card.all');
            Route::post('balance', 'BatchCardController@balance')->name('frontend.steam.card.balance');
            Route::post('updateZhichongState', 'BatchCardController@updateZhichongState')->name('frontend.steam.card.updateZhichongState');
            Route::post('updatePwd', 'BatchCardController@updatePwd')->name('frontend.steam.card.updatePwd');
            Route::get('show', 'BatchCardController@show')->name('frontend.steam.card.show');
            //取号使用记录
            Route::get('list', 'BatchCardController@listData')->name('frontend.steam.card.list');
            Route::get('seal', 'BatchCardController@seal')->name('frontend.steam.card.seal');
            Route::get('zclist', 'BatchCardController@getZhiChongList')->name('frontend.steam.card.zclist');

            Route::get('game', 'BatchCardController@game')->name('frontend.steam.card.game');
            Route::post('addGame', 'BatchCardController@addGame')->name('frontend.steam.card.addGame');
            Route::post('delGameTmp', 'BatchCardController@delGameTmp')->name('frontend.steam.card.delGameTmp');
            Route::post('updateIsUsing', 'BatchCardController@updateIsUsing')->name('frontend.steam.card.updateIsUsing');
            Route::post('updateAuthType', 'BatchCardController@updateAuthType')->name('frontend.steam.card.updateAuthType');
        });

        // 平台卡库
        Route::prefix('cdkeylibrary')->group(function () {

            Route::get('/', 'CdkeyLibraryController@index')->name('frontend.steam.cdkeylibrary.index');

            Route::get('search', 'CdkeyLibraryController@search')->name('frontend.steam.cdkeylibrary.search');
            // 是否冻结
            Route::patch('is-something', 'CdkeyLibraryController@isSomething')->name('frontend.steam.cdkeylibrary.isSomething');

        });

        // 订单
        Route::prefix('order')->group(function () {
            // 订单列表
            Route::get('/', 'OrderController@index')->name('frontend.steam.order.index');
        });

    });
    // 宝贝参谋
    Route::prefix('baby')->namespace('Baby')->group(function() {
        Route::get('/', 'BabyAdviserController@index')->name('frontend.baby.index')->middleware('new.permission:frontend.baby.index');
        Route::get('show', 'BabyAdviserController@show')->name('frontend.baby.show')->middleware('new.permission:frontend.baby.show');
    });
});

//  手机端
Route::namespace('Mobile')->prefix('mobile')->group(function () {
    // 手机端标品下单
    Route::prefix('leveling')->group(function () {
        Route::get('demand', 'LevelingController@demand')->name('mobile.leveling.demand'); // 代练下单配置
        Route::post('games', 'LevelingController@games')->name('mobile.leveling.games'); // 代练游戏
        Route::post('types', 'LevelingController@types')->name('mobile.leveling.types'); // 代练类型
        Route::post('targets', 'LevelingController@targets')->name('mobile.leveling.targets'); // 代练目标
        Route::post('compute', 'LevelingController@compute')->name('mobile.leveling.compute'); // 计算价格和时间
        Route::post('go', 'LevelingController@go')->name('mobile.leveling.go'); 

        Route::get('place-order', 'LevelingController@placeOrder')->name('mobile.leveling.place-order'); // 下单配置界面
        Route::post('regions', 'LevelingController@regions')->name('mobile.leveling.regions'); // 区
        Route::post('servers', 'LevelingController@servers')->name('mobile.leveling.servers'); // 服
        Route::post('pay', 'LevelingController@pay')->name('mobile.leveling.pay'); // 支付

        Route::any('alipay/notify', 'LevelingController@alipayNotify')->name('mobile.leveling.alipay.notify');
        Route::any('alipay/return', 'LevelingController@alipayReturn')->name('mobile.leveling.alipay.return');
        
        Route::any('wechat/notify/{no}', 'LevelingController@wechatNotify')->name('mobile.leveling.wechat.notify');
        Route::any('wechat/return/{no}', 'LevelingController@wechatReturn')->name('mobile.leveling.wechat.return');
        Route::get('show/{id}', 'LevelingController@show')->name('mobile.leveling.show'); // 详情页
    }); 
});

//  渠道订单
Route::namespace('Frontend\Channel')->prefix('channel')->group(function () {
    Route::get('index', 'GameLevelingChannelOrderController@index')->name('channel.index'); // 下单首页
    Route::post('game', 'GameLevelingChannelOrderController@game')->name('channel.game'); // 获取代练游戏
    Route::post('type', 'GameLevelingChannelOrderController@type')->name('channel.type'); // 获取代练类型
    Route::post('target', 'GameLevelingChannelOrderController@target')->name('channel.target'); // 获取代练目标
    Route::post('compute', 'GameLevelingChannelOrderController@compute')->name('channel.compute'); // 计算价格和时间
    Route::post('go', 'GameLevelingChannelOrderController@go')->name('channel.go'); // 成功跳转

    Route::get('place-order', 'GameLevelingChannelOrderController@placeOrder')->name('channel.place-order'); // 下单配置界面
    Route::post('region', 'GameLevelingChannelOrderController@region')->name('channel.region'); // 区
    Route::post('server', 'GameLevelingChannelOrderController@server')->name('channel.server'); // 服
    Route::post('pay', 'GameLevelingChannelOrderController@pay')->name('channel.pay'); // 支付

    Route::any('alipay-notify', 'GameLevelingChannelOrderController@alipayNotify')->name('channel.alipay-notify');
    Route::any('alipay-return', 'GameLevelingChannelOrderController@alipayReturn')->name('channel.alipay-return');

    Route::any('wechat-notify-{no}', 'GameLevelingChannelOrderController@wechatNotify')->name('channel.wechat-notify');
    Route::any('wechat-return-{no}', 'GameLevelingChannelOrderController@wechatReturn')->name('channel.wechat-return');
    Route::get('show-{id}', 'GameLevelingChannelOrderController@show')->name('channel.show'); // 详情页
});

Route::namespace('Frontend\Auth')->group(function () {
	 // 登录
//	Route::get('/login', 'LoginController@showLoginForm')->name('login');
	Route::post('/login', 'LoginController@login');
	Route::post('/logout', 'LoginController@logout')->name('logout');
//	// 注册
//	Route::get('/register', 'RegisterController@showRegistrationForm')->name('register');
	Route::post('/register', 'RegisterController@register');
	// 密码找回
	Route::get('/password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
	Route::post('/password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
	Route::get('/password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
	Route::post('/password/reset', 'ResetPasswordController@reset');
});

