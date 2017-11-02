<?php

namespace App\Http\Middleware;

use Auth;
use Redis;
use Closure;

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

        $sessionId = Redis::get($table . ':' . $user->id);

        if ($sessionId) {
            Redis::del($sessionId);
            Redis::del($table . ':' . $user->id);
        }

        Redis::set($table . ':' . $user->id, session()->getId());

        return $next($request);
    }
}
