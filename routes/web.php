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

Auth::routes();

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