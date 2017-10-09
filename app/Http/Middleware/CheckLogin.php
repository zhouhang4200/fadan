<?php

namespace App\Http\Middleware;

use Hash;
use Closure;
use Carbon\Carbon;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use App\Models\AdminLoginHistory;
use Illuminate\Support\Facades\Auth;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
       if (Auth::guard('admin')->check()) {
            return redirect('/home');
        }

        if ($request->isMethod('post') && $this->checkLoginError($request)) {
            return redirect('/admin/login')->withInput()->with('loginError', '异地登录!');
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
        $user = AdminUser::where('name', $request->name)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // 30天时间
            $startOfDate = Carbon::now()->subDays(30)->startOfDay()->toDateTimeString();
            $endOfDate = Carbon::now()->endOfDay()->toDateTimeString();
            // 最常登录IP
            $mostLoginIp = AdminLoginHistory::select(\DB::raw('count(ip) as ipCount, ip'))
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
