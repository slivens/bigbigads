<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GatewayConfig extends Model
{
    const FACTORY_PAYPAL_EXPRESS_CHECKOUT = 'paypal_express_checkout';
    protected $casts = [
        'config' => 'json'
    ];
}
