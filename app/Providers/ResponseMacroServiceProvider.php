<?php

namespace App\Providers;

use Illuminate\Http\Response;
use Illuminate\Support\ServiceProvider;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('ajax', function ($status, $message = 'success', $content) {
            return response()->json(['status' => $status, 'message' => $message, 'content' => $content], 200, [], JSON_UNESCAPED_UNICODE);
        });
    }
}