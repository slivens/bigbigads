<?php

namespace App\Services;

use App\Payment;
use App\Subscription;
use App\User;
use Carbon\Carbon;
use Session;

/**
 * 用户过期时间、权限升级等逻辑处理，单独从User的Model层剥离出来，放在这里
 */
class UserUpgradeService
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * 为了防止重复订购，需要判断用户是否存在Active的订阅，且处于延迟扣款情况
     * 是这种情况，则返回true
     */
    public function onFailedRecurringPayments() : bool
    {
        $subs = $this->user->subscriptions;
        if(!$subs){
            return false;
        }
        $result = false;
        $nowTime = Carbon::now();
        $longestEndDate = Carbon::createFromDate(1970,1,1);
        foreach ($subs as $sub) {
            if($sub->status != Subscription::STATE_PAYED) {
                continue;
            }
            $payments = $sub->payments;
            //把这个订阅的所有payment循环一下，得到最新的那个payment的过期时间
            foreach ($payments as $payment) {
                if ($payment->status != Payment::STATE_COMPLETED) {
                    continue;
                }
                $endDate = new Carbon($payment->end_date);
                //取得最长的过期时间
                if ($endDate->gt($longestEndDate)) {
                    $longestEndDate = $endDate;
                }
            }
            //建个新变量，存放11天后的日期，因为paypal规定，最多延迟10天，超过10天还扣款失败，会把订阅取消
            //How reattempts on failed recurring payments work
            //https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/reattempt_failed_payment/?mark=fail#how-reattempts-on-failed-recurring-payments-work
            $longestEndDatePlus = $longestEndDate;
            $longestEndDatePlus->addDay(11);
            //既要已经过期，又要加11天没过期
            if($longestEndDate->lt($nowTime) && $longestEndDatePlus->gt($nowTime)){
                return true;
            }
        }
        return $result;
    }
}
