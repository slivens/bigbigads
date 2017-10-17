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
    private $isForce;
    /**
     * Create a new job instance.
     * 第二个参数为是否强制生成，
     * @return void
     */
    public function __construct(Collection $p, $isForce = NULL)
    {
        $this->payments = $p;
        $this->isForce = $isForce;
    }

    /**
     * Execute the job.
     * 可选强制生成
     * @return void
     */
    public function handle()
    {
        Log::info('generate invoice');
        if (!$this->payments instanceof Collection) {
            Log::warning('jobs:GenerateInvoice : payments are invalid');
            return;
        }
        $paymentService = app(\App\Contracts\PaymentService::class);
        if(empty($this->isForce)) {
            $this->isForce = false;
        } else {
            $this->isForce = true;
            $extraMessage = ',and this is re-generate.';// 标明强制生成，与常规生成区分开来
        }   
        foreach($this->payments as $payment) {
            $logMessage = 'use payment number: ' . $payment->number . ' to generate invoice';
            if($this->isForce) $logMessage .= $extraMessage;
            Log::info($logMessage);
            $paymentService->generateInvoice($payment->number, $this->isForce);// 此处入参为交易id,17位，payment的number字段值
        }
    }
}
