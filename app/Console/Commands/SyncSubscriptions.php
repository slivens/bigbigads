<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Subscription;
use App\Contracts\PaymentService;

class SyncSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bba:sync-subscriptions {--paypal : 只同步paypal} {--stripe : 只同步stripe} {agreement-id?} {--f|force : 强制与远程同步}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '与Paypal, Stripe同步订阅;可指定特定订阅;默认跳过被取消的订阅，除非指定-f参数';

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
        $force = $this->option('force');
        $agreeId = $this->argument('agreement-id');
        $gateways = [];
        if ($hasPaypal)
            $gateways[] = PaymentService::GATEWAY_PAYPAL;
        if ($hasStripe)
            $gateways[] = PaymentService::GATEWAY_STRIPE;
        $this->info("start syncing subscriptions...");
        if ($force) {
            $service->setParameter(PaymentService::PARAMETER_FORCE, true);
            $this->info("force flag set");
        }
        $service->setLogger($this);
        $sub = null;
        if ($agreeId) {
            $sub = Subscription::where('agreement_id', $agreeId)->first();
            if (!$sub) {
                $this->error("$agreeId not found");
                return;
            }

        }
        $service->syncSubscriptions($gateways, $sub);
    }
}
