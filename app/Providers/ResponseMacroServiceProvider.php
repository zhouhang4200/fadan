<?php

namespace App\Providers;

use Response;
use Illuminate\Support\ServiceProvider;
use App\Extensions\EncryptAndDecrypt\Aes;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // 站内前端ajax返回
        Response::macro('ajax', function ($status = 1, $message = 'success', $content = []) {
            return response()->json(['status' => $status, 'message' => $message, 'content' => $content], 200, ["Content-type" => "application/json;charset=utf-8"], JSON_UNESCAPED_UNICODE);
        });

        /*
         * app 接口返回
         */
        Response::macro('jsonReturn', function ($status = 1, $message = 'success', $content = []) {
            $data = ['status' => $status, 'message' => $message, 'content' => $content, 'time' => time()];
            // return response()->json($data);

            // 加密
            $data = ['data' => (new Aes(config('ios.aes_key')))->encrypt(json_encode($data, JSON_UNESCAPED_UNICODE))];

            return response()->json($data);
        });

        // 淘宝接收已授权店铺订单 API 响应
        Response::macro('tb', function ($status = 1, $message = 'success', $content= []) {
            return response()->json(['status' => $status, 'message' => $message, 'content' => $content], 200, ["Content-type" => "application/json;charset=utf-8"], JSON_UNESCAPED_UNICODE);
        });

        // 接口响应
        Response::macro('api', function ($status = 1, $message = 'success', $content= []) {
            $data = ['status' => $status, 'message' => $message, 'content' => $content, 'time' => time()];

            // 加密
            $data = ['data' => (new Aes(config('custom.aes.key'), config('custom.aes.iv')))->encrypt(json_encode($data, JSON_UNESCAPED_UNICODE))];

            return response()->json($data);
        });

        // 接口响应
        Response::macro('partner', function ($code  = 1, $message = '成功', $data= []) {
            $data = ['code' => $code, 'message' => $message, 'data' => $data];
            try {
                myLog('partner-response', [$data, request()]);
            } catch (\Exception $exception) {
                myLog('partner-response', [$exception->getMessage()]);
            }
            return response()->json($data);
        });

    }
}
