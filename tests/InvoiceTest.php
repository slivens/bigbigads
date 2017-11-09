<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Payment;
use App\Subscription;

class InvoiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * 测试生成指定订单的命令是否有问题
     *
     * ```
     * php artisan bba:invoice <交易ID> --force
     * ```
     *
     * 检查对应交易的invoice_id是否被设置，同时pdf文件存在；
     *
     * @return void
     */
    public function testInvoiceCmd()
    {
        $payment = Payment::where('status', Payment::STATE_COMPLETED)->whereHas('subscription', function($query) {
            $query->where('gateway', Subscription::GATEWAY_PAYPAL);
        })->first();
        $this->assertTrue($payment instanceof Payment);
        Artisan::call('bba:invoice', [
            'transaction_id' => $payment->number,
            '--force' => true
        ]);
        $newPayment = Payment::find($payment->id);
        $path = config('payment.invoice.save_path') . '/' . $newPayment->invoice_id . '.pdf';
        $this->assertTrue(Storage::exists($path));
    }
}
