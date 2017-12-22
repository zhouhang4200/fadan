<?php

namespace App\Http\Controllers\Api\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class TestController extends Controller
{
    public function index(Request $request)
    {
        dump($request->params);

        $user = Auth::guard('api')->user();
        // $user->api_token_expire = 0;
        // $user->save();

        dump($user);
    }
}
