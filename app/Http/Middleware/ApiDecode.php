<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\HttpService;

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


        // 解密数据包得到Aes 解密 key
        $decryptKey = null;
        try {
            $decryptKey = (openssl_private_decrypt(pack("H*", $this->encryptKey), $decrypted, $this->userInfo->private_key)) ? $decrypted : null;
        } catch(\Exception $e){
            return ApiHelper::response(10003);
        }

        if ($decryptKey == null) {
            return ApiHelper::response(10003);
        } else {
            // 解业务数据包
            $this->data = json_decode((new Aes($decryptKey))->decrypt($this->encryptData));
        }






        $requestAll = $request->all();
        myLog('api-request', ['App请求来了', $request->url(), $_SERVER['QUERY_STRING'],  $requestAll]);

        if (isset($requestAll['data']) && !empty($requestAll['data'])) {
            $request->data = taobaoAesDecrypt($requestAll['data']);
            myLog('api-request', ['App data解密', $request->url(), $_SERVER['QUERY_STRING'],  $request->data]);
        } else {
            return response()->jsonReturn(0, '请求格式错误');
        }

        return $next($request);
    }
}
