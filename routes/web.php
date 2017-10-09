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

Route::group(['middleware' => 'auth'], function () {
	Route::get('/home', 'HomeController@index')->name('home');
});

Route::middleware(['auth'])->group(function () {

	Route::prefix('home')->namespace('Frontend')->group(function () {

		Route::resource('account', 'AccountController', ['except' => ['show']]);

		Route::resource('login', 'LoginRecordController', ['only' => ['index']]);
	});

	Route::prefix('admin')->namespace('Backend')->group(function () {

		Route::get('realname/audit', 'RealNameIdent@showAudit')->name('realname.audit');
	});
});

Route::namespace('Frontend\Auth')->group(function () {
	 // 登录
	Route::get('login', 'LoginController@showLoginForm')->name('login');
	Route::post('login', 'LoginController@login');
	Route::post('logout', 'LoginController@logout')->name('logout');

	// 注册
	Route::get('register', 'RegisterController@showRegistrationForm')->name('register');
	Route::post('register', 'RegisterController@register');

	// 密码找回
	Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
	Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
	Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
	Route::post('password/reset', 'ResetPasswordController@reset');
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