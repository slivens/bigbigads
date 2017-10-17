<?php

namespace App\Http\Controllers;
use Braintree\ClientToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Log;
use App\User;
use App\Role;
use App\Plan;
use App\Subscription;
use App\Webhook;
use App\Coupon;
use App\Refund;
use App\ActionLog;
use App\Services\PaypalService;
use Carbon\Carbon;
use Payum\LaravelPackage\Controller\PayumController;
use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Model\CreditCard;
use Payum\Core\Model\Payment;
use App\Payment as OurPayment;
use App\Contracts\PaymentService;
use App\Jobs\SyncPaymentsJob;
use App\Jobs\SyncSubscriptionsJob;
use App\Jobs\GenerateInvoiceJob;
use App\Jobs\LogAction;
use GuzzleHttp\Client;

final class SubscriptionController extends PayumController
{
    private $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * 目前错误的返回统一以422作为Response返回码
     */
    public function responseError($desc, $code = -1) 
    {
        return response(["code"=>$code, "desc"=> $desc], 422);
    }

    /**
     * 生成唯一订单号:16位数字
     */
    public function generateNo()
    {
        return substr(implode("", array_map('ord', str_split(str_random(12),1))), 0, 16);
    }

    /**
     * 显示支付表单
     */
    public function form(Request $req)
    {
        //暂时当$planid不存在时重定向到404页面
        if ($req->plan || $req->name) {
            $planid = $req->plan;
            $name = $req->name;
            $plan = Plan::where('id',$planid)->orwhere('name',$name)->first();//find($planid);
            if(is_null($plan)){
                return view('errors.404');
            } else {
                return view('subscriptions.pay', ['plan'=>$plan, 'key' =>  env('STRIPE_PUBLISHABLE_KEY') ]);
            }
        } else {
            return view('errors.404');
        }
    }

    /**
     * 正常来讲，前端已经做了各种错误提示和防止提交无效的coupon，所以后端简化处理，统一提示一致的错误
     */
    protected function checkCoupon($code, $price)
    {
        $coupon = Coupon::where('code', $code)->first();    
        if (!$coupon)
            return false;
        if ($price < $coupon->total)
            return false;
        if ($coupon->used >= $coupon->uses)
            return false;
        if (!$coupon->start  || !$coupon->end)
            return false;
        $now = new Carbon();
        if ($now->lt(new Carbon($coupon->start)) || $now->gt(new Carbon($coupon->end)))
            return false;
        return $coupon;
    }
    /**
     * 支付表单提示的处理
     * @warning 如果用户已经订阅了，不允许创建同样的订阅
     */
   public function pay(Request $req)
    {
        // check that we have nonce and plan in the incoming HTTP request
        if(empty( $req->input('planid') ) ){
            return redirect()->back()->withErrors(['message' => 'Invalid request']);
        }

        $plan = Plan::find(intval($req->input('planid')));
        $user = Auth::user();
        $coupon = null;
        $discount = 0;
        // 如果存在对应的优惠券就使用
        if ($req->has('coupon')) {
            $coupon = $this->checkCoupon($req->coupon, $plan->amount);
            if (!$coupon)
                return redirect()->back()->withErrors(['message' => 'Invalid coupon']);
            $discount = $coupon->getDiscountAmount($plan->amount);
            /* if ($coupon->type == 0) { */
            /*     $discount = floor($plan->amount * $coupon->discount / 100); */
            /* } else if ($coupon->type == 1) { */
            /*     $discount = $coupon->discount; */
            /* } */
            if (Subscription::where('quantity', '>', 0)->where('user_id', $user->id)->where('coupon_id', $coupon->id)->count() >= $coupon->customer_uses)
                return redirect()->back()->withErrors(['message' => 'You have used the coupon']);
        }

        // 创建一个新的订阅(是否要清除已有的未支付订阅呢？需要的，防止数据库被填满，由于Paypal的token可能还在生效，所以在onPay时要确保不能支付成功)
        Subscription::where('user_id', $user->id)->where('status', Subscription::STATE_CREATED)->delete();
        $subscription = new Subscription();
        $subscription->user_id = $user->id;
        $subscription->plan = $plan->name;
        /* $subscription->agreement_id = $queryArr['token']; */
        $subscription->quantity = 0;
        $subscription->coupon_id = $coupon ? $coupon->id : 0;
        $subscription->setup_fee = $plan->amount - $discount;
        $subscription->frequency = $plan->frequency;
        $subscription->frequency_interval = $plan->frequency_interval;
        $subscription->gateway = PaymentService::GATEWAY_STRIPE;
        $subscription->status = Subscription::STATE_CREATED;
        $subscription->tag = Subscription::TAG_DEFAULT;
        $subscription->save();

        if ($req->has('payType') && $req->payType == 'stripe') {
            return $this->payByStripe($req, $plan, $user, $coupon);
        }       
        $service = $this->paymentService->getRawService(PaymentService::GATEWAY_PAYPAL);
        $approvalUrl = $service->createPayment($plan, $coupon ? ['setup_fee' => $plan->amount - $discount] : null);
        // 由于此时订阅的相关ID没产生，所以没办法通过保存ID，此时就先通过token作为中转
		$queryStr = parse_url($approvalUrl, PHP_URL_QUERY);
		$queryTmpArr = explode("&", $queryStr);
		$queryArr  = [];
		foreach($queryTmpArr as $key => $item) {
			$t = explode("=", $item);
			$queryArr[$t[0]] = $t[1];
        }

        $subscription->agreement_id = $queryArr['token'];
        $subscription->gateway = PaymentService::GATEWAY_PAYPAL;
        $subscription->save();
        return redirect($approvalUrl);
    }

