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
	// 账号管理
	Route::prefix('login')->group(function () {
		// 账号登录记录
		Route::get('history', 'LoginController@history')->name('login.history');
		// 子账号登录历史
		Route::get('child', 'LoginController@child')->name('login.child');		
	});
	// 账号管理
	Route::resource('users', 'UserController', ['except' => ['show']]);
	// 权限组
	Route::resource('rbacgroups', 'RbacGroupController', ['except' => ['show']]);

	// 财务
    Route::get('asset', 'AssetController@index')->name('frontend.asset');
    Route::get('asset/flow', 'AssetController@flow')->name('frontend.asset.flow');

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
