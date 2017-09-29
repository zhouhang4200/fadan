<?php

namespace App\Http\Middleware;

use Hash;
use Closure;
use App\User;
use Carbon\Carbon;
use App\Models\LoginHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            return redirect('login');
        }

        if ($this->checkLoginError($request)) {
            return redirect('login')->withInput()->with('loginError', '异地登录!');
        }

        return $next($request);
    }

    /**
     * 异地登录检查
     * @param  Request $request [description]
     * @return bool
     */
    protected function checkLoginError(Request $request)
    {
        $user = User::where('name', $request->name)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // 30天时间
            $startOfDate = Carbon::now()->subDays(30)->startOfDay()->toDateTimeString();
            $endOfDate = Carbon::now()->endOfDay()->toDateTimeString();
            // 最常登录IP
            $mostLoginIp = LoginHistory::select(\DB::raw('count(ip) as ipCount, ip'))
                        ->whereBetween('created_at', [$startOfDate, $endOfDate])
                        ->where('user_id', $user->id)
                        ->groupBy('ip')
                        ->latest('ipCount')
                        ->value('ip');
        
            if (ip2long($request->ip()) != $mostLoginIp) {      
                return true;
            }
        }
        return false;
    }
}
