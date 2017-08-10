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

class SubscriptionController extends PayumController
{
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
            if ($coupon->type == 0) {
                $discount = floor($plan->amount * $coupon->discount / 100);
            } else if ($coupon->type == 1) {
                $discount = $coupon->discount;
            }
            if (Subscription::where('quantity', '>', 0)->where('user_id', $user->id)->where('coupon_id', $coupon->id)->count() >= $coupon->customer_uses)
                return redirect()->back()->withErrors(['message' => 'You have used the coupon']);
        }

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

        //创建一个新的订阅(是否要清除已有的未支付订阅呢？需要的，防止数据库被填满，由于Paypal的token可能还在生效，所以在onPay时要确保不能支付成功)
        Subscription::where('user_id', $user->id)->where('quantity', 0)->delete();
        $subscription = new Subscription();
        $subscription->user_id = $user->id;
        $subscription->plan = $plan->name;
        $subscription->payment_id = $queryArr['token'];
        $subscription->quantity = 0;
        $subscription->coupon_id = $coupon ? $coupon->id : null;
        $subscription->setup_fee = $plan->amount - $discount;
        $subscription->save();
        return redirect($approvalUrl);
    }

    /**
     * 获取帐单信息(JSON)
     */
    public function billings()
    {
        $user = Auth::user();
        $res = [];
        $subscriptions = Subscription::where('quantity', '>', 0)->where('user_id', $user->id)->get();
        $service = new PaypalService();
        $resItem = [];
        //没有返回交易记录，不知觉厉
        foreach($subscriptions as $item) {
            $transactions = $service->transactions($item->payment_id);
            if ($transactions == null)
                return [];
            foreach ($transactions as $t) {
                $amount = $t->getAmount();
                if ($amount == null)
                    continue;
                $carbon = new Carbon($t->getTimeStamp(), $t->getTimeZone());
                $carbon->tz = Carbon::now()->tz;
                
                $resItem["id"] = $t->getTransactionId();
                $resItem["plan"] = $item->plan;
                $resItem["amount"] =  $amount->getValue();
                $resItem["currency"] = $amount->getCurrency();
                $resItem["type"] = $t->getTransactionType();
                $resItem["startDate"] = $carbon->toDateTimeString();
                $resItem["endDate"] = strpos($item->plan, "monthly") > 0 ? $carbon->addMonth()->toDateTimeString() : $carbon->addYear()->toDateTimeString();
                $resItem["status"] = $t->getStatus();
                array_push($res, $resItem);   
            }
        }
        return $res;
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

    protected function switchPlan(User $user, Subscription $subscription)
    {
        $plan = Plan::where('name', $subscription->plan)->first();
        $role = $plan->role;
        $oldRoleName = $user->role['display_name'];
        $user->subscription_id = $subscription->id;
        $user->role_id = $role->id;
        $user->initUsageByRole($role);//更改计划时切换资源
        $user->save();
        Log::info($user->name . " change plan to " . $plan->name . "({$oldRoleName} -> {$role['display_name']})");
    }

    /**
     * 客户同意支付后的回调
     */
    public function onPay(Request $request)
    {
        echo "processing...don't close the window";
        $subscription = Subscription::where('payment_id', $request->token)->first();
        if (!($subscription instanceof Subscription)) {
            abort(401, "no subscription found");
        }

        $service = new PaypalService();

        $payment = $service->onPay($request);
        //中途取消的情况下返回profile页面
        if ($payment == null)return redirect('/app/profile?active=0');
            //abort(401, "error on payment");

        $subscription->payment_id = $payment->getId();
        $subscription->quantity = 1;
        $subscription->save();
        if ($subscription->coupon_id > 0) {
            $subscription->coupon->used++;
            $subscription->coupon->save();
        }
        $user = $subscription->user;
        //原来有订阅的情况下，应该取消订阅,这步可以推到队列中去做，暂时不处理
        // TODO:取消订阅功能应实现
        /* try { */
        /*     if ($user->subscription_id > 0) { */
        /*         $service->suspendSubscription($user->subscription->payment_id); */
        /*     } */
        /* } catch(\Exception $e) { */
        /*     Log::info("suspend failed:" . $user->subscription_id); */
        /* } */
        $this->switchPlan($user, $subscription);
        return redirect('/app/profile?active=0');
    }

    /**
     * 处理支付的一些通知
     */
    public function onPayWebhooks(Request $request)
    {
        Log::info('webhooks id: '.$request->id);
        $webhook_id = $request->id;//webhook id
        $count = Webhook::where('webhook_id',$webhook_id)->count();
        //Log::info('$select: '.$select);
        //Log::info('count($select): '.count($select));
        //$select = DB::select('select * from webhooks where webhook_id = :webhook_id',['webhook_id'=>$webhook_id]);
        if($count==0){
            $webhook = new Webhook;
            $webhook->webhook_id = $webhook_id;
            $webhook->create_time = $request->create_time;
            $webhook->resource_type = $request->resource_type;
            $webhook->event_type = $request->event_type;
            $webhook->summary = $request->summary;
            $webhook->webhook_content = serialize($request->resource);
            try {
                $re = $webhook->save();
                Log::info('$webhook->save(): '.$re);
            } catch(\Exception $e) {
                Log::error("save webhooks failed:" . $e->getMessage());
                return null;
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
		$storage = $this->getPayum()->getStorage(Payment::class);
		$payment = $storage->create();
		$payment->setNumber(uniqid());
		$payment->setCurrencyCode($plan->currency);
		$payment->setTotalAmount(0); 
		$payment->setDescription($plan->display_name);
		$payment->setClientId($user->id);
		$payment->setClientEmail($user->email);
        $payment->setDetails(new \ArrayObject([
            'amount' => $plan->amount * 100, 
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

        if ($status->getValue() == 'captured') {
            $detail = $payment->getDetails();
            $ourPayment = new OurPayment();
            $ourPayment->status = $status->getValue();
            $ourPayment->client_id = $payment->getClientId();
            $ourPayment->client_email = $payment->getClientEmail();
            $ourPayment->total_amount = $detail['amount'];
            $ourPayment->currency_code = ($payment->getCurrencyCode());
            $ourPayment->setNumber($payment->getNumber());
            $ourPayment->setDetails($detail);
            $ourPayment->save();

            $user = User::find($payment->getClientId());
            $planName = $detail['local']['customer']['plan'];
            $subscription = new Subscription();
            $subscription->payment_id = $ourPayment->id;
            $subscription->quantity = 1;
            $subscription->plan = $planName;
            $subscription->setup_fee = $detail['amount'] / 100;
            $subscription->user()->associate($user);
            $subscription->save();
            $this->switchPlan($user, $subscription);
        }
        return redirect('/app/profile?active=0');
        /* return \Response::json([ */
        /*     'status' => $status->getValue(), */
        /*     'order' => [ */
        /*         'total_amount' => $payment->getTotalAmount(), */
        /*         'currency_code' => $payment->getCurrencyCode(), */
        /*         'details' => $payment->getDetails() */
        /*     ] */
        /* ]); */
    }
}
