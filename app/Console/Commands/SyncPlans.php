<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Plan;
use Payum\Stripe\Request\Api\CreatePlan;
use Stripe\Error;
use Stripe\Plan as StripePlan;
use Stripe\Stripe;
use App\Contracts\PaymentService;

class SyncPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bba:sync-plans {--paypal : 只同步paypal} {--stripe : 只同步stripe}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '与Paypal,Stripe同步计划';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 对Payum分析，只提供了CreatePlan用于创建Plan；而其他方面并未提供，考虑过对Payum封装，但细想并无必要，这部分差异大，同时封装只是对接口做一层转接，反而觉得过度设计。
     *
     * @return mixed
     */
    public function handle()
    {
        $service = app(\App\Contracts\PaymentService::class);
        /* $isAll = true; */
        $hasPaypal = $this->option('paypal');
        $hasStripe = $this->option('stripe');
        $gateways = [];
        if ($hasPaypal)
            $gateways[] = PaymentService::GATEWAY_PAYPAL;
        if ($hasStripe)
            $gateways[] = PaymentService::GATEWAY_STRIPE;
        /* try { */
            $this->info("start syncing plans...");
            $service->setLogger($this);
            $service->syncPlans($gateways);
        /* } catch (\Exception $e) { */
        /*     $this->error("Error Occured:" . $e->getMessage()); */
        /* } */
    }
}
