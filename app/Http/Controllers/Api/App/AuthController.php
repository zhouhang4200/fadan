<?php

namespace App\Http\Controllers\Api\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Validator;
use Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (empty($request->params['name'])) {
            return response()->jsonReturn(0, '用户名不能为空');
        }

        if (empty($request->params['password'])) {
            return response()->jsonReturn(0, '密码不能为空');
        }

        $user = User::where('name', $request->params['name'])->first();
        if (empty($user)) {
            return response()->jsonReturn(0, '用户不存在');
        }
        if (!Hash::check($request->params['password'], $user->password)) {
            return response()->jsonReturn(0, '密码错误');
        }

        $user->api_token = str_random(60);
        $user->api_token_expire = config('ios.api_token_expire') + time();
        if (!$user->save()) {
            return response()->jsonReturn(0, '登陆失败');
        }

        return response()->jsonReturn(1, '登陆成功', ['api_token' => $user->api_token]);
    }

    public function logout()
    {
        $user = Auth::guard('api')->user();
        $user->api_token_expire = 0;

        if (!$user->save()) {
            return response()->jsonReturn(0, '登出失败');
        }

        return response()->jsonReturn(1);
    }
}
