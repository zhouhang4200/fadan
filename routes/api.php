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

    // 登陆后接口
    Route::middleware('api.auth')->group(function () {
        Route::any('test', 'TestController@index');
    });
});





Route::get('t', function () {
    $aesKey = str_random(24);
    openssl_public_encrypt($aesKey, $key, '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC+QgdARwi/NTr99dGmcxb/Xur5
sxbLzoXcsKIOe4JyfkAtZY/CC0PogWnrHkGA+QvVxtY31W9pOYKaslKCFdNMki64
t/lfMWPIrBoCiEl3cqvfj9WkTatkUt7ePcH+MckHsG4Cq9B6B9PXlRYE3+q0Hh9j
BQd9ukGBXFJxLygnwQIDAQAB
-----END PUBLIC KEY-----');
    $key = bin2hex($key);
    dump($key);

    $data = json_encode(['user' => 2741, 'name' => 'buer']);
    $a = new \App\Extensions\EncryptAndDecrypt\Aes($aesKey);

    $str = $a->encrypt($data);
    dump($str);
    dump($a->decrypt($str));

    $b = new \App\Extensions\EncryptAndDecrypt\Aes1($aesKey);
    $str = $b->encrypt($data);
    dump($str);
    dump($b->decrypt($str));
});
