<?php

namespace App\Providers;

use Response;
use Illuminate\Support\ServiceProvider;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('ajax', function ($status = 1, $message = 'success', $content= []) {
            return response()->json(['status' => $status, 'message' => $message, 'content' => $content], 200, ["Content-type" => "application/json;charset=utf-8"], JSON_UNESCAPED_UNICODE);
        });
    }
}