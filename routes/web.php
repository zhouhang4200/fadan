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
	Route::resource('home-accounts', 'AccountController', ['only' => ['index', 'update', 'edit']]);

	Route::group(['middleware' => ['role:home.qiantaiguanlizu|home.qiantaitixianzu|home.qiantaijiedanzu|home.qiantaimorenzu']], function () {
		// 实名认证
		Route::resource('idents', 'IdentController', ['except' => ['destroy', 'show']]);
		Route::post('upload-images', 'IdentController@uploadImages')->name('ident.upload-images');
	});

	Route::group(['middleware' => ['role:home.qiantaiguanlizu|home.qiantaitixianzu|home.qiantaijiedanzu']], function () {
		// 子账号管理
		Route::resource('users', 'UserController', ['except' => ['show']]);
		// 分组管理
		Route::resource('rbacgroups', 'RbacGroupController', ['except' => ['show']]);
		// 子账号分组
		Route::resource('user-groups', 'UserGroupController', ['except' => ['show']]);
		// 系统日志
		Route::resource('home-system-logs', 'SystemLogController', ['only' => ['index']]);
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
		    Route::get('asset', 'AssetController@index')->name('frontend.finance.asset');

		    Route::get('amount-flow', 'AmountFlowController@index')->name('frontend.finance.amount-flow');
		    Route::get('amount-flow/export', 'AmountFlowController@export')->name('frontend.finance.amount-flow.export');

	        Route::get('asset-daily', 'AssetDailyController@index')->name('frontend.finance.asset-daily');

	        Route::post('withdraw-order/store', 'WithdrawOrderController@store')->name('frontend.finance.withdraw-order.store');
		});
	});

	Route::group(['middleware' => ['role:home.qiantaiguanlizu|home.qiantaitixianzu']], function () {
		// 财务提现
		Route::namespace('Finance')->prefix('finance')->group(function () {
	        Route::get('withdraw-order', 'WithdrawOrderController@index')->name('frontend.finance.withdraw-order');
	    });
	});

	Route::group(['middleware' => ['role:home.qiantaiguanlizu|home.qiantaitixianzu|home.qiantaijiedanzu']], function () {
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
	        });
		});
	});

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
