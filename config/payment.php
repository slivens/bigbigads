<?php

return [
    'paypal' => [
        'appid' => env('PAYPAL_APPID'),
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET'),
        'webhook' => env('PAYPAL_WEBHOOK'),
        'returnurl' => env('PAYPAL_RETURNURL'),
        'mode'=> env('PAYPAL_MODE')
    ],
    'stripe' => [
        'publishable_key' => env('STRIPE_PUBLISHABLE_KEY'),
        'secret_key' => env('STRIPE_SECRET_KEY')
    ]
];
