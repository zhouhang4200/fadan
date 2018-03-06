<?php

namespace App\Http\Middleware;

use Closure;

/**
 * 内部api ip 验证
 * Class InternalApi
 * @package App\Http\Middleware
 */
class InternalApi
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
        if (!in_array(getClientIp(), config('internalip'))) {
            return response()->ajax(0, 'IP不在白名单');
        }
        return $next($request);
    }
}
