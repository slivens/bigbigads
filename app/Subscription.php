<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{

    /**
     * 订阅刚创建，未完成时的状态,每个用户应最多只有一个处于该状态的订阅
     */
    const STATE_CREATED = "created";

    /**
     * 订阅完成时的状态
     */
    const STATE_SUBSCRIBED = "subscribed";

    /**
     * 订阅完成支付的状态
     */
    const STATE_PAYED = "payed";

    /**
     * 订阅到期未续费的状态
     */
    const STATE_EXPIRED = "expired";

    /**
     * 取消订阅时的状态
     */
    const STATE_CANCLED = "canceled";

    /**
     * 挂起订阅时的状态
     */
    const STATE_SUSPENDED = "suspended";

    /**
     * 挂起订阅时的状态
     */
    const STATE_PENDING = "pending";

    public function user()
    {
        return $this->belongsTo('\App\User');
    }

    public function coupon()
    {
        return $this->belongsTo('\App\Coupon');
    }

    public function payments()
    {
        return $this->hasMany('\App\Payment');
    }
}
