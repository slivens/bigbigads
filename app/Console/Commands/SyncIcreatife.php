<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PaymentService as PaymentService;
use App\Services\PaypalService as PaypalService;
use App\Payment;
use App\Subscription;
use Carbon\Carbon;
class SyncIcreatife extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'icreatife:sync {agreement-id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        echo "sync icreatife subscriptions ";
        $agreeId = $this->argument('agreement-id');
        $icreatife = [
            'paypal' => [
            'client_id' => 'ARM7eI7gBYYAvW-4R4nmcChvoMuc-DOvnRPv2AWHx2n9wCn24FSluG39LhXIemgd9Smxqwk6zQ2so1MT',
            'client_secret' => 'ECDrTkBlmY0YuGv2GNGaAhOxau9Cx9jIul1wpF4L66oE_fdWrkGVEa2Jm3kC1ZKKsGWiZc_b8GeCX5XX',
            'mode' =>'live'
            ],
            'stripe' => [
                'publishable_key' => 'pk_live_8nP9tRZZNWX7RmgX5XxBjvZQ',
                'secret_key' => 'sk_live_3DbT2xWCQqfk10vtRshKrIOU'
            ]
        ];
        $this->comment(" with ARM7 icreatife api");
        $this->comment("agrement id:" . $agreeId);
        $paymentsService = new PaymentService($icreatife);//内含同步代码 
        $paymentsService->setLogger($this);
        $paymentsService->setParameter(PaymentService::PARAMETER_FORCE, true);
        $paymentsService->setParameter(PaymentService::PARAMETER_TAGS, ['icreatife']);
        $paymentsService->syncSubscriptions([], $agreeId);
        $paymentsService->syncPayments([], $agreeId);
        /* if (empty($agreeId)) { */
        /*     $unSyncSubs = Subscription::where('agreement_id','like','I-%')->where('created_at','<','2017-06-07')->get();//返回的是多个订阅组合成的二维数组 */
        /* }else{ */
        /*     $unSyncSubs = Subscription::where('agreement_id', $agreeId)->first();//返回的是单个订阅的数组 */
        /*     $unSyncSubs = [$unSyncSubs];//再套一层变成二维数组 */
        /* } */
        /* $paymentsService = new PaymentService($icreatife);//内含同步代码 */ 
        /* $paypalService = new PaypalService($icreatife['paypal']);//内含获取订阅和交易列表的代码，用来验证 */
        /* $this->info("start syncing icreatife  subscriptions  with api head with 'ARM7...'"); */

        /* foreach ($unSyncSubs as $key => $unSyncSub) { */
        /*     $getSub = $paypalService->subscription($unSyncSub->agreement_id); */
        /*     //查不到的订阅，放弃 */
        /*     if (is_null($getSub)) { */
        /*         $this->info("$unSyncSub->agreement_id cannot be found on ARM7 api"); */
        /*          continue; */
        /*     } */
        /*     //返回的订阅id跟原始查询的id不一致，放弃 */
        /*     if ($getSub->getId() !== $unSyncSub->agreement_id) { */
        /*         //取回的订阅id与要更新的订阅id不一致，说明api用错，这个订阅不是这个api底下的 */
        /*         $this->info("The profile_id with $unSyncSub->agreement_id is not 'ARM7' icreatife api's subscription"); */
        /*         continue; */
        /*     } */
        /*     //同步订阅，只更新1个订阅 */
        /*     $this->info("sync  $unSyncSub->agreement_id 's subscription"); */
        /*     $paymentsService->syncSubscriptions([], $unSyncSub); */
        /*     $this->info('sleep 10s ...then sync payments'); */
        /*     sleep(10); */
        /*     //同步交易，有可能更新多个交易 */
        /*     $this->info("sync  $unSyncSub->agreement_id 's payments"); */
        /*     $paymentsService->syncPayments([],$unSyncSub); */
        /*     $this->info('sleep 10s ...then sync next or all done'); */
        /*     sleep(10); */
        /* } */
        /* $this->info("sync subscriptions and payments end"); */
    }
}
