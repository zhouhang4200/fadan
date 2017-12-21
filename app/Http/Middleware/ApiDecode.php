<?php

namespace App\Http\Middleware;

use Closure;
use Validator;
use Exception;
use App\Extensions\EncryptAndDecrypt\Aes;

// 接口解密
class ApiDecode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        myLog('app-request', ['App请求来了', $request->url(), $_SERVER['QUERY_STRING'],  $request->all()]);

        $validator = Validator::make($request->all(), [
            'api_token' => 'bail|required|min:1|max:60',
            'key'       => 'bail|required',
            'data'      => 'bail|required',
        ]);

        if ($validator->fails()) {
            return response()->jsonReturn(0, '参数不正确');
        }

        try {
            // 解密数据包得到Aes 解密 key
            if (!openssl_private_decrypt(pack("H*", $request->key), $aesKey, config('encryptanddecrypt.app.ase_private_key'))) {
                throw new Exception('key解密失败');
            }

            if (empty($aesKey)) {
                throw new Exception('解密失败');
            }
        } catch(Exception $e){
            return response()->jsonReturn(0, $e->getMessage());
        }

        $request->data = json_decode((new Aes($aesKey))->decrypt($request->data), true);

        myLog('app-request', ['App data解密', $request->url(), $_SERVER['QUERY_STRING'],  $request->data]);

        return $next($request);
    }
}
