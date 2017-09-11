<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Contracts\PaymentService;
use App\User;

class SyncByUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bba:sync-by-user {email : 用户的email} {--only-subs : 只同步订阅} {--only-payments : 只同步订单}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '对指定用户同步订阅和订单，总是强制执行';

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
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("user of {$email} not found");
            return;
        }
        $syncAll = true;
        $syncSub = $this->option('only-subs');
        $syncPay = $this->option('only-payments');
        $paymentService = app(\App\Contracts\PaymentService::class);
        $paymentService->setParameter(PaymentService::PARAMETER_FORCE, true);
        $paymentService->setLogger($this);
        if ($syncAll || $syncSub)
            $paymentService->syncSubscriptions([], $user->subscriptions);
        if ($syncAll || $syncPay)
            $paymentService->syncPayments([], $user->subscriptions);
    }
}
