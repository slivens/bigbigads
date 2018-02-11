<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GatewayConfig extends Model
{
    const FACTORY_PAYPAL_EXPRESS_CHECKOUT = 'paypal_express_checkout';
    const FACTORY_PAYPAL_REST = 'paypa_rest';
    protected $casts = [
        'config' => 'json'
    ];
}
