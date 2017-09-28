<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use Illuminate\Database\Eloquent\Collection;

class GenerateInvoiceJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    private $payments;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Collection $p)
    {
        $this->payments = $p;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->payments instanceof Collection) {
            Log::warning("jobs:GenerateInvoice : payments are invalid");
            return;
        }
        $paymentService = app(\App\Contracts\PaymentService::class);
        foreach($this->payments as $payment){
            log::info('use payment number: '.$payment->number);
            $paymentService->generateInvoice($payment->number);//此处入参为交易id,17位，payment的number字段值
        }
    }
}
