<?php
namespace App\Services;

use Illuminate\Support\Collection;
use App\Contracts\PaymentService as PaymentServiceContract;
use Log;
use App\Plan;
use App\Role;
use App\Payment;
use App\Subscription;
use App\Refund;
use Payum\Stripe\Request\Api\CreatePlan;
use Stripe\Error;
use Stripe\Plan as StripePlan;
use Stripe\Stripe;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Cache;
use App\Jobs\SyncPaymentsJob;
use App\Notifications\RefundRequestNotification;
use App\Notifications\CancelSubOnSyncNotification;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Storage;

class PaymentService implements PaymentServiceContract
{
    const LOG_DEBUG = 'LOG_DEBUG';
    const LOG_INFO = 'LOG_INFO';
    const LOG_ERROR = 'LOG_ERROR';
    protected $config;
    protected $logger;
    private $paypalService;

    /**
     * 参数影响支付系统的行为
     * force: true强制与远程同步;false按优化情况与远程同步
     */
    private $parameters;

    public function __construct($config)
    {
        $this->config = $config;
        $this->parameters = new Collection();
        Stripe::setApiKey($this->config['stripe']['secret_key']);
        $this->setParameter(PaymentService::PARAMETER_TAGS, ['default']);
        /* $this->setParameter(PaymentService::PARAMETER_SYNC_RANGE, ['start' => '2017-06-07 14:15:12', 'end' => null]); */
    }

    public function setParameter($key, $val)
    {
        $this->parameters[$key] = $val;
    }

    public function getParameter($key)
    {
        if ($this->parameters->has($key))
            return $this->parameters[$key];
        return null;
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }
    
    protected function getPaypalService()
    {
        if (!$this->paypalService) {
            $this->paypalService = new PaypalService($this->config['paypal']);
        }
        return $this->paypalService;
    }

    public function getRawService($gateway)
    {
        if ($gateway == PaymentService::GATEWAY_PAYPAL)
            return $this->getPaypalService();
        return false;
    }



    /**
     * 根据不同的记录器调用不同方法
     */
    public function log($msg, $level = PaymentService::LOG_DEBUG)
    {
        if ($this->logger instanceof Command) {
            switch ($level) {
            case PaymentService::LOG_DEBUG:
                $this->logger->comment($msg);
                break;
            case PaymentService::LOG_INFO:
                $this->logger->info($msg);
                break;
            case PaymentService::LOG_ERROR:
                $this->logger->error($msg);
                    break;
            }
        } else {
            switch ($level) { 
            case  PaymentService::LOG_INFO:
                Log::info($msg);
                break;
            case  PaymentService::LOG_ERROR:
                Log::error($msg);
                break;
            }

        }
    }

