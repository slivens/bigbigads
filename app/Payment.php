<?php
namespace App;

use  Payum\LaravelPackage\Model\Payment as BasePayment;

class Payment extends BasePayment
{
    protected $table="payments";

    public function client()
    {
        return $this->belongsTo('App\User', 'client_id');
    }
}
