<?php
namespace App;

use  Payum\LaravelPackage\Model\Payment as BasePayment;

class Payment extends BasePayment
{
    const STATE_PENDING = "pending";
    const STATE_COMPLETED = "completed";
    const STATE_FAILED = "failed";
    const STATE_REFUNDED = "refunded";
    const STATE_REFUNDING = "refunding";

    protected $table = "payments";
    protected $appends = ['gateway'];
    protected $casts = [
        'details' => 'json'
    ];
    

    public function client()
    {
        return $this->belongsTo('App\User', 'client_id');
    }

    public function subscription()
    {
        return $this->belongsTo('App\Subscription');
    }

    public function getGatewayAttribute()
    {
        return $this->subscription->gateway;
    }
}
