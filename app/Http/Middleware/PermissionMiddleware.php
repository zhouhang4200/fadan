<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class PermissionMiddleware
{
    public function handle($request, Closure $next, $permission)
    {

        if (auth('admin')->guest()) {
            abort(403);
        }

        $permissions = is_array($permission)
            ? $permission
            : explode('|', $permission);

        foreach ($permissions as $permission) {

            if (auth('admin')->user()->can($permission)) {

                return $next($request);
            }
        }

        if (Request::ajax()) {
        
            return response()->ajax(0, '您未开通相应权限！');
        }

        abort(403);
    }
}
