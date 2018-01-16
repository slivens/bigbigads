<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        "*", // TODO:调试阶段全放开，后续再关
        "/forward/*",
        '/onPayment',
        '/onPayWebhooks',
        '/register',
        '/mailgun/*'
    ];
}
