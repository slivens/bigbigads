<?php

namespace App\Http\Controllers;
use Braintree\ClientToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Log;
use DB;
use App\User;
use App\Role;
use App\Plan;
use App\Subscription;
use App\Webhook;
use App\Coupon;
use App\Services\PaypalService;
use Carbon\Carbon;
use Payum\LaravelPackage\Controller\PayumController;
use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Model\CreditCard;
use Payum\Core\Model\Payment;
use App\Payment as OurPayment;
use App\Contracts\PaymentService;
use App\Jobs\SyncPaymentsJob;

final class SubscriptionController extends PayumController
{
    private $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
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
        //check that we have nonce and plan in the incoming HTTP request
        if(empty( $req->input('planid') ) ){
            return redirect()->back()->withErrors(['message' => 'Invalid request']);
        }

        $plan = Plan::find(intval($req->input('planid')));
        $user = Auth::user();
        $coupon = null;
        $discount = 0;
        //如果存在对应的优惠券就使用
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

        //创建一个新的订阅(是否要清除已有的未支付订阅呢？需要的，防止数据库被填满，由于Paypal的token可能还在生效，所以在onPay时要确保不能支付成功)
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
        $subscription->save();

        if ($req->has('payType') && $req->payType == 'stripe') {
            return $this->payByStripe($req, $plan, $user, $coupon);
        }       
        $service = new \App\Services\PaypalService;
        $approvalUrl = $service->createPayment($plan, $coupon ? ['setup_fee' => $plan->amount - $discount] : null);
        //由于此时订阅的相关ID没产生，所以没办法通过保存ID，此时就先通过token作为中转
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
     * @return \App\Payment[]
     */
    public function billings()
    {
        $user = Auth::user();
        return $user->payments()->orderBy('created_at', 'desc')->get();
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
        $service = new PaypalService();
        return $service->dropPlans();
    }


    /**
     * 客户同意支付后的回调
     */
    public function onPay(Request $request)
    {
        echo "processing...don't close the window";
        $subscription = Subscription::where('agreement_id', $request->token)->first();
        if (!($subscription instanceof Subscription)) {
            abort(401, "no subscription found");
        }

        $service = new PaypalService();

        $payment = $service->onPay($request);
        //中途取消的情况下返回profile页面
        if ($payment == null) return redirect('/app/profile?active=0');
            //abort(401, "error on payment");
        $subscription->agreement_id = $payment->getId();
        $subscription->quantity = 1;
        $subscription->status = Subscription::STATE_SUBSCRIBED;
        $subscription->save();

        // 正常来讲，3分钟内会有webhook产生，但是webhook不是个可靠机制，所以在5分钟后再次同步试试
        dispatch((new SyncPaymentsJob($subscription))->delay(Carbon::now()->addMinutes(3)));
        return redirect('/app/profile?active=0');
    }

    /**
     * 处理Paypal支付的通知
     * @warning Webhook的通知可能是不可靠的，因此我们还需要另外一种主动查询的机制去保证所有订单被正确的处理。
     */
    public function onPayWebhooks(Request $request)
    {
        Log::info('webhooks id: '. $request->id);
        /* if (!$request->has('id')) { */
        /*     Log::warning('invalid webhook'); */
        /*     return; */
        /* } */
        $webhook_id = $request->id;//webhook id
        $count = Webhook::where('webhook_id',$webhook_id)->count();
        //Log::info('$select: '.$select);
        //Log::info('count($select): '.count($select));
        //$select = DB::select('select * from webhooks where webhook_id = :webhook_id',['webhook_id'=>$webhook_id]);
        if($count == 0) {
            $resource = $request->resource;
            $webhook = new Webhook;
            $webhook->webhook_id = $webhook_id;
            $webhook->create_time = $request->create_time;
            $webhook->resource_type = $request->resource_type;
            $webhook->event_type = $request->event_type;
            $webhook->summary = $request->summary;
            $webhook->webhook_content = $resource;
            $re = $webhook->save();
            Log::info('$webhook->save(): '.$re);
            switch ($webhook->event_type) {
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
                $user = $subscription->user;

                $payment = new OurPayment();
                $payment->status = OurPayment::STATE_COMPLETED;
                $payment->client_id = $user->id;
                $payment->client_email = $user->email;
                $payment->amount = $resource['amount']['total'];
                $payment->currency =  $resource['amount']['currency'];
                $payment->number = $resource['id'];// Paypal的订单号是自动生成的
                $payment->description = $request->summary;
                $payment->details = $resource;
                $payment->subscription()->associate($subscription);
                $payment->save();

                $this->paymentService->handlePayment($payment);
                break;
            case 'PAYMENT.SALE.REFUNDED':
                $payment = OurPayment::where('number', $resource['sale_id'])->first();
                if (!$payment) {
                    Log::warning("the payment is refunded, but no record in the system");
                    break;
                }
                $payment->status = OurPayment::STATE_REFUNDED;
                $payment->save();
                break;
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
            //$subscription->agreement_id = ;$details['local']['customer']['subscriptions']['data']['id'];
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
}
