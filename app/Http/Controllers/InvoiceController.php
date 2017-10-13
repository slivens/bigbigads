<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Log;
use App\Payment;
use App\Contracts\PaymentService;

class InvoiceController extends Controller
{
    private $paymentService;
    
    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }
    /**
     *  票据文件确认
     *  需要确认归属，即票据id对应的交易属于当前登录的用户
     *  需要确认文件是否存在
     * @param int $invoice_id 票据id
     * @todo 如果文件不存在，将生成票据任务推入队列，并且需要做判断，一定时间内只能推1次
     */
    public function getGenerateStatus($invoice_id)
    {
        $user = Auth::user();
        if(!$user) {
            Log::info('verify users failed on getGenerateStatus because user not found');
            return $this->responseError('You will be login first!');
        }
        $payment = Payment::where('invoice_id', $invoice_id)->first();
        // 请求的票据id无效
        if(!$payment) {
            return $this->responseError("Cannot download invoice,invalid invoice");
        }
        if($payment->client_id != $user->id) {
            Log::info("this invoice (id:$invoice_id) is not users (id:$user->id)");
            return $this->responseError('Cannot download invoice,because its not yours.');
        } elseif($payment->status != 'completed') {
            Log::info("this payment (invoice_id:$invoice_id) is not a completed payment");
            return $this->responseError('Cannot download invoice,because this is not a completed payment.');
        }
        if($this->paymentService->checkInvoiceExists($invoice_id)) {
            return response()->json([
                'code' => 0,
                'success' => true
            ], 200);
        } else {
            //请求的票据id有效，存在交易表中，但是对应的文件不在磁盘中
            return $this->responseError("Cannot download invoice,because file is not in disk.");
        }
    }

    /**
    * 票据下载方法
    *
    * @param int $sub_id subscription id ,订阅的id,非agreement_id
    * @param int $invoice_id ,票据的id，也是票据文件名称，具体文件名称为 票据id.pdf
    * @return object 下载文件
    */
    public function downloadInvoice($invoice_id)
    {
        $user = Auth::user();
        if(!$user) {
            Log::info('verify users failed on getGenerateStatus because user not found');
            return $this->responseError('You will be login first!');
        }
        if(Payment::where('invoice_id', $invoice_id)->where('client_id', $user->id)->value('status') == 'completed') {
            Log::info("downloading Invoice file on use invoice_id:$invoice_id");
            return $this->paymentService->downloadInvoice($invoice_id);
        } else {
            return $this->responseError('Cannot download invoice,because this is not a completed payment or is not your payment.');
        }
    }
}