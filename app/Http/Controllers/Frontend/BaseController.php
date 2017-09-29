<?php

namespace App\Http\Controllers\Frontend;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    // 登录的 userId
	protected $userId;

    public function __construct()
    {
    	$this->middleware(function ($request, $next) {
            $this->userId = Auth::user()->pid ?: Auth::user()->id;
            return $next($request);
        }); 
    }
}