    /**
     * {@inheritDoc}
     * @remark 同步计划没有考虑到试用期, 建立费用，延迟时间
     */
    public function syncPlans(Array $gateways)
    {
        $plans = Plan::all();
        $isAll = true;
        $gateways = new Collection($gateways);
        if (!$gateways->isEmpty())
            $isAll = false;

        if ($isAll || $gateways->contains(PaymentService::GATEWAY_STRIPE)) {
            foreach ($plans as $plan) {
                // 如果已经存在就对比，发现不一致就删除，再创建
                $planDesc = [
                    "amount" => $plan->amount * 100,
                    "interval" => strtolower($plan->frequency),
                    'interval_count' => $plan->frequency_interval,
                    "name" => $plan->display_name,
                    "currency" => strtolower($plan->currency),
                    "id" => $plan->name
                ];

                // 有没有更好的机制可以输出更详细的信息？
                $this->log("stripe plan:{$plan->name} is creating");
                try {
                    $old = StripePlan::retrieve($planDesc['id']);
                    if ($old) {
                        $dirty = false;
                        $oldArray = $old->__toArray(true);
                        foreach($planDesc as $key => $item) {
                            if ($oldArray[$key] != $item) {
                                $old->delete();
                                $this->log("stripe plan已存在并且{$key}(new:{$item}, old:{$oldArray[$key]})不一致，删除", PaymentService::LOG_INFO);
                                $dirty = true;
                                break;
                            }
                        }
                        if (!$dirty) {
                            $this->log("{$plan->name}已经创建过且无修改,忽略");
                            continue;
                        }
                    } 
                } catch(\Exception $e) {
                }
                StripePlan::create($planDesc);
                $this->log("{$plan->name} created for stripe");
            }

        }


        if ($isAll || $gateways->contains(PaymentService::GATEWAY_PAYPAL)) {
            $this->log("sync to paypal, this will cost time, PLEASE WAITING...");
            $service = new PaypalService($this->config['paypal']);                                         
            $plans->each(function($plan, $key) use($service){
                $this->log("Plan {$plan->name} is creating");
                //Paypal如果要建立TRIAL用户，过程比较繁琐，这里直接跳过
                if ($plan->amount == 0) {
                    $this->log("Plan {$plan->name}: ignore free plan in paypal");
                    return;
                }
                $paypalPlan = null;
                if ($plan->paypal_id)
                    $paypalPlan = $service->getPlan($plan->paypal_id);
                if ($paypalPlan) {
                    $merchantPreference = $paypalPlan->getMerchantPreferences();
                    $paymentDef = $paypalPlan->getPaymentDefinitions();
                    $paymentDef = $paymentDef[0];
                    $money = $paymentDef->getAmount();
                    $paypalPlanDesc = [
                        'name' => $paypalPlan->getName(),
                        'display_name' => $paypalPlan->getDescription(),
                        'amount' => $money->getValue(),
                        'currency' => $money->getCurrency(),
                        'frequency' => $paymentDef->getFrequency(),
                        'frequency_interval' => $paymentDef->getFrequencyInterval()
                    ];
                    $isDirty = false;
                    foreach ($paypalPlanDesc as $key => $val) {
                        if (strtolower($plan[$key]) != strtolower($val)) {
                            $this->log("remote paypal diff with local ({$key}):{$plan[$key]} $val");
                            $service->deletePlan($plan->paypal_id);
                            $isDirty = true;
                            break;
                        }
                    }
                    if (!$isDirty) {
                        $this->log("{$plan->name}已经创建过且无修改,忽略");
                        return;
                    }
                }
                $output = $service->createPlan($plan);
                $plan->paypal_id = $output->getId();
                $plan->save();
                $this->log("{$plan->name} created for paypal");
            }); 
            $this->log("paypal sync done");
        }
    }


