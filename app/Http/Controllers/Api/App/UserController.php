<?php

namespace App\Http\Controllers\Api\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class UserController extends Controller
{
    public function index()
    {
        return response()->jsonReturn(1, 'success', ['user_info' => Auth::guard('api')->user()]);
    }
}
