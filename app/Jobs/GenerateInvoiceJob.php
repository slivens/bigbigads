<?php
/**
 * 票据生成Job
 * 
 * @category Job
 * @package  Invoice
 * @author   ChenTeng <shanda030258@hotmail.com>
 * @license  MIT
 * @link     #
 * @since    1.0.0
 */
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use Illuminate\Database\Eloquent\Collection;
/**
 * 票据生成Job，调用\App\Service\generateInvoice完成批量或者单个票据生成
 * 
 * @category Job
 * @package  Invoice
 * @author   ChenTeng <shanda030258@hotmail.com>
 * @license  MIT
 * @link     #
 * @since    1.0.0
 */
class GenerateInvoiceJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    private $_payments;
    private $_isForce;
    /**
     * 构造函数
     * 
     * @param Collection $_payments 需要生成票据的交易集合
     * @param bool       $_isForce  是否强制生成
     * 
     * @return void
     */
    public function __construct(Collection $_payments, $_isForce = null)
    {
        $this->payments = $_payments;
        $this->isForce = $isForce;
    }

    /**
     * Execute the job.
     * 可选强制生成
     * 
     * @return object
     */
    public function handle()
    {
        Log::info('generate invoice');
        if (!$this->payments instanceof Collection) {
            Log::warning('jobs:GenerateInvoice : payments are invalid');
            return;
        }
        $paymentService = app(\App\Contracts\PaymentService::class);
        if (empty($this->isForce)) {
            $this->isForce = false;
        } else {
            $this->isForce = true;
            $extraMessage = ',and this is re-generate.';// 标明强制生成，与常规生成区分开来
        }   
        foreach ($this->payments as $payment) {
            $logMessage = 'use payment number: ' . $payment->number . ' to generate invoice';
            if ($this->isForce) {
                $logMessage .= $extraMessage;
            }
            Log::info($logMessage);
            $paymentService->generateInvoice($payment->number, $this->isForce);// 此处入参为交易id,17位，payment的number字段值
        }
    }
}
