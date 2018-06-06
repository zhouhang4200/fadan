<?php

namespace App\Http\Middleware;

use Closure;

// 淘宝接口中间件
class TaobaoApi
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
        $requestAll = $request->all();

        if (isset($requestAll['data']) && !empty($requestAll['data'])) {
            $request->data = taobaoAesDecrypt($requestAll['data']);
            myLog('api-request', [
                '淘宝data解密',
                $request->url(),
                $_SERVER['QUERY_STRING'],
                $request->data['Tid'],
//                $request->data['NumIid'],
//                $request->data['BuyerNick'],
//                $request->data['SellerNick'],
//                $request->data
            ]);
        } else {
            myLog('api-request-err', [file_get_contents("php://input")]);
            return response()->tb(0, '请求格式错误');
        }
        return $next($request);
    }
}