    /**
     * 获取帐单信息
     * 额外添加首单交易完成时间，针对每个交易所属的订阅单独获取，目的在于使发票下载许可延期7天
     * @return \App\Payment[]
     */
    public function billings()
    {
        $user = Auth::user();
        $payments = $user->payments()->with('refund')->orderBy('created_at', 'desc')->get();
        foreach ($payments as $payment) {
            $firstPayment = $user->payments()->where('subscription_id', $payment->subscription_id)->orderBy('created_at', 'asc')->first();// 取回该交易上级订阅的首单交易
            $payment->firstCompletedTime = $firstPayment->getStartDateAttribute();
        } 
        return $payments;
    }

    /**
     * 获取所有计划
     */
    public function plans()
    {
        $items = Role::with('permissions', 'policies')->where('plan', '<>', null)->get();
        foreach ($items as $key=>$item) {
            $item->groupPermissions = $item->permissions->groupBy('table_name');
            $item->append('plans');
        }
        return $items;
    }

    /**
     * 显示所有服务端计划(主要用于测试目的)
     */
    public function showPlans()
    {
        $service = $this->paymentService->getRawService(PaymentService::GATEWAY_PAYPAL);
        return $service->dropPlans();
    }


    /**
     * 客户同意支付后的回调
     */
    public function onPay(Request $request)
    {
        echo "processing...don't close the window.";
        if ($request->success == 'false')
            return redirect('/app/profile?active=0');
        $subscription = Subscription::where('agreement_id', $request->token)->first();
        if (!($subscription instanceof Subscription)) {
            abort(401, "no subscription found");
        }
        $service = $this->paymentService->getRawService(PaymentService::GATEWAY_PAYPAL);

        $agreement = $service->onPay($request);

        if (!$agreement) {
            echo "Paypal encountered some error, please <a href='/app/profile?active=0'>Back</a> and retry.";
            return;
        }
        $payer = $agreement->getPayer();
        $info = $payer->getPayerInfo();
        // 中途取消的情况下返回profile页面
        if ($agreement == null) return redirect('/app/profile?active=0');
        $detail = $agreement->getAgreementDetails();
        // abort(401, "error on agreement");
        $subscription->agreement_id = $agreement->getId();
        $subscription->quantity = 1;
        $subscription->status = $subscription->translateStatus($agreement->getState());
        $subscription->remote_status = $agreement->getState();
        $subscription->buyer_email = $info->getEmail();
        $subscription->next_billing_date = $detail->getNextBillingDate();
        $subscription->save();
        $subscription->user->subscription_id = $subscription->id;
        $subscription->user->save();
        if (strtolower($agreement->getState()) != 'active') {
            /* $subscription->user->subscription_id = null; */
            /* $subscription->user->save(); */
            // 如果没有立刻成功，补一个取消订阅的操作
            /* $service->cacnel($subscription); */
            return redirect('/app/profile?active=0&sub=' . $subscription->id);
        }

        $this->paymentService->syncPayments([], $subscription);
        // 完成订阅后10秒后就去同步，基本上订单都已产生；如果没有产生，3分钟后再次同步试。同时webhook如果有收到，也会去同步。
        dispatch((new SyncPaymentsJob($subscription))->delay(Carbon::now()->addSeconds(10)));
        dispatch((new SyncPaymentsJob($subscription))->delay(Carbon::now()->addSeconds(30)));
        
        // 生成票据,此处入参为该订阅下所有的交易，执行过程中会跳过已经生成票据的交易
        dispatch(new GenerateInvoiceJob(OurPayment::where('subscription_id', $subscription->id)->get()));

        // 更改七日内的统计为guzzle 同步请求
        $domain = env('APP_URL');
        $url = $domain . 'payStatistics.html';
        $client = new Client();
        $client->request('GET', $url);
        return redirect('/app/profile?active=0');
    }

