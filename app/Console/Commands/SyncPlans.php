<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Plan;
use Payum\Stripe\Request\Api\CreatePlan;
use Stripe\Error;
use Stripe\Plan as StripePlan;
use Stripe\Stripe;

class SyncPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bigbigads:sync-plans {--paypal : 只同步paypal} {--stripe : 只同步stripe}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '与Paypal,Stripe同步计划';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 对Payum分析，只提供了CreatePlan用于创建Plan；而其他方面并未提供，考虑过对Payum封装，但细想并无必要，这部分差异大，同时封装只是对接口做一层转接，反而觉得过度设计。
     *
     * @return mixed
     */
    public function handle()
    {
        $isAll = true;
        $onlyPaypal = $this->option('paypal');
        $onlyStripe = $this->option('stripe');
        if ($onlyPaypal || $onlyStripe)
            $isAll = true;
        /* $payum = \App::make('payum'); */
        $plans = Plan::all();

        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        foreach ($plans as $plan) {
            // 如果已经存在就对比，发现不一致就删除，再创建
            if ($isAll || $onlyStripe) {
                $planDesc = [
                    "amount" => $plan->amount,
                    "interval" => strtolower($plan->frequency),
                    'interval_count' => $plan->frequency_interval,
                    "name" => $plan->display_name,
                    "currency" => strtolower($plan->currency),
                    "id" => $plan->name
                ];

                $this->info("stripe plan:{$plan->name} is creating");
                try {
                    $old = StripePlan::retrieve($planDesc['id']);
                    if ($old) {
                        $dirty = false;
                        $oldArray = $old->__toArray(true);
                        foreach($planDesc as $key => $item) {
                            if ($oldArray[$key] != $item) {
                                $old->delete();
                                $this->comment("stripe plan已存在并且{$key}(new:{$item}, old:{$oldArray[$key]}不一致，删除");
                                $dirty = true;
                                break;
                            }
                        }
                        if (!$dirty) {
                            $this->comment("{$plan->name}已经创建过且无修改,忽略");
                            continue;
                        }
                    } 
                } catch(\Exception $e) {
                }
                StripePlan::create($planDesc);
                $this->info("{$plan->name} created for stripe");
            }

            if ($isAll || $onlyPaypal) {
                // TODO: Paypal待补充
            }
        }
    }
}
