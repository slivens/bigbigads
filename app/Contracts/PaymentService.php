<?php
namespace App\Contracts;

use Illuminate\Support\Collection;

interface PaymentService
{
    const GATEWAY_NONE = "none";
    const GATEWAY_STRIPE = "stripe";
    const GATEWAY_PAYPAL = "paypal";

    /**
     * 同步计划
     * @param Illuminate\Support\Collection $gateways 为空值时表示同步所有计划;否则同步指定的计划。每个数组项应从GATEWAY_*中取值。
     * @return void
     * @throw Exception
     */
    public function syncPlans(Array $gateways);

    /**
     * 用于调试目的日志输出
     * @param mixed $logger 控制台或者Log
     */
    public function setLogger($logger);
}
