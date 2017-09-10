<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Subscription;

class CancelSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bba:cancel {agreement_id : 订阅ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '取消指定的订阅';

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
        $agreementId = $this->argument('agreement_id');
        $service = app(\App\Contracts\PaymentService::class);
        $service->setLogger($this);
        $this->comment("cancelling agreement id: $agreementId");
        $sub = Subscription::where('agreement_id', $agreementId)->first();
        if (!$sub) {
            $this->error("$agreementId not found");
            return;
        }
        $service->cancel($sub);
    }
}
