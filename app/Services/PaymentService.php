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
    public function log($msg, $level = PaymentService::LOG_INFO)
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
                                $this->log("stripe plan已存在并且{$key}(new:{$item}, old:{$oldArray[$key]})不一致，删除", PaymentService::LOG_ERROR);
                                $dirty = true;
                                break;
                            }
                        }
                        if (!$dirty) {
                            $this->log("{$plan->name}已经创建过且无修改,忽略", PaymentService::LOG_ERROR);
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
                        $this->log("{$plan->name}已经创建过且无修改,忽略", PaymentService::LOG_ERROR);
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
    public function syncSubscriptions(Array $gateways = [])
    {
        // 正常应该是从远程同步，以确定status状态，这里先从本地同步以便流程可以正常往下走
        $subs = Subscription::all();//where('gateway', 'paypal')->get();
        $service = new PaypalService($this->config['paypal']);

        $this->log("sync to local");
        foreach ($subs as $sub) {
            $plan = Plan::where('name', $sub->plan)->first();
            if (!$plan) {
                $this->log("Plan {$sub->plan} is not found, warning", PaymentService::LOG_ERROR);
            }
            $sub->frequency = $plan->frequency;
            $sub->frequency_interval = $plan->frequency_interval;
            $sub->save();
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
                    $this->log("status change:{$payment->status} -> $paypalStatus", PaymentService::LOG_ERROR);
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
                    $this->log("payment {$payment->number} has no change", PaymentService::LOG_DEBUG);
                }

            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function handlePayment(Payment $payment)
    {
        $subscription = $payment->subscription;
        if ($subscription->status == Subscription::STATE_PAYED) {
            Log::info("You can't change user plan twice");
            return;
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

    public function refund($no)
    {
    }
}
