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

    /**
     * 动态计算实际折扣价格
     * @param float $amount
     * @return int 实际折扣价格
     */
    public function getDiscountAmount($amount)
    {
        if ($this->type == 0) {
            $discount = floor($amount * $this->discount / 100);
        } else if ($this->type == 1) {
            $discount = $this->discount;
        }
        return $discount;
    }
}