    /**
     * {@inheritDoc}
     * @todo 同步远程的Paypal, stripe
     */
    public function syncSubscriptions(Array $gateways = [], $subscription)
    {
        // 正常应该是从远程同步，以确定status状态，从本地同步是错误的方法
        // 特别是frequency_interval和frequency,它们的目的就是为了防止本地Plan修改后
        // 影响到已经完成的订阅，所以一定不能从本地Plan去同步。

        // Stripe的同步
        // TODO:

        // Paypal的同步
        $tags = $this->getParameter(PaymentService::PARAMETER_TAGS);
        if ($subscription) {
            if (is_array($subscription) || $subscription instanceof Collection)
                $subs = $subscription;
            else
                $subs = new Collection([$subscription]);
            $force = true;
        } else {
            $subs = Subscription::where('gateway', 'paypal')->where('status', '<>', Subscription::STATE_CREATED)->where('status', '<>', '')->whereIn('tag', $tags)->get();
            $force = $this->getParameter(PaymentService::PARAMETER_FORCE);
        }
        $paypalService = $this->getPaypalService();
        $this->log("sync to paypal, this may take long time...({$subs->count()})");
        foreach ($subs as $sub) {
            if (!in_array($sub->tag, $tags)) {
                $this->log("{$sub->agreement_id} tag is {$sub->tag}, not in tags, skip");
                continue;
            }
            if (strlen($sub->agreement_id) < 3) {
                continue;
            }
            if ($sub->status == Subscription::STATE_CANCLED && !$force) {
                $this->log("skip cancelled subscription {$sub->agreement_id}");
                $this->checkSubscription($sub);
                continue;
            }
            $this->log("handling {$sub->agreement_id}");
            $remoteSub = $paypalService->subscription($sub->agreement_id);
            if (!$remoteSub) {
                $this->log($sub->agreement_id . " is not found");
                continue;
            }
            $plan = $remoteSub->getPlan();
            $def = $plan->getPaymentDefinitions()[0];

            $detail = $remoteSub->getAgreementDetails();
            $payer = $remoteSub->getPayer();
            $info = $payer->getPayerInfo();
            $nextBillingDate = $detail->getNextBillingDate();
            $newData = [
                'frequency' => $def->getFrequency(),
                'frequency_interval' => $def->getFrequencyInterval(),
                'remote_status' => $remoteSub->getState(),
                'buyer_email' => $info->getEmail(),
                'next_billing_date' => $nextBillingDate ? new Carbon($nextBillingDate) : null
            ];
            $state = $remoteSub->getState();
            $newStatus = '';
            switch (strtolower($state)) {
            case 'pending':
                $newStatus = Subscription::STATE_PENDING;
                break;
            case 'active':
                if ($sub->payments()->count() > 0)
                    $newStatus = Subscription::STATE_PAYED;
                else
                    $newStatus = Subscription::STATE_SUBSCRIBED;
                break;
            case 'cancelled':
                $newStatus = Subscription::STATE_CANCLED;
                //当订阅退订时获取其退订时间
                $cancel_time = $this->getCancaledTime($sub->agreement_id);
                if($cancel_time){
                    $newData['canceled_at'] = $cancel_time;
                }else{
                    $this->log("cannot get cancal time on {$sub->agreement_id},but subscription was cancelled", PaymentService::LOG_INFO);
                }
                break;
            case 'suspended':
                $newStatus = Subscription::STATE_SUSPENDED;
                break;
            }
            // 一个用户只能有一个激活的订阅，其他订阅应该设置被取消或挂起，采用通知操作，由管理员确认后手动取消。
            if ($sub->user->subscription_id != $sub->id && strtolower($state) == 'active') {
                $this->log("{$sub->agreement_id} is not {$sub->user->email}'s active subscrition, now send email to notify admin to cancel it", PaymentService::LOG_INFO);
                /* if ($paypalService->suspendSubscription($sub->agreement_id)) */
                /*     $newStatus = Subscription::STATE_SUSPENDED; */
                $sub->user->notify(new CancelSubOnSyncNotification($sub));
            }
            if (!empty($newStatus)) {
                $newData['status'] = $newStatus;
            }
            $isDirty = false;
            foreach ($newData as $key => $val) {
                if ($sub[$key] != $val) {
                    $this->log("{$key}: old {$sub[$key]}, new: $val", PaymentService::LOG_INFO);
                    $isDirty = true;
                    $sub[$key] = $val;
                }
            }
            if ($isDirty) {
                $sub->save();
                $this->checkSubscription($sub);
            } else {
                $this->log("{$sub->agreement_id} has no change");
            }

        }
        /* $this->log("sync to paypal, this will cost time, PLEASE WAITING..."); */
        /* foreach ($subs as $sub) { */
        /*     $remoteSub = $paypalService->subscription($sub->agreement_id); */
        /* } */
    }

    /* /** */
    /*  * 只同步单个Payment */
    /*  *1/ */
    /* public function syncOnePayment($payment) */
    /* { */
    /* } */

