<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bba:sync-subscriptions {--paypal : 只同步paypal} {--stripe : 只同步stripe}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '与Paypal, Stripe同步订阅';

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
     * Execute the console command.
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
        $this->info("start syncing subscriptions...");
        $service->setLogger($this);
        $service->syncSubscriptions($gateways);
    }
}
