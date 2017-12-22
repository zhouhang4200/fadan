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

        $validator = Validator::make($request->all(), ['data' => 'bail|required']);
        if ($validator->fails()) {
            return response()->jsonReturn(0, '参数不正确');
        }

        $request->params = json_decode((new Aes(config('ios.aes_key')))->decrypt($request->data), true);
        if (empty($request->params)) {
            return response()->jsonReturn(0, '数据解密失败');
        }

        myLog('app-request', ['App data解密', $request->url(), $_SERVER['QUERY_STRING'],  $request->params]);

        return $next($request);
    }
}
