<?php
namespace App\Services;

use Illuminate\Support\Collection;
use App\Contracts\PaymentService as PaymentServiceContract;
use Log;
use App\Plan;
use App\Payment;
use App\Subscription;
use Payum\Stripe\Request\Api\CreatePlan;
use Stripe\Error;
use Stripe\Plan as StripePlan;
use Stripe\Stripe;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Cache;

class PaymentService implements PaymentServiceContract
{
    const LOG_DEBUG = 'LOG_DEBUG';
    const LOG_INFO = 'LOG_INFO';
    const LOG_ERROR = 'LOG_ERROR';
    protected $config;
    protected $logger;
    private $paypalService;

    public function __construct($config)
    {
        $this->config = $config;
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
        Stripe::setApiKey($this->config['stripe']['secret_key']);

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
    public function syncSubscriptions(Array $gateways = [], Subscription $subscription)
    {
        // 正常应该是从远程同步，以确定status状态，从本地同步是错误的方法
        // 特别是frequency_interval和frequency,它们的目的就是为了防止本地Plan修改后
        // 影响到已经完成的订阅，所以一定不能从本地Plan去同步。

        // Stripe的同步
        // TODO:

        // Paypal的同步
        if ($subscription) {
            $subs = new Collection([$subscription]);
        } else {
            $subs = Subscription::where(['gateway' => 'paypal'])->get();
        }
        $service = $this->getPaypalService();
        $this->log("sync to paypal, this may take long time...({$subs->count()})");
        foreach ($subs as $sub) {
            if (strlen($sub->agreement_id) < 3) {
                continue;
            }
            $this->log("handling {$sub->agreement_id}");
            $remoteSub = $service->subscription($sub->agreement_id);
            if (!$remoteSub) {
                $this->log($sub->agreement_id . " is not found");
                continue;
            }
            $plan = $remoteSub->getPlan();
            $def = $plan->getPaymentDefinitions()[0];

            /* $plan = Plan::where('name', $sub->plan)->first(); */
            /* if (!$plan) { */
            /*     $this->log("Plan {$sub->plan} is not found, warning", PaymentService::LOG_INFO); */
            /* } */

            $newData = [
                'frequency' => $def->getFrequency(),
                'frequency_interval' => $def->getFrequencyInterval()
            ];
            $state = $remoteSub->getState();
            $newStatus = '';
            switch (strtolower($state)) {
            case 'active':
                if ($sub->payments()->count() > 0)
                    $newStatus = Subscription::STATE_PAYED;
                else
                    $newStatus = Subscription::STATE_SUBSCRIBED;
                break;
            case 'cancelled':
                $newStatus = Subscription::STATE_CANCLED;
                break;
            case 'suspended':
                $newStatus = Subscription::STATE_SUSPENDED;
                break;
            }
            // 一个用户只能有一个激活的订阅，其他订阅应该设置被取消或挂起，目前采用取消操作
            if ($sub->user->subscription_id != $sub->id && strtolower($state) == 'active') {
                $this->log("{$sub->agreement_id} is not {$sub->user->email}'s active subscrition, now cancel it...", PaymentService::LOG_INFO);
                if ($service->cancelSubscription($sub->agreement_id))
                    $newStatus = Subscription::STATE_CANCLED;
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
            } else {
                $this->log("{$sub->agreement_id} has no change");
            }

            // 根据用户过期时间规划是否在指定时间同步该订阅的订单
            if ($sub->status == Subscription::STATE_PAYED)
                $this->autoScheduleSyncPayments($sub);
        }
        /* $this->log("sync to paypal, this will cost time, PLEASE WAITING..."); */
        /* foreach ($subs as $sub) { */
        /*     $remoteSub = $service->subscription($sub->agreement_id); */
        /* } */
    }

    /**
     * {@inheritDoc}
     */
    public function syncPayments(Array $gateways = [], Subscription $subscription)
    {
        // 目前只有Paypal需要同步支付记录, stripe是立即获取的
        $res = [];
        if ($subscription) {
            $subscriptions = [$subscription];
        } else {
            $subscriptions = Subscription::where('quantity', '>', 0)->where('gateway', 'paypal')->get();
        }
        $service = $this->getPaypalService();
        foreach($subscriptions as $item) {
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

                // 当状态变化时要更新订单
                if ($paypalStatus != $payment->status) {
                    $this->log("status change:{$payment->status} -> $paypalStatus", PaymentService::LOG_INFO);
                    $isDirty = true;
                    switch ($paypalStatus) {
                    default:
                    // 刚好我们的状态名称与Paypal一致，如果发现不一致需要一一转换
                    $payment->status = $paypalStatus;
                    }
                    if ($payment->status == Payment::STATE_COMPLETED) {
                        $this->log("handle payment...");
                        $this->handlePayment($payment);
                    }
                }
                if ($isDirty) {
                    $payment->save();
                    $this->log("payment {$payment->number} is synced");
                } else {
                    $this->log("payment {$payment->number} has no change", PaymentService::LOG_INFO);
                }

            }
        }
    }

