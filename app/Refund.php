<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    const STATE_CREATED = "created";
    const STATE_PENDING = "pending";
    const STATE_ACCEPTED = "accepted";
    const STATE_REJECTED = "rejected";

    public function payment()
    {
        return $this->belongsTo(\App\Payment::class);
    }
    /**
     * 获取该条退款单的paypal买家邮箱，然后查他的劣迹，即他是否之前退款过多次
     * @return annually和monthly
     */
    public function getSubscriptionCount($buyer_email)
    {
        $count = 0;
        $count = Payment::where('buyer_email', $buyer_email)
            ->where('status','refunded')->count();
        return $count;
    }
    /**
     * Voyager根据同名field去查找外键，需要优化下
     */
    /* public function paymentId() */
    /* { */
    /*     return $this->payment(); */
    /* } */

    public function isRefunding()
    {
        return in_array($this->status, [Refund::STATE_CREATED, Refund::STATE_PENDING]);
    }
}
