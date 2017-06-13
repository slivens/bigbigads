<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    //
    public function subscriptions()
    {
        return $this->hasMany('\App\Subscription');
    }
}
