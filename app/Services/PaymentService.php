<?php
namespace App\Services;

use Illuminate\Support\Collection;
use App\Contracts\PaymentService as PaymentServiceContract;
use Log;
use App\Plan;
use App\Subscription;
use Payum\Stripe\Request\Api\CreatePlan;
use Stripe\Error;
use Stripe\Plan as StripePlan;
use Stripe\Stripe;
use Illuminate\Console\Command;

class PaymentService implements PaymentServiceContract
{
    const LOG_INFO = 'LOG_INFO';
    const LOG_ERROR = 'LOG_ERROR';
    protected $config;
    protected $logger;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * 根据不同的记录器调用不同方法
     */
    public function log($msg, $level = PaymentService::LOG_INFO)
    {
        if ($this->logger instanceof Command) {
            switch ($level) {
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
    public function syncPayments(Array $gateways = [])
    {
        // 目前只有Paypal需要同步支付记录
        /* $res = []; */
        /* $subscriptions = Subscription::where('quantity', '>', 0)->where('user_id', $user->id)->get(); */
        /* $service = new PaypalService(); */
        /* $resItem = []; */
        /* //没有返回交易记录，不知觉厉 */
        /* foreach($subscriptions as $item) { */
        /*     $transactions = $service->transactions($item->agreement_id); */
        /*     if ($transactions == null) */
        /*         return []; */
        /*     foreach ($transactions as $t) { */
        /*         $amount = $t->getAmount(); */
        /*         if ($amount == null) */
        /*             continue; */
        /*         $carbon = new Carbon($t->getTimeStamp(), $t->getTimeZone()); */
        /*         $carbon->tz = Carbon::now()->tz; */
                
        /*         $resItem["id"] = $t->getTransactionId(); */
        /*         $resItem["plan"] = $item->plan; */
        /*         $resItem["amount"] =  $amount->getValue(); */
        /*         $resItem["currency"] = $amount->getCurrency(); */
        /*         $resItem["type"] = $t->getTransactionType(); */
        /*         $resItem["startDate"] = $carbon->toDateTimeString(); */
        /*         $resItem["endDate"] = strpos($item->plan, "monthly") > 0 ? $carbon->addMonth()->toDateTimeString() : $carbon->addYear()->toDateTimeString(); */
        /*         $resItem["status"] = $t->getStatus(); */
        /*         array_push($res, $resItem); */   
        /*     } */
        /* } */
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
