<?php

namespace App\Http\Controllers\Api\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class TestController extends Controller
{
    public function index(Request $request)
    {
        // dump($request->params);

        // $user = Auth::guard('api')->user();
        // $user->api_token_expire = 0;
        // $user->save();

        // dump($user);

        // $aesKey = str_random(24);
        // dump($aesKey);
        // openssl_public_encrypt($aesKey, $key, config('ios.ase_public_key'));
        // $key = bin2hex($key);
        // dump($key);

        $aesKey = config('ios.aes_key');
        $data = json_encode(['no' => '2017121917434600000003', 'remark' => '不要了']);
        $a = new \App\Extensions\EncryptAndDecrypt\Aes($aesKey);

        $str = $a->encrypt($data);
        dump($str);
        dump($a->decrypt($str));

        // $b = new \App\Extensions\EncryptAndDecrypt\Aes1($aesKey);
        // $str = $b->encrypt($data);
        // dump($str);
        // dump($b->decrypt($str));
    }
}
