<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('kamen', 'OrderController@KamenOrder');
Route::any('test', 'OrderController@test');

/* App 接口 */
Route::namespace('App')->middleware('api.decode')->group(function () {
    // 登陆接口
    Route::post('auth/login', 'AuthController@login');
    Route::post('auth/logout', 'AuthController@logout');

    // 登陆后接口
    Route::middleware('api.auth')->group(function () {
        Route::any('test', 'TestController@index');
    });
});

Route::get('t', function () {
    // $aesKey = str_random(24);
    // dump($aesKey);
    // openssl_public_encrypt($aesKey, $key, config('ios.ase_public_key'));
    // $key = bin2hex($key);
    // dump($key);

    $aesKey = 'woca';
    $data = json_encode(['name' => 'buer2202', 'password' => 'abcdefg']);
    $a = new \App\Extensions\EncryptAndDecrypt\Aes($aesKey);

    $str = $a->encrypt($data);
    dump($str);
    dump($a->decrypt('ca255768f3b50c88d7468b3be507659a'));

    // $b = new \App\Extensions\EncryptAndDecrypt\Aes1($aesKey);
    // $str = $b->encrypt($data);
    // dump($str);
    // dump($b->decrypt($str));
});
