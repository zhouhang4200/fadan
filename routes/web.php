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

	Route::group(['middleware' => ['role:home.qiantaiguanlizu|home.qiantaitixianzu|home.qiantaijiedanzu|home.qiantaimorenzu']], function () {
		// 实名认证
		// Route::resource('idents', 'IdentController', ['except' => ['destroy', 'show']]);
		Route::get('idents', 'IdentController@index')->name('idents.index');
		Route::get('idents/create', 'IdentController@create')->name('idents.create');
		Route::post('idents', 'IdentController@store')->name('idents.store');
		Route::get('idents/{id}/edit', 'IdentController@edit')->name('idents.edit');
		Route::put('idents/{id}', 'IdentController@update')->name('idents.update');
		Route::post('upload-images', 'IdentController@uploadImages')->name('ident.upload-images');
	});

	Route::group(['middleware' => ['role:home.qiantaiguanlizu|home.qiantaitixianzu|home.qiantaijiedanzu']], function () {
		// 子账号管理
		// Route::resource('users', 'UserController', ['except' => ['show']]);
		Route::get('users', 'UserController@index')->name('users.index');
		Route::get('users/create', 'UserController@create')->name('users.create');
		Route::post('users', 'UserController@store')->name('users.store');
		Route::get('users/{id}/edit', 'UserController@edit')->name('users.edit');
		Route::put('users/{id}', 'UserController@update')->name('users.update');
		Route::delete('users/{id}', 'UserController@destroy')->name('users.destroy');
		// 分组管理
		// Route::resource('rbacgroups', 'RbacGroupController', ['except' => ['show']]);
		Route::get('rbacgroups', 'RbacGroupController@index')->name('rbacgroups.index');
		Route::get('rbacgroups/create', 'RbacGroupController@create')->name('rbacgroups.create');
		Route::post('rbacgroups', 'RbacGroupController@store')->name('rbacgroups.store');
		Route::get('rbacgroups/{id}/edit', 'RbacGroupController@edit')->name('rbacgroups.edit');
		Route::put('rbacgroups/{id}', 'RbacGroupController@update')->name('rbacgroups.update');
		Route::delete('rbacgroups/{id}', 'RbacGroupController@destroy')->name('rbacgroups.destroy');
		// 子账号分组
		// Route::resource('user-groups', 'UserGroupController', ['except' => ['show']]);
		Route::get('user-groups', 'UserGroupController@index')->name('user-groups.index');
		Route::get('user-groups/create', 'UserGroupController@create')->name('user-groups.create');
		Route::post('user-groups', 'UserGroupController@store')->name('user-groups.store');
		Route::get('user-groups/{id}/edit', 'UserGroupController@edit')->name('user-groups.edit');
		Route::put('user-groups/{id}', 'UserGroupController@update')->name('user-groups.update');
		Route::delete('user-groups/{id}', 'UserGroupController@destroy')->name('user-groups.destroy');
		// 系统日志
		// Route::resource('home-system-logs', 'SystemLogController', ['only' => ['index']]);
		Route::get('home-system-logs', 'SystemLogController@index')->name('home-system-logs.index');
	});

	Route::group(['middleware' => ['role:home.qiantaiguanlizu|home.qiantaitixianzu|home.qiantaijiedanzu']], function () {
	    // 商品
	    Route::prefix('goods')->group(function () {
	        // 商品列表
	        Route::get('/', 'GoodsController@index')->name('frontend.goods.index');
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
	});

	Route::group(['middleware' => ['role:home.qiantaiguanlizu|home.qiantaijiedanzu|home.qiantaitixianzu']], function () {
		// 财务
		Route::namespace('Finance')->prefix('finance')->group(function () {
			// 我的资产
		    Route::get('asset', 'AssetController@index')->name('frontend.finance.asset');

		    // 资金流水
		    Route::get('amount-flow', 'AmountFlowController@index')->name('frontend.finance.amount-flow');
		    // 资金流水导出
		    Route::get('amount-flow/export', 'AmountFlowController@export')->name('frontend.finance.amount-flow.export');

		    // 资产日报
	        Route::get('asset-daily', 'AssetDailyController@index')->name('frontend.finance.asset-daily');

	        // 我的提现
	        Route::post('withdraw-order/store', 'WithdrawOrderController@store')->name('frontend.finance.withdraw-order.store');
		});
	});

	Route::group(['middleware' => ['role:home.qiantaiguanlizu|home.qiantaitixianzu']], function () {
		// 财务提现
		Route::namespace('Finance')->prefix('finance')->group(function () {
	        Route::get('withdraw-order', 'WithdrawOrderController@index')->name('frontend.finance.withdraw-order');
	    });
	});

	//Route::group(['middleware' => ['role:home.qiantaiguanlizu|home.qiantaitixianzu|home.qiantaijiedanzu']], function () {
		// 工作台
		Route::namespace('Workbench')->prefix('workbench')->group(function () {
	        // 首页
	        Route::get('/', 'OrderController@index')->name('frontend.workbench.index');
	        // 获取用户所有前台可显示的商品
	        Route::post('goods', 'OrderController@goods')->name('frontend.workbench.goods');
	        // 商品模版
	        Route::post('template', 'OrderController@template')->name('frontend.workbench.template');
	        // 获取子级的值
	        Route::post('child', 'OrderController@widgetChild')->name('frontend.workbench.widget.child');
	        // 下单
	        Route::post('order', 'OrderController@order')->name('frontend.workbench.order');
	        // 订单列表
	        Route::post('order-list', 'OrderController@orderList')->name('frontend.workbench.order-list');


	        // 订单操作
	        Route::prefix('order-operation')->group(function (){
	            // 订单详情
	            Route::get('detail', 'OrderOperationController@detail')->name('frontend.workbench.order-operation.detail');
	            // 订单发货
	            Route::post('delivery', 'OrderOperationController@delivery')->name('frontend.workbench.order-operation.delivery');
	            // 失败订单
	            Route::post('fail', 'OrderOperationController@fail')->name('frontend.workbench.order-operation.fail');
	            // 取消订单
	            Route::post('cancel', 'OrderOperationController@cancel')->name('frontend.workbench.order-operation.cancel');
	            // 确认收货
	            Route::post('confirm', 'OrderOperationController@confirm')->name('frontend.workbench.order-operation.confirm');
	            // 返回集市
                Route::post('turn-back', 'OrderOperationController@turnBack')->name('frontend.workbench.order-operation.turnBack');
                // 申请售后
                Route::post('after-sales', 'OrderOperationController@afterSales')->name('frontend.workbench.order-operation.after-sales');
	        });
		});
	//});

	Route::group(['middleware' => ['role:home.qiantaiguanlizu|home.qianshoujiedanzu']], function () {
		// 工作台接单
		Route::namespace('Workbench')->prefix('workbench/order-operation')->group(function () {
			// 接单
	        Route::post('receiving', 'OrderOperationController@receiving')->name('frontend.workbench.order-operation.receiving');
		});
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
