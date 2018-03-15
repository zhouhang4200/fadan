<?php

namespace App\Http\Controllers\Api;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Class UplayController
 * @package App\Http\Controllers\Api
 */
class UplayController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function accountVerification(Request $request)
    {
        $account = $request->input('account');
        $password = $request->input('password');

        if (!$account && !$password || !strpos($account, '@')) {
            return response()->ajax(0, '请输入正确账号密码');
        }

        try {
            $client = new Client();
            $result = $client->request('POST', 'https://connect.ubi.com/ubiservices/v2/profiles/sessions', [
                'json' => [
                    'rememberMe' => 'true',
                ],
                'headers' => [
                    'Ubi-AppId' => '880650b9-35a5-4480-8f32-6a328eaa3aad',
                    'Content-Type' => 'application/json; charset=UTF-8',
                    'Authorization' => 'Basic ' . base64_encode($account . ':' . $password),
                ]
            ]);
            $code = $result->getStatusCode();
            if ($code == 200) {
                return response()->ajax(1, '账号密码正确');
            }
        } catch (\Exception $exception) {
            return response()->ajax(0, '账号密码错误');
        }
        return response()->ajax(0, '账号密码错误');
    }
}