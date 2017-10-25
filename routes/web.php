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

	Route::group(['middleware' => ['role:home.qiantaichaojiguanliyuan|home.qiantaimorenjuese']], function () {
		// 我的账号
		Route::resource('home-accounts', 'AccountController', ['only' => ['index', 'update', 'edit']]);
		// 实名认证
		Route::resource('idents', 'IdentController', ['except' => ['destroy', 'show']]);

		Route::post('upload-images', 'IdentController@uploadImages')->name('ident.upload-images');
	});

	Route::group(['middleware' => ['role:home.qiantaichaojiguanliyuan']], function () {
		// 子账号管理
		Route::resource('users', 'UserController', ['except' => ['show']]);
		// 分组管理
		Route::resource('rbacgroups', 'RbacGroupController', ['except' => ['show']]);
		// 子账号分组
		Route::resource('user-groups', 'UserGroupController', ['except' => ['show']]);
		// 系统日志
		Route::resource('home-system-logs', 'SystemLogController', ['only' => ['index']]);
	});

    // 商品
    Route::prefix('goods')->group(function () {
        // 商品列表
        Route::get('/', 'GoodsController@index')->name('frontend.goods.index');
        // 添加视图
        Route::get('create', 'GoodsController@create')->name('frontend.goods.create');
        // 保存商品
        Route::post('store', 'GoodsController@store')->name('frontend.goods.store');
        // 删除商品
        Route::post('destroy', 'GoodsController@destroy')->name('frontend.goods.destroy');
    });

	// 财务
	Route::namespace('Finance')->prefix('finance')->group(function () {
	    Route::get('asset', 'AssetController@index')->name('frontend.finance.asset');

	    Route::get('amount-flow', 'AmountFlowController@index')->name('frontend.finance.amount-flow');
	    Route::get('amount-flow/export', 'AmountFlowController@export')->name('frontend.finance.amount-flow.export');

        Route::get('asset-daily', 'AssetDailyController@index')->name('frontend.finance.asset-daily');

        Route::get('widthdraw-order', 'WithdrawOrderController@index')->name('frontend.finance.widthdraw-order');
	});

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


        // 订单操作
        Route::prefix('order')->group(function (){
            Route::prefix('operation')->group(function (){
                // 接单
                Route::get('receiving/{id}', 'OrderOperationController@receiving')->name('frontend.workbench.order.receiving');
            });
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
