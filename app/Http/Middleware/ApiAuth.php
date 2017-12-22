<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Validator;

// 接口解密
class ApiAuth
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
        $validator = Validator::make($request->all(), [
            'api_token' => 'bail|required|min:1|max:60',
        ]);

        if ($validator->fails()) {
            return response()->jsonReturn(0, '参数不正确');
        }

        if (!Auth::guard('api')->validate(['api_token' => $request->api_token])) {
            return response()->jsonReturn(0, 'token不存在');
        }

        if (Auth::guard('api')->user()->api_token_expire < time()) {
            return response()->jsonReturn(0, 'token已过期，请重新登陆');
        }

        return $next($request);
    }
}
