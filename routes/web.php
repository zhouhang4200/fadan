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

 // Authentication Routes...
Route::get('login', 'Frontend\Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Frontend\Auth\LoginController@login');
Route::post('logout', 'Frontend\Auth\LoginController@logout')->name('logout');

// Registration Routes...
Route::get('register', 'Frontend\Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Frontend\Auth\RegisterController@register');

// Password Reset Routes...
Route::get('password/reset', 'Frontend\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Frontend\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Frontend\Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Frontend\Auth\ResetPasswordController@reset');

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