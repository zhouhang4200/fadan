<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/channel/order/',
        '/channel/pay/wx/notify/',
        '/v2/account/authentication-upload/',
        '/pay/wechat/notify/',
        '/pay/alipay/notify/',
    ];
}
