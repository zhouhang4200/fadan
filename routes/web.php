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

Route::middleware(['auth:admin'])->namespace('Backend')->group(function () {

	Route::prefix('/backend')->group(function () {
		// 后台首页
		Route::get('/index', 'AdminController@index')->name('backend.index');
		// 登录历史记录
		Route::get('/login/record', 'LoginRecordController@index')->name('backend.loginrecord.index');
		// 实名认证页
		Route::get('/realname/audit', 'RealNameIdent@showAudit')->name('realnameident.showaudit');
		// 权限管理
		Route::resource('/roles', 'RoleController');
		Route::resource('/rbacgroups', 'RbacGroupController');
		Route::resource('/permissions', 'PermissionController');
	});
});


Route::middleware(['auth'])->namespace('Frontend')->group(function () {
	// 首页
	Route::get('/', 'HomeController@index')->name('frontend.index');
	// 登录历史记录
	Route::get('/login/record', 'LoginRecordController@index')->name('loginrecord.index');
	// 账号管理
	Route::resource('accounts', 'AccountController');
	// 权限组
	Route::resource('rbacgroups', 'RbacGroupController', ['except' => ['show']]);

	Route::get('test', 'TestController@index');
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

Route::prefix('admin')->namespace('Backend\Auth')->group(function () {
	 // 登录
	Route::get('login', 'LoginController@showLoginForm')->name('admin.login');
	Route::post('login', 'LoginController@login');
	Route::post('logout', 'LoginController@logout')->name('admin.logout');
	// 注册
	Route::get('register', 'RegisterController@showRegistrationForm')->name('admin.register');
	Route::post('register', 'RegisterController@register');
	// 密码找回
	Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('admin.password.request');
	Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('admin.password.email');
	Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('admin.password.reset');
	Route::post('password/reset', 'ResetPasswordController@reset');
});

Route::get('test', 'TestController@index');
