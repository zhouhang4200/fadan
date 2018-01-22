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

Route::middleware(['auth:web'])->namespace('Frontend')->group(function () {
	// 首页
	Route::get('/', 'HomeController@index')->name('frontend.index');
	// 登录管理
	Route::prefix('login')->group(function () {
		// 账号登录记录
		Route::get('history', 'LoginController@history')->name('login.history');
	});
	// 我的账号
	// Route::resource('home-accounts', 'AccountController', ['only' => ['index', 'update', 'edit']]);
	Route::get('home-accounts', 'AccountController@index')->name('home-accounts.index');
	Route::get('home-accounts/{id}/edit', 'AccountController@edit')->name('home-accounts.edit');
	Route::put('home-accounts/{id}', 'AccountController@update')->name('home-accounts.update');
    // 修改资料，上传头像
    Route::prefix('users')->namespace('User')->group(function () {
        Route::get('persional', 'UserController@persional')->name('users.persional');
        Route::post('persional', 'UserController@updatePersional')->name('users.update-persional')->middleware('permission:users.update-persional');
        Route::post('voucher', 'UserController@updateVoucher')->name('users.update-voucher');
        Route::post('upload-images', 'UserController@uploadImages')->name('users.upload-images');
    });

	// 实名认证
	// Route::resource('idents', 'IdentController', ['except' => ['destroy', 'show']]);
	Route::get('idents', 'IdentController@index')->name('idents.index')->middleware('permission:idents.index');
	Route::get('idents/create', 'IdentController@create')->name('idents.create')->middleware('permission:idents.create');
	Route::post('idents', 'IdentController@store')->name('idents.store')->middleware('permission:idents.store');
	Route::get('idents/{id}/edit', 'IdentController@edit')->name('idents.edit')->middleware('permission:idents.edit');
	Route::put('idents/{id}', 'IdentController@update')->name('idents.update')->middleware('permission:idents.update');
	Route::post('upload-images', 'IdentController@uploadImages')->name('ident.upload-images')->middleware('permission:ident.upload-images');

	// 子账号管理
	// Route::resource('users', 'UserController', ['except' => ['show']]);
	Route::get('users', 'UserController@index')->name('users.index')->middleware('permission:users.index');
	Route::get('users/create', 'UserController@create')->name('users.create')->middleware('permission:users.create');
	Route::post('users', 'UserController@store')->name('users.store')->middleware('permission:users.store');
	Route::get('users/{id}/edit', 'UserController@edit')->name('users.edit')->middleware('permission:users.edit');
	Route::put('users/{id}', 'UserController@update')->name('users.update')->middleware('permission:users.update');
	Route::delete('users/{id}', 'UserController@destroy')->name('users.destroy')->middleware('permission:users.destroy');
	// 分组管理
	// Route::resource('rbacgroups', 'RbacGroupController', ['except' => ['show']]);
	Route::get('rbacgroups', 'RbacGroupController@index')->name('rbacgroups.index')->middleware('permission:rbacgroups.index');
	Route::get('rbacgroups/create', 'RbacGroupController@create')->name('rbacgroups.create')->middleware('permission:rbacgroups.create');
	Route::post('rbacgroups', 'RbacGroupController@store')->name('rbacgroups.store')->middleware('permission:rbacgroups.store');
	Route::get('rbacgroups/{id}/edit', 'RbacGroupController@edit')->name('rbacgroups.edit')->middleware('permission:rbacgroups.edit');
	Route::put('rbacgroups/{id}', 'RbacGroupController@update')->name('rbacgroups.update')->middleware('permission:rbacgroups.update');
	Route::delete('rbacgroups/{id}', 'RbacGroupController@destroy')->name('rbacgroups.destroy')->middleware('permission:rbacgroups.destroy');
	// 子账号分组
	// Route::resource('user-groups', 'UserGroupController', ['except' => ['show']]);
	Route::get('user-groups', 'UserGroupController@index')->name('user-groups.index')->middleware('permission:user-groups.index');
	Route::get('user-groups/create', 'UserGroupController@create')->name('user-groups.create')->middleware('permission:user-groups.create');
	Route::post('user-groups', 'UserGroupController@store')->name('user-groups.store')->middleware('permission:user-groups.store');
	Route::get('user-groups/{id}/edit', 'UserGroupController@edit')->name('user-groups.edit')->middleware('permission:user-groups.edit');
	Route::put('user-groups/{id}', 'UserGroupController@update')->name('user-groups.update')->middleware('permission:user-groups.update');
	Route::delete('user-groups/{id}', 'UserGroupController@destroy')->name('user-groups.destroy')->middleware('permission:user-groups.destroy');
	// 系统日志
	// Route::resource('home-system-logs', 'SystemLogController', ['only' => ['index']]);
	Route::get('home-system-logs', 'SystemLogController@index')->name('home-system-logs.index')->middleware('permission:home-system-logs.index');

	// 违规管理
	Route::prefix('punish')->namespace('Punish')->group(function () {
		Route::get('home-punishes', 'PunishController@index')->name('home-punishes.index')->middleware('permission:home-punishes.index');
		Route::post('home-punishes/payment', 'PunishController@payment')->name('home-punishes.payment'); // 付款
		Route::post('home-punishes/complain', 'PunishController@complain')->name('home-punishes.complain'); //申诉
	});

    // 代练员工管理
    Route::prefix('staff-management')->namespace('Account')->group(function () {
        Route::get('index', 'StaffManagementController@index')->name('staff-management.index'); // 员工列表
        Route::post('forbidden', 'StaffManagementController@forbidden')->name('staff-management.forbidden'); // 子账号禁用
        Route::get('edit/{id}', 'StaffManagementController@edit')->name('staff-management.edit')->where('id', '[0-9]+'); // 员工编辑
        Route::put('update/{id}', 'StaffManagementController@update')->name('staff-management.update')->where('id', '[0-9]+'); // 提交员工编辑
        Route::delete('delete', 'StaffManagementController@delete')->name('staff-management.delete'); // 删除员工
        Route::get('create', 'StaffManagementController@create')->name('staff-management.create');
        Route::post('store', 'StaffManagementController@store')->name('staff-management.store');
    });

    // 员工数据统计
    Route::prefix('statistic')->namespace('Statistic')->group(function () {
        Route::get('employee', 'StatisticController@employee')->name('frontend.statistic.employee')->middleware('permission:frontend.statistic.employee');
        Route::get('order', 'StatisticController@order')->name('frontend.statistic.order')->middleware('permission:frontend.statistic.order');
        Route::get('price', 'StatisticController@price')->name('frontend.statistic.price')->middleware('permission:frontend.statistic.price');
        Route::get('message', 'StatisticController@message')->name('frontend.statistic.message')->middleware('permission:frontend.statistic.message');
    });

    // 商品
    Route::prefix('goods')->namespace('Goods')->group(function () {
        // 商品列表
        Route::get('/', 'GoodsController@index')->name('frontend.goods.index')->middleware('permission:frontend.goods.index');
        // 添加视图
        Route::get('create', 'GoodsController@create')->name('frontend.goods.create')->middleware('permission:frontend.goods.create');
        // 保存商品
        Route::post('store', 'GoodsController@store')->name('frontend.goods.store')->middleware('permission:frontend.goods.store');
        // 编辑视图
        Route::get('edit/{id}', 'GoodsController@edit')->name('frontend.goods.edit')->middleware('permission:frontend.goods.edit');
        // 修改商品
        Route::post('update', 'GoodsController@update')->name('frontend.goods.update')->middleware('permission:frontend.goods.update');
        // 删除商品
        Route::post('destroy', 'GoodsController@destroy')->name('frontend.goods.destroy')->middleware('permission:frontend.goods.destroy');

    });

    // 订单
    Route::prefix('order')->namespace('Order')->group(function () {
        // 接单列表
        Route::get('receive', 'OrderController@receive')->name('frontend.order.receive')->middleware('permission:frontend.order.receive');
        // 发单列表
        Route::get('send', 'OrderController@send')->name('frontend.order.send')->middleware('permission:frontend.order.send');
    });

    // 用户设置
    Route::namespace('Setting')->prefix('setting')->group(function () {
        // 接单权限
        Route::prefix('receiving-control')->group(function () {
            Route::get('/', 'ReceivingControlController@index')->name('frontend.setting.receiving-control.index')->middleware('permission:frontend.setting.receiving-control.index');
            Route::get('get-control-user', 'ReceivingControlController@getControlUser')->name('frontend.setting.receiving-control.get-control-user')->middleware('permission:frontend.setting.receiving-control.get-control-user');
            Route::get('get-control-category', 'ReceivingControlController@getControlCategory')->name('frontend.setting.receiving-control.get-control-category')->middleware('permission:frontend.setting.receiving-control.get-control-category');
            Route::post('add-user', 'ReceivingControlController@addUser')->name('frontend.setting.receiving-control.add-user')->middleware('permission:frontend.setting.receiving-control.add-user');
            Route::post('add-category', 'ReceivingControlController@addCategory')->name('frontend.setting.receiving-control.add-category')->middleware('permission:frontend.setting.receiving-control.add-category');
            Route::post('delete-control-user', 'ReceivingControlController@deleteControlUser')->name('frontend.setting.receiving-control.delete-control-user')->middleware('permission:frontend.setting.receiving-control.delete-control-user');
            Route::post('delete-control-category', 'ReceivingControlController@deleteControlCategory')->name('frontend.setting.receiving-control.delete-control-category')->middleware('permission:frontend.setting.receiving-control.delete-control-category');
            Route::post('control-mode', 'ReceivingControlController@controlMode')->name('frontend.setting.receiving-control.control-mode')->middleware('permission:frontend.setting.receiving-control.control-mode');
        });
        // api 风控设置
        Route::prefix('api-risk-management')->group(function () {
            Route::get('/', 'ApiRiskManagementController@index')->name('frontend.setting.api-risk-management.index')->middleware('permission:frontend.setting.api-risk-management.index');
            Route::post('set', 'ApiRiskManagementController@set')->name('frontend.setting.api-risk-management.set')->middleware('permission:frontend.setting.api-risk-management.set');
        });
        // 皮肤交易设置
        Route::prefix('skin')->group(function () {
            Route::get('/', 'SkinController@index')->name('frontend.setting.skin.index')->middleware('permission:frontend.setting.skin.index');
            Route::post('set', 'SkinController@set')->name('frontend.setting.skin.set')->middleware('permission:frontend.setting.skin.set');
        });
        // 店铺抓取订单授权
        Route::prefix('tb-auth')->group(function () {
            Route::get('/', 'TbAuthController@index')->name('frontend.setting.tb-auth.index')->middleware('permission:frontend.setting.tb-auth.index');
            Route::get('store', 'TbAuthController@store')->name('frontend.setting.tb-auth.store')->middleware('permission:frontend.setting.tb-auth.store');
        });

    });

	// 财务
	Route::namespace('Finance')->prefix('finance')->group(function () {
		// 我的资产
	    Route::get('asset', 'AssetController@index')->name('frontend.finance.asset')->middleware('permission:frontend.finance.asset');

	    // 资金流水
	    Route::get('amount-flow', 'AmountFlowController@index')->name('frontend.finance.amount-flow')->middleware('permission:frontend.finance.amount-flow');
	    // 资金流水导出
	    Route::get('amount-flow/export', 'AmountFlowController@export')->name('frontend.finance.amount-flow.export')->middleware('permission:frontend.finance.amount-flow.export');

	    // 资产日报
        Route::get('asset-daily', 'AssetDailyController@index')->name('frontend.finance.asset-daily')->middleware('permission:frontend.finance.asset-daily');

        // 我的提现
        Route::post('withdraw-order/store', 'WithdrawOrderController@store')->name('frontend.finance.withdraw-order.store')->middleware('permission:frontend.finance.withdraw-order.store');
		// 财务提现
        Route::get('withdraw-order', 'WithdrawOrderController@index')->name('frontend.finance.withdraw-order')->middleware('permission:frontend.finance.withdraw-order');
	});

	// 工作台
	Route::namespace('Workbench')->prefix('workbench')->group(function () {

        // 代充
        Route::namespace('Recharge')->prefix('recharge')->group(function (){
            // 首页
            Route::get('/', 'IndexController@index')->name('frontend.workbench.recharge.index')->middleware('permission:frontend.workbench.recharge.index');
        });

        // 代练
        Route::namespace('Leveling')->prefix('leveling')->group(function (){
            // 获取下单项的子菜单
            Route::post('get-select-child', 'IndexController@getSelectChild')->name('frontend.workbench.get-select-child');
            // 首页
            Route::get('/', 'IndexController@index')->name('frontend.workbench.leveling.index')->middleware('permission:frontend.workbench.leveling.index');
            // 根据订单状态获取订单数据
            Route::any('order-list', 'IndexController@orderList')->name('frontend.workbench.leveling.order-list');
            // 创建订单
            Route::get('create', 'IndexController@create')->name('frontend.workbench.leveling.create');
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
            Route::get('leave-image/{order_no}', 'IndexController@leaveImage')->name('frontend.workbench.leveling.leave-image');
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
            // 订单操作记录
            Route::post('operation-record', 'IndexController@operationRecord')->name('frontend.workbench.leveling.operation-record');
            // 待接单列表
            Route::get('wait', 'IndexController@wait')->name('frontend.workbench.leveling.wait');
            // 待接单数据
            Route::post('wait-list', 'IndexController@waitList')->name('frontend.workbench.leveling.wait-list');
            // 重发
            Route::get('repeat/{id?}', 'IndexController@repeat')->name('frontend.workbench.leveling.repeat');
            // 发送短信
            Route::post('send-sms', 'IndexController@sendSms')->name('frontend.workbench.leveling.send-sms');
        });

        // 首页
        Route::get('/', 'IndexController@index')->name('frontend.workbench.index')->middleware('permission:frontend.workbench.index');
        // 获取用户所有前台可显示的商品
        Route::post('goods', 'IndexController@goods')->name('frontend.workbench.goods')->middleware('permission:frontend.workbench.goods');
        // 商品模版
        Route::post('template', 'IndexController@template')->name('frontend.workbench.template')->middleware('permission:frontend.workbench.template');
        // 获取子级的值
        Route::post('child', 'IndexController@widgetChild')->name('frontend.workbench.widget.child')->middleware('permission:frontend.workbench.widget.child');
        // 下单
        Route::post('order', 'IndexController@order')->name('frontend.workbench.order')->middleware('permission:frontend.workbench.order');
        // 订单列表
        Route::group(['middleware'=>'throttle:30'],function(){
            Route::post('order-list', 'IndexController@orderList')->name('frontend.workbench.order-list')->middleware('permission:frontend.workbench.order-list');
        });
        // 清空急需处理数量角标
        Route::post('clear-wait-handle-quantity', 'IndexController@waitHandleQuantityClear')->name('frontend.workbench.clear-wait-handle-quantity');
        // 修改当前账号状态
        Route::post('set-status', 'IndexController@setStatus')->name('frontend.workbench.set-status');

        // 订单操作
        Route::prefix('order-operation')->group(function (){
            // 订单详情
            Route::get('detail', 'OrderOperationController@detail')->name('frontend.workbench.order-operation.detail')->middleware('permission:frontend.workbench.order-operation.detail');
            // 订单发货
            Route::post('delivery', 'OrderOperationController@delivery')->name('frontend.workbench.order-operation.delivery')->middleware('permission:frontend.workbench.order-operation.delivery');
            // 失败订单
            Route::post('fail', 'OrderOperationController@fail')->name('frontend.workbench.order-operation.fail')->middleware('permission:frontend.workbench.order-operation.fail');
            // 取消订单
            Route::post('cancel', 'OrderOperationController@cancel')->name('frontend.workbench.order-operation.cancel')->middleware('permission:frontend.workbench.order-operation.cancel');
            // 确认收货
            Route::post('confirm', 'OrderOperationController@confirm')->name('frontend.workbench.order-operation.confirm')->middleware('permission:frontend.workbench.order-operation.confirm');
            // 返回集市
            Route::post('turn-back', 'OrderOperationController@turnBack')->name('frontend.workbench.order-operation.turnBack')->middleware('permission:frontend.workbench.order-operation.turnBack');
            // 申请售后
            Route::post('after-sales', 'OrderOperationController@afterSales')->name('frontend.workbench.order-operation.after-sales')->middleware('permission:frontend.workbench.order-operation.after-sales');
			// 接单
            Route::group(['middleware'=>'throttle:30'],function(){
                Route::post('receiving', 'OrderOperationController@receiving')->name('frontend.workbench.order-operation.receiving')->middleware('permission:frontend.workbench.order-operation.receiving');
            });
        	// 支付
            Route::post('payment', 'OrderOperationController@payment')->name('frontend.workbench.order-operation.payment')->middleware('permission:frontend.workbench.order-operation.payment');
           });
	});

	Route::namespace('Data')->prefix('data')->group(function () {
		// 日常数据
		Route::get('index', 'DataController@index')->name('data.index');
		// Route::get('index', 'DataController@index')->name('data.index')->middleware('permission:data.index');
	});
});

Route::namespace('Frontend\Auth')->group(function () {
	 // 登录
	Route::get('/login', 'LoginController@showLoginForm')->name('login');
	Route::post('/login', 'LoginController@login');
	Route::post('/logout', 'LoginController@logout')->name('logout');
	// 注册
	Route::get('/register', 'RegisterController@showRegistrationForm')->name('register');
	Route::post('/register', 'RegisterController@register');
	// 密码找回
	Route::get('/password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
	Route::post('/password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
	Route::get('/password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
	Route::post('/password/reset', 'ResetPasswordController@reset');
});

Route::get('test', 'TestController@index');