    /**
     * {@inheritDoc}
     */
    public function syncPayments(Array $gateways = [], $subscription = null)
    {
        // 目前只有Paypal需要同步支付记录, stripe是立即获取的
        $res = [];
        $tags = $this->getParameter(PaymentService::PARAMETER_TAGS);
        if (is_array($subscription)|| $subscription instanceof Collection) {
            $subscriptions = $subscription;
            $force = true;
        } else if ($subscription instanceof Subscription) {
            $subscriptions = [$subscription];
            $force = true;
        } else {
            $subscriptions = Subscription::where('quantity', '>', 0)->where('gateway', 'paypal')->whereIn('tag', $tags)->get();
            $force = $this->getParameter(PaymentService::PARAMETER_FORCE);
        }
        $service = $this->getPaypalService();
        foreach($subscriptions as $item) {
            if (!in_array($item->tag, $tags)) {
                $this->log("{$item->agreement_id} tag is {$item->tag}, not in tags, skip");
                continue;
            }
            // 未完成订阅直接忽略
            if ($item->status == Subscription::STATE_CREATED)
                continue;
            if ($item->status == Subscription::STATE_CANCLED && !$force) {
                $this->log("skip cancelled subscription {$item->agreement_id}");
                continue;
            }
            $this->log("sync payments from paypal agreement:"  . $item->agreement_id);
            $transactions = $service->transactions($item->agreement_id);
            if ($transactions == null)
                continue;
            foreach ($transactions as $t) {
                $amount = $t->getAmount();
                if ($amount == null)
                    continue;

                $carbon = new Carbon($t->getTimeStamp(), $t->getTimeZone());
                $carbon->tz = Carbon::now()->tz;

                $isDirty = false;
                $paypalStatus = strtolower($t->getStatus());
                $payment = Payment::firstOrNew(['number' => $t->getTransactionId()]);
                $payment->number = $t->getTransactionId();
                $payment->description = $item->plan;
                $payment->client_id = $item->user->id;
                $payment->client_email = $item->user->email;
                $payment->amount = $amount->getValue();
                $payment->currency = $amount->getCurrency();
                $payment->subscription()->associate($item);
                $payment->details = $t->toJSON();
                $payment->created_at = $carbon;

                // TODO: 该代码主要解决早期buyer_email为空的问题，应该直接赋值，移除判断
                if (empty($payment->buyer_email)) {
                    $payment->buyer_email = $t->getPayerEmail();
                    $isDirty = true;
                } else {
                    $payment->buyer_email = $t->getPayerEmail();
                }
                // 当状态变化时要更新订单
                if ($paypalStatus != $payment->status) {
                    $this->log("status will change:{$payment->status} -> $paypalStatus", PaymentService::LOG_INFO);
                        
                    $isDirty = true;
                    switch ($paypalStatus) {
                    case 'completed':
                        if ($payment->refund && $payment->refund->isRefunding()) {
                            $payment->status = Payment::STATE_REFUNDING;
                            $this->log("the payment is refunding, change to `refunding` instead of `completed`", PaymentService::LOG_INFO);
                        } else {
                            $payment->status = Payment::STATE_COMPLETED;
                        }
                        break;
                            
                    default:
                    // 刚好我们的状态名称与Paypal一致，如果发现不一致需要一一转换
                    $payment->status = $paypalStatus;
                    }
                    if ($payment->status == Payment::STATE_COMPLETED) {
                        $this->log("handle payment...");
                    }
                }

                if ($isDirty) {
                    $payment->save();
                    $this->handlePayment($payment);
                    $this->log("payment {$payment->number} is synced");
                } else {
                    $this->log("payment {$payment->number} has no change", PaymentService::LOG_INFO);
                }

                // 补全退款申请单和根据退款状态处理用户状态
                if ($payment->status == Payment::STATE_REFUNDED) {
                    $refund = $payment->refund;
                    if (!$refund) {
                        $refund = new Refund();
                        $refund->amount = $payment->amount;
                        $refund->note = "auto synced refunds";
                        $refund->status = Refund::STATE_ACCEPTED;
                        $refund->payment_id = $payment->id;//payment()->associate($payment);
                        $res = $refund->save();
                        $this->log("generate refund automatically:$res", PaymentService::LOG_INFO);
                    }
                    if ($refund->status != Refund::STATE_ACCEPTED) {
                        $refund->status = Refund::STATE_ACCEPTED;
                        $refund->save();
                    }
                    $this->handleRefundedPayment($payment);
                }
            }

            // 根据用户过期时间规划是否在指定时间同步该订阅的订单
            if ($item->status == Subscription::STATE_PAYED)
                $this->autoScheduleSyncPayments($item);
        }
    }

    /**
     * 同步用户的订阅与支付订单、
     * @param $users 用户列表
     */
    public function syncUsers(Array $users = [])
    {
        
    }

