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
use App\Jobs\LogAction;
use GuzzleHttp\Client;
use App\Jobs\SendUserMail;
use App\Jobs\SendUnsubscribeMail;
use App\GatewayConfig;
use App\Exceptions\BusinessErrorException;
use Voyager;

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
        return substr(implode("", array_map('ord', str_split(str_random(12), 1))), 0, 16);
    }

    /**
     * 显示支付表单
     */
    public function form(Request $req)
    {
        // 暂时当$planid不存在时重定向到404页面
        if ($req->plan || $req->name) {
            $planid = $req->plan;
            $name = $req->name;
            $plan = Plan::where('id', $planid)->orwhere('slug', $name)->first();// find($planid);
            if (is_null($plan)) {
                return view('errors.404');
            } else {
                $plan->amount = number_format($plan->amount, 2); //价格统一格式：2位小数
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
        if (!$coupon) {
            return false;
        }
        if ($price < $coupon->discount) {
            return false;
        }
        if ($coupon->used >= $coupon->uses) {
            return false;
        }
        if (!$coupon->start  || !$coupon->end) {
            return false;
        }
        $now = new Carbon();
        if ($now->lt(new Carbon($coupon->start)) || $now->gt(new Carbon($coupon->end))) {
            return false;
        }
        return $coupon;
    }
    /**
     * 支付表单提示的处理
     *
     * 如果没有指明支付类型，就使用默认网关
     * @warning 如果用户已经订阅了，不允许创建同样的订阅
     */
    public function pay(Request $req)
    {
        // check that we have nonce and plan in the incoming HTTP request
        if (!$req->has('planid') && !$req->has('plan_name')) {
            // TODO:统一处理
            return "invalid request";
            /* return redirect()->back()->withErrors(['desc' => 'Invalid request']); */
        }
        if ($req->has('planid')) {
            $plan = Plan::find(intval($req->input('planid')));
        } else {
            $plan = Plan::where('name', $req->input('plan_name'))->first();
        }
        $user = Auth::user();
        $coupon = null;
        $discount = 0;
        // 如果存在对应的优惠券就使用
        if ($req->has('coupon')) {
            $coupon = $this->checkCoupon($req->coupon, $plan->amount);
            if (!$coupon) {
                return redirect()->back()->withErrors(['message' => 'Invalid coupon']);
            }
            $discount = $coupon->getDiscountAmount($plan->amount);
            /* if ($coupon->type == 0) { */
            /*     $discount = floor($plan->amount * $coupon->discount / 100); */
            /* } else if ($coupon->type == 1) { */
            /*     $discount = $coupon->discount; */
            /* } */
            if (Subscription::where('quantity', '>', 0)->where('user_id', $user->id)->where('coupon_id', $coupon->id)->count() >= $coupon->customer_uses) {
                return redirect()->back()->withErrors(['message' => 'You have used the coupon']);
            }
        }
        $gatewayConfig = GatewayConfig::where('gateway_name', Voyager::setting('current_gateway'))->first();
        if (!$gatewayConfig) {
            // TODO: 统一定向到某个错误页面或者前端需要知道如何读取Flash数据
            return 'No Gateway Config';
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
        $subscription->gateway = PaymentService::GATEWAY_STRIPE;// TODO:删除，将用gateway_id，以支持多网关
        $subscription->gateway_id = $gatewayConfig->id;
        $subscription->status = Subscription::STATE_CREATED;
        $subscription->tag = Subscription::TAG_DEFAULT;
        $subscription->skype = $req->input('skype', ''); // 获取skype字段
        $subscription->save();

        if ($req->has('payType') && $req->payType == 'stripe') {
            return $this->payByStripe($req, $plan, $user, $coupon);
        }
        if ($gatewayConfig->factory_name == GatewayConfig::FACTORY_PAYPAL_EXPRESS_CHECKOUT) {
            return $this->preparePaypalCheckout($req, $subscription->setup_fee);    
        }
        $service = $this->paymentService->getRawService(PaymentService::GATEWAY_PAYPAL);
        $approvalUrl = $service->createPayment($plan, $coupon ? ['setup_fee' => $plan->amount - $discount] : null);
        // 由于此时订阅的相关ID没产生，所以没办法通过保存ID，此时就先通过token作为中转
        $queryStr = parse_url($approvalUrl, PHP_URL_QUERY);
        $queryTmpArr = explode("&", $queryStr);
        $queryArr  = [];
        foreach ($queryTmpArr as $key => $item) {
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
     * 时间限制：
     * 首个订阅的首单成交时间7天之内可以申请1次退款，其他交易不能退款。
     * 7天后所有成功交易可以下载票据
     *
     * @return \App\Payment[]
     */
    public function billings()
    {
        $user = Auth::user();
        $payment = $user->payments()->with('refund')->orderBy('created_at', 'desc')->get();
        // 后续新增的plan 需要往$level数组内按 des, level, plan添加直接让前端使用
        // TODO: 前后端都应该修改，错误的使用方式
        $levels = [
            ['des'  =>  'lite_monthly'              , 'level' => 'Lite',       'plan' => 'Monthly'],
            ['des'  =>  'lite_annual'               , 'level' => 'Lite',       'plan' => 'Annual'],
            ['des'  =>  'lite_quarterly'            , 'level' => 'Lite',       'plan' => 'Quarterly'],
            ['des'  =>  'standard_monthly'          , 'level' => 'Standard',   'plan' => 'Monthly'],
            ['des'  =>  'standard_quarter_monthly'  , 'level' => 'Standard',   'plan' => 'Quarterly'],
            ['des'  =>  'standard'                  , 'level' => 'Standard',   'plan' => 'Annual'],
        ];
        foreach ($payment as $index => $key) {
            $endDate = new Carbon($key->end_date);
            $startDate = new Carbon($key->start_date);
            foreach ($levels as $level) {
                if ($level['des'] == $key->description) {
                    $payment[$index]['level'] = $level['level'];
                    $payment[$index]['plan'] = $level['plan'];
                }          
            }
            $payment[$index]['expireDate'] = $endDate->toFormattedDateString();
            $payment[$index]['startDate'] = $startDate->toFormattedDateString();
        }
        return $payment;
    }

    /**
     * 获取所有计划
     *
     * 默认禁止返回权限相关信息，只有必要时才允许返回
     * @return Role $items 计划列表
     */
    public function plans()
    {
        $items = Role::where('plan', '<>', null)->get();
        /* $items = Role::with('permissions', 'policies')->where('plan', '<>', null)->get(); */
        
        foreach ($items as $key => $item) {
            /* $item->groupPermissions = $item->permissions->groupBy('table_name'); */
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
     * TODO: onPay这一步，应该考虑支持API请求会比较灵活
     */
    public function onPay(Request $request)
    {
        /* echo "processing...don't close the window."; */
        if ($request->success == 'false') {
            return redirect(Voyager::setting('payed_redirect'));
        }
        $subscription = Subscription::where('agreement_id', $request->token)->first();
        if (!($subscription instanceof Subscription)) {
            abort(500, "no subscription found");
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
        if ($agreement == null) {
            return redirect(Voyager::setting('payed_redirect'));
        }
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
        // 对成功订阅的用户发送帮助邮件, 排除社交登录无有效邮箱和内部测试使用邮箱
        // modify by ruanmingzhi 2017-12-14
        $user = User::find($subscription->user->id);
        if ($user && $user->id > 3 && User::isTestEmail($user->email)) {
            Log::info("help mail sended after subscription");
            dispatch(new SendUserMail($user, new \App\Mail\PayHelpMail($user)));
        }
        // 完成订阅后的/app/profile界面，如果没有成功会自己尝试同步;同时webhook如果有收到，也会去同步。
        /* dispatch((new SyncPaymentsJob($subscription))->delay(Carbon::now()->addSeconds(10))); */
        dispatch((new SyncPaymentsJob($subscription))->delay(Carbon::now()->addSeconds(30)));

        // 更改七日内的统计为guzzle 同步请求
        try {
            $domain = env('APP_URL');
            $url = $domain . 'payStatistics.html';
            $client = new Client();
            $client->request('GET', $url);
        } catch (\Exception $e) {
        }
        return redirect(Voyager::setting('payed_redirect'));
    }

    /**
     * 处理Paypal支付的通知
     *
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
        $count = Webhook::where('webhook_id', $webhook_id)->count();
        // webhook有记录说明处理过就不再处理
        if ($count == 0) {
            // 如果是无效的webhook就主从查询（可能是Paypal本身问题返回验证失败，也可能是伪造的），不管哪种情况，主动与Paypal做一次同步即可
            if (!$isValid) {
                switch ($request->event_type) {
                    case 'PAYMENT.SALE.COMPLETED':
                        $agreementId = $request->resource['billing_agreement_id'];
                        $subscription = Subscription::where('agreement_id', $agreementId)->first();
                        if ($subscription) {
                            dispatch(new SyncPaymentsJob($subscription));
                        }
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
                Log::info('$webhook->save(): ' . $re);
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

    /**
     * Paypal Express Checkout
     */
    protected function preparePaypalCheckout(Request $request, $amount = null)
    {
        if (!$request->has('amount') && !$amount) {
            return "amount parameter is required";
        }
        if (!$amount) {
            $amount = $request->amount;
        }
        $storage = $this->getPayum()->getStorage('Payum\Core\Model\ArrayObject');
        $details = $storage->create();
        $details['PAYMENTREQUEST_0_CURRENCYCODE'] = 'USD';
        $details['PAYMENTREQUEST_0_AMT'] = $amount;
        $storage->update($details);
        $captureToken = $this->getPayum()->getTokenFactory()->createCaptureToken(Voyager::setting('current_gateway'), $details, 'paypal_done');
        return redirect($captureToken->getTargetUrl());
    }

    protected function preparePaypalRestCheckout(Request $request, $amount = null)
    {
        $payment = $this->paymentService->checkout();           
        return redirect($payment->getApprovalLink());
    }

    protected function payByStripe(Request &$req, Plan &$plan, &$user, $coupon)
    {
        if (!$req->has('stripeToken')) {
            return redirect()->back()->withErrors(['message' => 'invalid credit card']);
        }
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
        $payment->setDetails(
            new \ArrayObject(
                [
                    'amount' => ($plan->amount  - $discount) * 100,
                    'currency' => $plan->currency,
                    'card' => $req->stripeToken,
                    'local' => [
                        'save_card' => true,
                        'customer' => ['plan' => $plan->name]
                    ]
                ]
            )
        );
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
            case 'paypal_rest':
                return $this->preparePaypalRestCheckout($request);
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
        $detail = iterator_to_array($status->getFirstModel());
        if (!Auth::user()) {
            throw new BusinessErrorException('pay failed, if you have completed payment, please contact us: support@onlineadspyer.com');
        }
        if (!($status->getValue() == 'captured')) {
            Log::warning('pay failed', ['user' => Auth::user()->email, 'detail' => $detail]);
            throw new BusinessErrorException('pay failed, please try again <a href="/pricing">Back</a>, or mail to support@onlineadspyer.com');
        }

        if (\App\Payment::where('number', $detail['TRANSACTIONID'])->count() > 0) {
            return redirect(Voyager::setting('payed_redirect'));
        }
        $user = Auth::user();
        $subscription = $user->subscriptions()->where('status', Subscription::STATE_CREATED)->first();
        $subscription->agreement_id = '';
        $subscription->quantity = 1;
        $subscription->status = Subscription::STATE_PAYED;
        $subscription->remote_status = '';
        $subscription->buyer_email = $detail['EMAIL'];
        // TODO:总是按月，应该做得更细致些
        $subscription->next_billing_date = Carbon::now()->addMonth();
        $subscription->save();
        $subscription->user->subscription_id = $subscription->id;
        $subscription->user->save();

        $payment = new \App\Payment();
        $payment->number = $detail['TRANSACTIONID'];
        $payment->description = '';
        $payment->client_id = $user->id;
        $payment->client_email = $user->email;
        $payment->amount = $detail['AMT'];
        $payment->currency = $detail['PAYMENTINFO_0_CURRENCYCODE'];
        $payment->details = $detail;
        $payment->buyer_email = $detail['EMAIL'];
        $payment->status = \App\Payment::STATE_COMPLETED;
        $payment->created_at = Carbon::now();

        $payment->subscription()->associate($subscription);
        $payment->save();

        $subscription->user->fixInfoByPayments();
        Log::info('pay detail:', ['detail' => $detail]);
        $redirectUrl = Voyager::setting('payed_redirect');
        if ($request->expectsJson()) {
            return response()->json(['redirect' => $redirectUrl]);
        }
        return redirect($redirectUrl);
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
        if ($sub->status != Subscription::STATE_SUBSCRIBED && $sub->status != Subscription::STATE_PAYED) {
            return new Response(['code' => -1, 'desc' => "not a valid state:{$sub->status}"]);
        }
        if (!$this->paymentService->cancel($sub)) {
            return ['code' => -1, 'desc' => "cancel failed"];
        }
        dispatch(new LogAction(ActionLog::ACTION_USER_CANCEL, $sub->toJson(), "", $user->id));
        // 用户申请退订后发送退订邮件到用户邮箱
        // Todo 通用邮件模板合并进去后需要使用通用jOb发送邮件，并删除该多余的job
        dispatch(new SendUnsubscribeMail($user));
        return ['code' => 0, 'desc' => 'success'];
    }

       
    public function sync($sid)
    {
        $user = Auth::user();
        $sub = $user->subscriptions()->where('agreement_id', $sid)->first();
        if (!$sub) {
            return ['code' => -1, 'desc' => "$sid not found"];
        }
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
        $user = Auth::user();
        $payment = OurPayment::where('number', $no)->first();
        if (!$payment) {
            return $this->responseError("no such payment $no", -1);
        }
        if ($payment->refund) {
            return $this->responseError("you have request refunding before", -1);
        }
        $refund = $this->paymentService->requestRefund($payment);
        
        return ['code' => 0, 'desc' => 'success'];
    }
}