    /**
     * 订阅处于支付状态时，发现有新的支付订单，有可能是循环扣款的新订单。
     * 做检查并设置过期时间。
     */
    protected function handleNextPayment(Payment $payment)
    {
        $endDate = new Carbon($payment->end_date);
        $user = $payment->client;
        if ($endDate->gt(new Carbon($user->expired))) {
            $this->log("{$user->email} has billing next payment, change his expired date({$payment->endDate}) > (" . $user->expired. ")", PaymentService::LOG_INFO);
            $user->expired  = $endDate->addDay();
            $user->save();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function handlePayment(Payment $payment)
    {
        $subscription = $payment->subscription;
        if ($subscription->status == Subscription::STATE_PAYED) {
            $this->log("You haved payed for the subscription, check if it's the next payment");
            return $this->handleNextPayment($payment);
        }

        if (Carbon::now()->gte(new Carbon($payment->end_date))) {
            Log::warning("the payment has expired, now: " . Carbon::now()->toDateTimeString() . ", end date:" . $payment->end_date );
            return;
        }

        // 添加payment记录和修改subscription状态
        $subscription->status = Subscription::STATE_PAYED;
        if ($subscription->coupon_id > 0) {
            $subscription->coupon->used++;
            $subscription->coupon->save();
        }
        $subscription->save();

        // 切换用户计划
        $user = $subscription->user;
        $plan = Plan::where('name', $subscription->plan)->first();
        $role = $plan->role;
        $oldRoleName = $user->role['display_name'];
        $user->subscription_id = $subscription->id;
        $user->role_id = $role->id;
        $user->initUsageByRole($role);//更改计划时切换资源
        switch (strtolower($plan->frequency)) {
        case 'day':
            $user->expired = Carbon::now()->addDays($plan->frequency_interval);
            break;
        case 'week':
            $user->expired = Carbon::now()->addWeeks($plan->frequency_interval);
            break;
        case 'month':
            $user->expired = Carbon::now()->addMonths($plan->frequency_interval);
            break;
        case 'year':
            $user->expired = Carbon::now()->addYears($plan->frequency_interval);
            break;
        }
        // 过期时间统一再加上一天，为了防止到期后，系统重置权限先于扣款，将引来不必要的麻烦。
        $user->expired->addDay(); 
        $user->save();
        Log::info($user->name . " change plan to " . $plan->name . "({$oldRoleName} -> {$role['display_name']})");
    }

    /**
     * {@inheritDoc}
     */
    public function requestRefund($number)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function refund($number)
    {
    }

    /**
     * 自动规划当用户到期时间快到时，对活动订阅的订单同步，以解决循环扣款不能及时检测到的问题。
     * 只要是正常操作，用户就不会过期。如果出现订阅正常扣款，但是用户过期的情况，由用户联系客户手动处理。
     * 不在此处考虑范围内。
     * @param Subscription $subscription
     */
    public function autoScheduleSyncPayments(Subscription $subscription)
    {
        if ($subscription->status != Subscription::STATE_PAYED || $subscription->id != $subscription->user->subscription_id)
            return;
        $key = "schedule-subscription-" . $subscription->id;
        if (Cache::has($key)) {
            $this->log("{$subscription->agreement_id} has scheduled, ignore");
            return;
        }
        $this->log("on schedule checking...");
        $user = $subscription->user;
        $carbon = new Carbon($user->expired);
        // 7天及以内过期的用户，在过期前几个小时检查订单状态
        if ($carbon->gt(Carbon::now()) && Carbon::now()->diffInDays($carbon, false) <= 7)  {
            $scheduleTime = $carbon->subHours(5);
            // 对于在5小时内就要过期的订单，1分钟后就立刻执行
            if ($scheduleTime->lt(Carbon::now()))
                $scheduleTime = Carbon::now()->addMinutes(1);
            $this->log("schedule {$subscription->agreement_id} at " . $scheduleTime->toDateTimeString(), PaymentService::LOG_INFO);
            dispatch((new \App\Jobs\SyncPaymentsJob($subscription))->delay($scheduleTime));
            Cache::put($key, $subscription->agreement_id, $scheduleTime);
        }
    }

    public function cancel(Subscription $subscription)
    {
        if ($subscription->gateway == PaymentService::GATEWAY_PAYPAL) {
            if (!in_array($subscription->status, [Subscription::STATE_SUBSCRIBED, Subscription::STATE_PAYED]))
                return false;
            $res = $this->getPaypalService()->cancelSubscription($subscription->agreement_id);
            if ($res) {
                $subscription->status = Subscription::STATE_CANCLED;    
                $subscription->save();
            }
            return $res;
        }
        return false;
    }
}
