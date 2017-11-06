<?php

namespace App\Http\Middleware;

use Auth;
use Redis;
use Closure;
use App\Services\RedisConnect;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $table = null)
    {
        $user = Auth::user();

        $redis = RedisConnect::session();

        if ($table ==  'users') {

            $sessionId = $redis->get(config('redis.user')['loginSession']);    
        } else {
            $sessionId = $redis->get(config('redis.user')['adminLoginSession']);  
        }

        if ($sessionId) {

            $redis->del($sessionId);

            $redis->del($table . ':' . $user->id);
        }

        if ($table ==  'users') {

            $sessionId = $redis->set(config('redis.user')['loginSession'] . $user->id, session()->getId());    
        } else {
            $sessionId = $redis->set(config('redis.user')['adminLoginSession'] . $user->id, session()->getId());  
        }
        return $next($request);
    }
}
