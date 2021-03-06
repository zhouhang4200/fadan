<?php

namespace App\Http\Middleware;


use App\Models\User;
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
        myLog('request.in', [$request->url(), $request->all()]);
        // 判断请求是否为重复使用
//        if (time() - $request->timestamp > 20) {
//            return response()->partner(0, '无效请求');
//        }

        // 检测appId
        $request->user = User::where('app_id', $request->app_id)->first();
        if (! isset($request->app_id) || ! $request->user) {
            return response()->partner(0, 'app_id错误');
        }

        // 检测sign
        if ( ! $this->checkSign($request)) {
            return response()->partner(0, '签名错误');
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
        ksort($par);
        $str = '';
        foreach ($par  as $key => $value) {
            if ($key != 'sign' && $value != '') {
                $str .= $key . '=' . $value . '&';
            }
        }

        $sign = md5(rtrim($str,  '&') . $request->user->app_secret);

        if ($sign != $request->sign) {
            $str2 = '';
            foreach ($par  as $key => $value) {
                if ($key != 'sign') {
                    $str2 .= $key . '=' . $value . '&';
                }
            }
            $sign2 = md5(rtrim($str2,  '&') . $request->user->app_secret);
            if ($sign2 != $request->sign) {
                return false;
            }
        }
        return true;
    }
}
