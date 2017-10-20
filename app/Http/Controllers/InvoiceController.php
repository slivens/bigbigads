<?php
/**
 * 票据模块，包含票据所有权判断和票据下载，生成方法位于 \App\Service\PaymentService
 * 
 * @category Payment
 * @package  Invoice
 * @author   ChenTeng <shanda030258@hotmail.com>
 * @license  MIT
 * @link     #
 * @since    1.0.0
 */
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Log;
use App\Payment;
use App\Contracts\PaymentService;
use App\Jobs\GenerateInvoiceJob;
use Carbon\Carbon;

/**
 * 票据控制器
 * 
 * @category Payment
 * @package  Invoice
 * @author   ChenTeng <shanda030258@hotmail.com>
 * @license  MIT
 * @link     #
 * @since    1.0.0
 */
class InvoiceController extends Controller
{
    private $_paymentService;
    
    public function __construct(PaymentService $_paymentService)
    {
        $this->paymentService = $_paymentService;
    }
    /**
     *  票据文件确认
     *  需要确认归属，即票据id对应的交易属于当前登录的用户
     *  需要确认文件是否存在，并且确认交易所属的订阅首单成交时间已经过去7天
     *  只在票据不存在(交易表中invoice_id为null)或者票据文件不存在磁盘中时执行生成
     *  如果invoice_id为null,可以认为交易流程出了问题，交易完成之后没有生成票据
     * 
     * @param int $invoiceId 票据id
     * 
     * @return object 正确返回success=>true,code=>0的json,错误返回错误提示
     */
    public function getGenerateStatus($invoiceId)
    {
        $user = Auth::user();
        if (!$user) {
            // 用户验证失败
            Log::info('verify users failed on getGenerateStatus because user not found');
            return $this->responseError('You will be login first!');
        }
        $payment = Payment::where('invoice_id', $invoiceId)->first();
        if (!$payment) {
            // 请求的票据id无效
            return $this->responseError('Cannot download invoice,invalid invoice');
        }
        if ($payment->client_id != $user->id) {
            // 交易不属于当前用户
            Log::info("this invoice (id:$invoiceId) is not users (id:$user->id)");
            return $this->responseError('Cannot download invoice,because its not yours.');
        } elseif ($payment->status != Payment::STATE_COMPLETED) {
            // 非成功交易
            Log::info("this payment (invoice_id:$invoiceId) is not a completed payment");
            return $this->responseError('Cannot download invoice,because this is not a completed payment.');
        }
        $firstPayment = $user->payments()->orderBy('created_at', 'asc')->first();
        if (Carbon::now()->diffInDays($firstPayment->created_at) < 7) {
            // 首单交易时间距今7天内
            return $this->responseError('Please download the invoice after 7 days.');
        }
        if ($this->paymentService->checkInvoiceExists($invoiceId)) {
            // 确认文件存在，成功通过
            return response()->json(
                [
                    'code' => 0,
                    'success' => true
                ], 200
            );
        } else {
            // 请求的票据id有效，存在交易表中，但是对应的文件不在磁盘中，重新生成，这里使用强制生成
            Log::info("payment number:$payment->number invoice is not exist, will be re-generate.");
            dispatch(new GenerateInvoiceJob(Payment::where('invoice_id', $invoiceId)->get(), true));// 入参必须为collection类型，前面的first()获得的是payment类型
            return $this->responseError('Cannot download invoice,please refresh this page and try again later.');
        }
    }

    /**
     * 票据下载方法
     * 需要阻止的对象：1）非登录用户；2）非交易所有者；3）距离初始交易成功时间少于7天的；4）票据所属交易非成功状态
     *
     * @param int $invoiceId ,票据的id，也是票据文件名称，具体文件名称为 票据id.pdf
     * 
     * @return object 下载文件
     */
    public function downloadInvoice($invoiceId)
    {
        $user = Auth::user();
        if (!$user) {
            // 用户验证失败
            Log::info('verify users failed on getGenerateStatus because user not found');
            return $this->responseError('You will be login first!');
        }
        $thisPayment = Payment::where('invoice_id', $invoiceId)->where('client_id', $user->id)->first();
        $firstPayment = $user->payments()->orderBy('created_at', 'asc')->first();
        if (Carbon::now()->diffInDays($firstPayment->created_at) < 7) {
            // 首单交易时间距今7天内
            return $this->responseError('Please download the invoice after 7 days.');
        }
        if ($thisPayment->status == Payment::STATE_COMPLETED) {
            // 通过验证，执行下载
            Log::info("downloading Invoice file on use invoice_id:$invoiceId");
            return $this->paymentService->downloadInvoice($invoiceId);
        } else {
            // 非成功交易
            return $this->responseError('Cannot download invoice,because this is not a completed payment or is not your payment.');
        }
    }
}