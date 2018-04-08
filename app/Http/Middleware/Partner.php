<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Validator;

/**
 * 代练接单平台验证中间件
 * Class Partner
 * @package App\Http\Middleware
 */
class Partner
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
        // 检测是否有订单号

        // 检测appId
        $request->user = User::where('app_id', $request->app_id)->first();

        if ( ! $request->user) {
            return response()->parnter(0, 'app_id错误');
        }

        // 检测sign
        if ( ! $this->checkSign($request)) {
            dd(1);
        }
        return $next($request);
    }

    /**
     * 检测签名
     * @param $request
     * @return bool
     */
    public function checkSign($request)
    {
        // 获取所有参数 并对参数进行排序
        $par = $request->all();
        sort($par);
        $str = '';
        foreach ($par  as $key => $value) {
            if ($key != 'sign') {
                $str .= $key . '=' . $value . '&';
            }
        }
        $newStr = rtrim('&', $str);
        dd($newStr);
        return false;
    }
}
