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
         * 站外调用接口返回
         * 注意：此接口依赖ajax宏
         */
        Response::macro('jsonReturn', function ($status = 1, $message = 'success', $content = []) {
            return response()->ajax($status, $message, $content);

            $content = empty($content) ? null : (new Aes(config('ios.aes_key')))->encrypt(json_encode($content));

            return response()->ajax($status, $message, $content);
        });
    }
}