    /**
     * 处理Paypal支付的通知
     * @warning 在Sandbox下测试发现，Webhook的webhook机制非常不可靠。要么收不到，要么收到了但是发现验证失败，能成功验证的次数不多。
     */
    public function onPayWebhooks(Request $request)
    {
        Log::info('webhooks id: '. $request->id);
        $service = $this->paymentService->getRawService(PaymentService::GATEWAY_PAYPAL);
        $isValid = $service->verifyWebhook($request);
        /* if (!$isValid) */
        /*     return; */
        $webhook_id = $request->id;//webhook id
        $count = Webhook::where('webhook_id',$webhook_id)->count();
        // webhook有记录说明处理过就不再处理
        if($count == 0 ) {
            // 如果是无效的webhook就主从查询（可能是Paypal本身问题返回验证失败，也可能是伪造的），不管哪种情况，主动与Paypal做一次同步即可
            if (!$isValid) {
                switch ($request->event_type) {
                case 'PAYMENT.SALE.COMPLETED':
                    $agreementId = $request->resource['billing_agreement_id'];
                    $subscription = Subscription::where('agreement_id', $agreementId)->first();
                    if ($subscription)
                        dispatch(new SyncPaymentsJob($subscription));
                        dispatch(new GenerateInvoiceJob(OurPayment::where('subscription_id', $subscription->id)->get()));//同步交易以后生成新交易的票据
                }
            } else {
                $resource = $request->resource;
                $webhook = new Webhook;
                $webhook->webhook_id = $webhook_id;
                $webhook->create_time = $request->create_time;
                $webhook->resource_type = $request->resource_type;
                $webhook->event_type = $request->event_type;
                $webhook->summary = $request->summary;
                $webhook->webhook_content = base64_encode(serialize($resource));
                $re = $webhook->save();
                Log::info('$webhook->save(): '.$re);
                switch ($webhook->event_type) {
                case 'BILLING.SUBSCRIPTION.CANCELLED':
                    break;
                case 'PAYMENT.SALE.PENDING':
                    // 收到PENDING通常是安全原因引起，买家已付款，但是需要卖家确认才能收到款，暂不处理
                    // TODO: 创建PENDING的Payment，然后在其他状态中对其修改
                    break;
                case 'PAYMENT.SALE.COMPLETED':
                    // 用户完成支付才切换权限
                    $agreementId = $request->resource['billing_agreement_id'];
                    $subscription = Subscription::where('agreement_id', $agreementId)->first();
                    if (!$subscription) {
                        Log::warning("payment completed, but no `$agreementId` subscription found");
                        break;
                    }
                    dispatch(new SyncPaymentsJob($subscription));
                    dispatch(new GenerateInvoiceJob(OurPayment::where('subscription_id', $subscription->id)->get()));//同步交易以后生成新交易的票据
                    break;
                case 'PAYMENT.SALE.REFUNDED':
                    $payment = OurPayment::where('number', $resource['sale_id'])->first();
                    if (!$payment) {
                        Log::warning("the payment is refunded, but no record in the system");
                        break;
                    }
                    dispatch(new SyncPaymentsJob($payment->subscription));
                    break;
                }
            }
        }
    }

    protected function preparePaypalCheckout(Request $request)
    {
        if (!$request->has('amount'))
            return "amount parameter is required";
        $storage = $this->getPayum()->getStorage('Payum\Core\Model\ArrayObject');
        $details = $storage->create();
        $details['PAYMENTREQUEST_0_CURRENCYCODE'] = 'USD';
        $details['PAYMENTREQUEST_0_AMT'] = $request->amount;
        $storage->update($details);
        $captureToken = $this->getPayum()->getTokenFactory()->createCaptureToken('paypal_ec', $details, 'paypal_done');
        return redirect($captureToken->getTargetUrl());
    }

    protected function payByStripe(Request &$req, Plan &$plan, &$user, $coupon)
    {
        if (!$req->has('stripeToken'))
            return redirect()->back()->withErrors(['message' => 'invalid credit card']);
        $discount = 0;
        if ($coupon) {
            $discount = $coupon->getDiscountAmount($plan->amount);
        }
		$storage = $this->getPayum()->getStorage(Payment::class);
		$payment = $storage->create();
		$payment->setNumber($this->generateNo());
		$payment->setCurrencyCode($plan->currency);
		$payment->setTotalAmount(0); 
		$payment->setDescription($plan->display_name);
		$payment->setClientId($user->id);
		$payment->setClientEmail($user->email);
        $payment->setDetails(new \ArrayObject([
            'amount' => ($plan->amount  - $discount) * 100, 
            'currency' => $plan->currency, 
            'card' => $req->stripeToken,
            'local' => [
                'save_card' => true,
                'customer' => ['plan' => $plan->name]
            ]
        ]));
		$storage->update($payment);

        $captureToken = $this->getPayum()->getTokenFactory()->createCaptureToken('stripe', $payment, 'stripe_done');
        return redirect($captureToken->getTargetUrl());
    }

