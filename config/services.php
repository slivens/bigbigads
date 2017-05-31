<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
	'braintree' => [
		'model'  => App\User::class,
		'environment' => env('BRAINTREE_ENV'),
		'merchant_id' => env('BRAINTREE_MERCHANT_ID'),
		'public_key' => env('BRAINTREE_PUBLIC_KEY'),
		'private_key' => env('BRAINTREE_PRIVATE_KEY'),
    ],
    'bigbigads' => [
        'ad_search_url' => env('AD_SEARCH_URL', 'http://121.41.107.126:8080/search'),
        'adser_search_url' => env('ADSER_SEARCH_URL', 'http://121.41.107.126:8080/adser_search'),
        'adser_analysis_url' => env('ADSER_ANALYSIS_URL', 'http://xgrit.xicp.net:5000/adser_analysis'),
        'trends_url' => env('TRENDS_URL',  'http://xgrit.xicp.net:5000/adsid_trend')
    ],
    'github' => [
        'client_id' => env('GITHUB_KEY'),
        'client_secret' => env('GITHUB_SECRET'),
        'redirect' => env('GITHUB_REDIRECT_URI')
    ],
    'linkedin' => [
        'client_id' => env('LINKEDIN_KEY'),
        'client_secret' => env('LINKEDIN_SECRET'),
        'redirect' => env('LINKEDIN_REDIRECT_URI')
    ],
    'google' => [
        'client_id' => env('GOOGLE_KEY'),
        'client_secret' => env('GOOGLE_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI')
    ],
    'facebook' => [
        'client_id' => env('FACEBOOK_KEY'),
        'client_secret' => env('FACEBOOK_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI')
    ],
];