    /**
     * 对于退款支付订单满足以下条件，则订阅会被取消：
     * 1. 退款订意属于活动订阅
     * 2. 活动订阅没有其他有效订单
     * 用户权限切换到Free。
     * @remark 如果先取消订阅再发起退款呢？这种情况不能忽略。否则会出现用户取消订阅了，钱也退了，但是权限还在。
     */
    public function handleRefundedPayment(Payment $payment)
    {
        $user = $payment->subscription->user;
        if ($payment->status != Payment::STATE_REFUNDED)
            return false;
        if (!$payment->subscription->isActive())
            return false;
        if ($payment->subscription->hasEffectivePayment())
            return false;
        $this->log("reset user {$user->email} to Free because of refund:{$payment->number}", PaymentService::LOG_INFO);
        $this->cancel($payment->subscription);
        $user->fixInfoByPayments();
        /* $user->role()->associate(Role::where('name', 'Free')->first()); */
        /* $user->save(); */
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function handlePayment(Payment $payment)
    {
        $subscription = $payment->subscription;
        if ($subscription->status == Subscription::STATE_PAYED) {
            $this->log("You haved payed for the subscription, check if it's the next payment");
            return $subscription->user->fixInfoByPayments();
        }

        // 添加payment记录和修改subscription状态
        $subscription->status = Subscription::STATE_PAYED;
        if ($subscription->coupon_id > 0) {
            $subscription->coupon->used++;
            $subscription->coupon->save();
        }
        $subscription->save();

        // 切换用户计划
        $subscription->user->fixInfoByPayments();
    }



    /**
     * 自动规划当用户到期时间快到时，对活动订阅的订单同步，以解决循环扣款不能及时检测到的问题。
     * 只要是正常操作，用户就不会过期。如果出现订阅正常扣款，但是用户过期的情况，由用户联系客户手动处理。
     * 不在此处考虑范围内。
     * @param Subscription $subscription
     */
    public function autoScheduleSyncPayments(Subscription $subscription)
    {
        $user = $subscription->user;
        if ($subscription->status != Subscription::STATE_PAYED || $subscription->id != $user->subscription_id)
            return;
        if ($user->inWhitelist()) {
            return;
        }
        $key = "schedule-subscription-" . $subscription->id;
        if (Cache::has($key)) {
            $this->log("{$subscription->agreement_id} has scheduled, ignore");
            return;
        }
        $this->log("on schedule checking...");
        $carbon = new Carbon($user->expired);
        // 7天及以内过期的用户，在过期前几个小时检查订单状态
        if ($carbon->gt(Carbon::now()) && Carbon::now()->diffInDays($carbon, false) <= 10)  {
            $scheduleTime = $carbon->subHours(5);
            // 对于在5小时内就要过期的订单，1分钟后就立刻执行
            if ($scheduleTime->lt(Carbon::now()))
                $scheduleTime = Carbon::now()->addMinutes(1);
            $this->log("schedule {$subscription->agreement_id} at " . $scheduleTime->toDateTimeString(), PaymentService::LOG_INFO);
            dispatch((new \App\Jobs\SyncPaymentsJob($subscription))->delay($scheduleTime));
            Cache::put($key, $subscription->agreement_id, $scheduleTime);// 自动过期
        }
    }

    /**
     * 检查订阅是否符合系统设计
     * 当订阅取消时，如果是活动订阅，则当前用户的活动订阅应该清空
     */
    private function checkSubscription($subscription)
    {
        if ($subscription->status == Subscription::STATE_CANCLED)  {
            // 对于活动订阅，解除用户的当前订阅
            if ($subscription->isActive() && !$subscription->hasEffectivePayment()) {
                $this->log("{$subscription->user->email}'s subscription set to null", PaymentService::LOG_INFO);
                $user = $subscription->user;
                $user->subscription_id = null;
                $user->save();
            }
        }
    }

    /**
     * @{inheritDoc}
     */
    public function cancel(Subscription $subscription)
    {
        if ($subscription->status == Subscription::STATE_CANCLED)  {
            return true;
        }
        if (!$subscription->canCancel())
            return false;
        $isOk = false;
        if ($subscription->gateway == PaymentService::GATEWAY_STRIPE) {
            $stripeSub = \Stripe\Subscription::retrieve($subscription->agreement_id);
            $res = $stripeSub->cancel();
            if ($res["status"] == \Stripe\Subscription::STATUS_CANCELED) {
                $isOk = true;
            }
        }

        if ($subscription->gateway == PaymentService::GATEWAY_PAYPAL) {
            $res = $this->getPaypalService()->cancelSubscription($subscription->agreement_id);
            if ($res) {
                $isOk = true;
            }
            $this->syncSubscriptions([], $subscription);
        }

        if ($isOk) {
            $subscription->status = Subscription::STATE_CANCLED;
            $subscription->save();

            $this->checkSubscription($subscription);
        }
        return $isOk;
    }

    /**
     * {@inheritDoc}
     */
    public function requestRefund(Payment $payment, $amount = 0)
    {
        if ($payment->status != Payment::STATE_COMPLETED)
            return false;
        if (!$amount)
            $amount = $payment->amount;
        // 状态转移由Model监听完成
        $refund = new Refund();
        $refund->amount = $payment->amount;
        $refund->note = "";
        $refund->status = Refund::STATE_CREATED;
        $refund->payment()->associate($payment);
        $refund->save();
        Log::info("{$payment->number} is on refunding");
        $refund->payment->client->notify(new RefundRequestNotification($refund));
        return $refund;
    }

    /**
     * @{inheritDoc}
     */
    public function refund(\App\Refund $refund)
    {
        $amount = $refund->amount;
        $payment = $refund->payment;
        // TODO:stripe情况
        $this->log("handle refunding {$payment->number}...");
        $paypalService = $this->getPaypalService();
        if ($payment->subscription->gateway == PaymentService::GATEWAY_PAYPAL) {
            // 退款成功后，应该同步该订单的状态，同时及时修正用户权限
            $paypalRefund= $paypalService->refund($payment->number, $payment->currency, $amount);
            if (!$paypalRefund)
                return false;
            switch (strtolower($paypalRefund->getState())) {
            case 'pending':
                $refund->status = Refund::STATE_PENDING;
               break;
            case 'completed':
                $refund->status = Refund::STATE_ACCEPTED;
                break;
            case 'canceled':
            case 'failed':
                // 退款失败，状态不变
                return false;
            }
            $refund->save();
            // 退款成功后，立刻与服务器同步订单状态，确保订单是处于订款状态
            $this->syncPayments([], $payment->subscription);
            /* $this->handleRefundedPayment($payment); */
            // 当处于pending时，10秒后同步防止钱退了，没将用户权限切回去
            // 或者退完成了，但是在handleRefundedPayment取消用户订阅时跟Paypal通信出错
            dispatch((new SyncPaymentsJob($payment->subscription))->delay(Carbon::now()->addSeconds(10)));
            return true;
        }
        $this->log("refund failed");
        return false;    
    }

    /**
     * 获取订阅的退订时间
     * 使用订阅id获取交易列表，取退订的时间，转换时区后返回
     * @param string $agreement_id 订阅Id,I-XXX
     * @return datetime $time 退订时间，经过时区转换过的
     */
    public function getCancaledTime($agreement_id){
        if(strpos($agreement_id,'I-') != 0){
            $this->log("unknown param on getCancelTime");
            return false;
        }
        $service = $this->getPaypalService();
        $transactions = $service->transactions($agreement_id);
        if(!$transactions || empty($transactions)){
            $this->log("cannot find transaction list with $agreement_id on getCancelTime");
            return false;
        }
        $cancel_arr = end($transactions);
        if($cancel_arr->getStatus() != 'Canceled'){
            $this->log("$agreement_id is not a canceled subscription");
            return false;
        }
        return Carbon::parse($cancel_arr->getTimeStamp())->timezone('Asia/Shanghai')->toDateTimeString();

        
    }

    /**
    * 生成票据
    * 字段值及定义：
    * Reference ID:自定义票据id，作为invoice_id存入payments表作为下载传参，来源:计算获取---> ceil(microtime(true) * 100).mt_rand(1000, 9999)
    * Amount of payment: payments.amount
    * Date of payment:payments.details中解析timestamp,然后自定义格式转换，看情况改时区
    * Payment account:payments.details中解析payer_email
    * Package:plans.display_name
    * Expiration time:payments.details中解析timestamp,然后依据package计算过期时间
    * Method:Paypal或者Stripe,stripe的程序要另外写或者后期补上
    * Name:users.name
    * Email:payments.client_email
    */
    public function generateInvoice($transaction_id, $force = false)
    {
        //默认不强制生成
        if(strlen($transaction_id) != 17 || empty($transaction_id)) {
            $re_str1 = "unknown param on generateInvoice";
            $this->log($re_str1, PaymentService::LOG_INFO);
            return $re_str1;
        }
        $payment = Payment::select('payments.amount', 'payments.details', 'payments.client_email', 'payments.invoice_id', 'plans.display_name', 'users.name')
            ->leftJoin('subscriptions', 'subscriptions.id', '=', 'payments.subscription_id')
            ->leftJoin('plans', 'subscriptions.plan', '=', 'plans.name')
            ->leftJoin('users', 'payments.client_id', '=', 'users.id')
            ->where('payments.number', $transaction_id)
            ->where('payments.status', 'completed')
            ->first();
        if(!$payment) {
            $re_str2 = "payment is invalid on use transaction id: $transaction_id,maybe status is not completed";
            $this->log($re_str2, PaymentService::LOG_INFO);
            return $re_str2;
        }
        if(!empty($payment->invoice_id) && !$force) {
            //该交易的invoice_id不为空，则已经生成过
            $re_str3 = "cannot generate this invoice with transaction id: $transaction_id because it was generated.";
            $this->log($re_str3, PaymentService::LOG_INFO);
            return $re_str3;
        }

        //$this->log($payment->details, PaymentService::LOG_INFO);
        $data = (object)array();
        $data->reference_id = ceil(microtime(true) * 100) . mt_rand(1000, 9999);//reference ID
        $data->amount = '$ ' . $payment->amount;//amount
        $data->package = $payment->display_name;//package
        $data->name = $payment->name;//name
        $data->email = $payment->client_email;//email
        $data->method = 'Paypal';

        $details = json_decode($payment->details);
        //var_dump($details);

        $data->payment_account = $details->payer_email;//payment account

        $time = strtotime($details->time_stamp);
        //目标时间格式 1 September 2017 at 5:16:04 p.m. HKT
        $year_and_month = date("j F Y", $time);
        $day = date("g:i:s", $time);
        $ampm = date("a", $time);
        if($ampm == 'am') {
            $ampm = 'a.m.';
        } else {
            $ampm = 'p.m.';
        }
        $data->date = $year_and_month . ' at ' . $day . ' ' . $ampm . ' HKT';//date of payment

        switch($data->package) {
            case 'Annual':
                $months = 12;
                break;
            case 'Quarterly':
                $months = 3;
                break;
            case 'Monthly':
                default:
                $months = 1;
                break;
        }
        //交易服务过期时间，格式 19 Sep 2017
        $data->expiration_time = Carbon::parse($details->time_stamp)->addMonths($months)->format('j M Y');//expiration time
        //渲染html,转换成pdf
        $dompdf = new DOMPDF(); //if you use namespaces you may use new \DOMPDF()
        $dompdf->loadHtml($this->getInvoicePage($data));
        $dompdf->render();

        //存储路径storage/app/public,可以通过public/storage访问
        Storage::put("$data->reference_id.pdf", $dompdf->output());

        //保存invoice_id到payments
        $re_payment = Payment::where('number', $transaction_id)->first();
        $re_payment->invoice_id = $data->reference_id;
        $re_payment->save();
        $re_str4 = "$transaction_id 's payment invoice was generated,reference id is $data->reference_id";
        $this->log($re_str4, PaymentService::LOG_INFO);
        return $re_str4;
    }

    /**
    * 调用视图并完成渲染，返回视图内容
    *
    * @param Array $invoice_data payments相关信息，用于视图中的内容赋值
    * @return Object 视图内容
    */
    public function getInvoicePage($invoice_data)
    {
        $view = view('subscriptions.invoice')->with('data', $invoice_data);
        return response($view)->getContent();//返回视图内容
    }

    /**
    * 票据下载方法
    *
    * @param bigint $invoice_id 票据id,每个payment记录有一个，如果没有需要执行方法生成
    * @return void 输出header，直接下载pdf,非打开
    */
    public function downloadInvoice($invoice_id)
    {
        $file_name = $invoice_id . '.pdf';
        if(Storage::exists($file_name)) {
            //找到文件
            $this->log("download invoice file ,file name is $file_name", PaymentService::LOG_INFO);
            $contents = Storage::get($file_name);
            header('Content-type: application/pdf');
            header('Content-Disposition: attachment; filename=' . $file_name);
            echo $contents;
        } else {
            //不存在这个文件,因为这里传参只是票据id，如果不存在，无法对应到任何一个交易，所以没办法调用命令去生成再下载
            $this->log("download invoice file but file is not exists,file name is $file_name", PaymentService::LOG_INFO);
            return 'cannot find file';
        }
    }




}