    protected function prepareStripeCheckout(Request $request)
    {
        $user = Auth::user();
        $plan = Plan::where('name', 'standard_monthly')->first();
        return $this->payByStripe($request, $plan, $user);
    }

    public function prepareCheckout(Request $request, $method)
    {
        switch ($method) {
        case 'paypal':
            return $this->preparePaypalCheckout($request);
        case 'stripe':
            return $this->prepareStripeCheckout($request);
        }

        return 'unknown payment method';
    }

    public function onPaypalDone(Request $request)
    {
        $token = $this->getPayum()->getHttpRequestVerifier()->verify($request);
        $gateway = $this->getPayum()->getGateway($token->getGatewayName());

        $gateway->execute($status = new GetHumanStatus($token));

        return \Response::json(array(
            'status' => $status->getValue(),
            'details' => iterator_to_array($status->getFirstModel())
        ));
    }

    public function onStripeDone(Request $request)
    {
        $token = $this->getPayum()->getHttpRequestVerifier()->verify($request);
        $gateway = $this->getPayum()->getGateway($token->getGatewayName());

        $gateway->execute($status = new GetHumanStatus($token));
        $payment = $status->getFirstModel();

        $details = $payment->getDetails();
        if ($status->getValue() == 'captured') {
            // 这整个流程应该是原子操作，应该放在队列中
            $user = User::find($payment->getClientId());
            $planName = $details['local']['customer']['plan'];

            $subscription = Subscription::where('user_id', $user->id)->where(['status' => Subscription::STATE_CREATED, 'gateway' => PaymentService::GATEWAY_STRIPE])->first();
            $subscription->agreement_id = $details['local']['customer']['subscriptions']['data'][0]['id'];
            $subscription->quantity = 1;
            /* $subscription->setup_fee = $details['amount'] / 100; */
            $subscription->save();


            $ourPayment = new OurPayment();
            switch ($status->getValue()) {
            case 'captured':
                $ourPayment->status = OurPayment::STATE_COMPLETED;
                break;
            default:
                $ourPayment->status = $status->getValue();
            }

            $ourPayment->client_id = $payment->getClientId();
            $ourPayment->client_email = $payment->getClientEmail();
            $ourPayment->amount = number_format($details['amount'] / 100, 2);
            $ourPayment->currency = ($payment->getCurrencyCode());
            $ourPayment->setNumber($payment->getNumber());
            $ourPayment->details = $details;
            $ourPayment->description = $payment->getDescription();
            $ourPayment->subscription()->associate($subscription);
            $ourPayment->save();


            $this->paymentService->handlePayment($ourPayment);
        }
        return redirect('/app/profile?active=0');
    }

    public function cancel($id)
    {
        $user = Auth::user();
        $sub = Subscription::where(['id' => $id, 'user_id' => $user->id])->first();
        if ($sub->status == Subscription::STATE_CANCLED) {
            return ['code' => 0, 'desc' => 'success'];
        }
        if ($sub->status != Subscription::STATE_SUBSCRIBED && $sub->status != Subscription::STATE_PAYED)
            return new Response(['code' => -1, 'desc' => "not a valid state:{$sub->status}"]);
        if (!$this->paymentService->cancel($sub))
            return ['code' => -1, 'desc' => "cancel failed"];
        dispatch(new LogAction(ActionLog::ACTION_USER_CANCEL, $sub->toJson(), "", $user->id));
        return ['code' => 0, 'desc' => 'success'];
    }

       
    public function sync($sid)
    {
        $user = Auth::user();
        $sub = $user->subscriptions()->where('agreement_id', $sid)->first();
        if (!$sub)
            return ['code' => -1, 'desc' => "$sid not found"];
        // 如果扣款失败，会自动被取消，因此同步的处理只需要处理pending的情况
        $this->paymentService->syncSubscriptions([], $sub);
        $this->paymentService->syncPayments([], $sub);
        /* if (Carbon::now()->diffInSeconds($sub->updated_at, true) > 30 && $sub->status ==  Subscription::STATE_PENDING) { */
        /*     $this->paymentService->cancel($sub); */
        /*     dispatch(new LogAction(ActionLog::ACTION_AUTO_CANCEL, $sub->toJson(), "", $user->id)); */
        /*     Log::info("{$sub->agreement_id} is auto canceled"); */
        /* } */
        return ['code' => 0, 'desc' => 'success'];
    }

    public function requestRefund($no)
    {
        $payment = OurPayment::where('number', $no)->first();
        if (!$payment)
            return $this->responseError("no such payment $no", -1);
        if ($payment->refund)
            return $this->responseError("you have request refunding before", -1);
        $refund = $this->paymentService->requestRefund($payment);
        return ['code' => 0, 'desc' => 'success'];
    }
}
