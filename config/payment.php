<?php

return [
    'driver' => 'paypal',
    'appid' => env('PAYPAL_APPID'),
    'client_id' => env('PAYPAL_CLIENT_ID'),
    'client_secret' => env('PAYPAL_CLIENT_SECRET'),
    'webhook' => env('PAYPAL_WEBHOOK'),
    'returnurl' => env('PAYPAL_RETURNURL')

];
