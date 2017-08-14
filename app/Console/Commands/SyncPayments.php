<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Contracts\PaymentService;
use App\Subscription;

class SyncPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bba:sync-payments {--paypal : 只同步paypal} {--stripe : 只同步stripe} {agreement-id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '与Paypal, Stripe同步支付订单;可指定特定订阅';

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
        $hasPaypal = $this->option('paypal');
        $hasStripe = $this->option('stripe');
        $agreeId = $this->argument('agreement-id');
        $gateways = [];
        if ($hasPaypal)
            $gateways[] = PaymentService::GATEWAY_PAYPAL;
        if ($hasStripe)
            $gateways[] = PaymentService::GATEWAY_STRIPE;
        $this->info("start syncing payments...");
        $service->setLogger($this);
        $service->syncPayments($gateways, $agreeId ? Subscription::where('agreement_id', $agreeId)->first() : null);
    }
}
