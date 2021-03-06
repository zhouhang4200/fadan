<?php

namespace App\Http\Middleware;

use Hash;
use RedisFacade;
use Closure;
use Carbon\Carbon;
use App\Models\User;
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

        if ($guard == 'web' && $request->isMethod('post') && $this->checkLoginError($request)) {
            return redirect('/login')->withInput()->with('loginError', '异地登录!');
        }

        if (Auth::guard('web')->check()) {
//            if (Auth::user()->could('frontend.workbench.leveling.index')) {
                return redirect('/workbench/leveling');
//            }
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
            // 最常登录城市
            $mostLoginCityId = LoginHistory::select(\DB::raw('count(ip) as ipCount, ip'))
                        ->whereBetween('created_at', [$startOfDate, $endOfDate])
                        ->where('user_id', $user->id)
                        ->groupBy('ip')
                        ->latest('ipCount')
                        ->value('city_id');
            
            $loginDetail = loginDetail($request->ip());
            $loginCity = $loginDetail['city_id'] ?: 0;

            if ($loginCity != $mostLoginCityId) {  

                return true;
            }
        }
        return false;
    }
}
