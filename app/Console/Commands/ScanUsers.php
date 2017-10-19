<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Contracts\PaymentService;
use App\User;

class ScanUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bba:scan-users {email?} {--fix-info : 根据订阅信息修复用户的角色和过期时间}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '扫描所有用户或者指定用户，如果已经过期，就重置权限';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function check($user)
    {
        if (!$user->expired || $user->expired == '0000-00-00 00:00:00') {
            return;
        }
        $expired = new Carbon($user->expired);
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $fix = $this->option('fix-info');
        $email = $this->argument('email');
        if ($fix) {
            $this->comment("You enabled --fix-info option");
        }
        if ($email) {
            $user = User::where('email', $email)->first();
            if ($fix) {
                $user->fixInfoByPayments();
            }
            $user->resetIfExpired();
            $this->comment("only scan user: $email ". $user->isExpired());
        } else {
            $this->comment("start scan users...");
            //modify by chenxin 20171010
            //因为webhook不稳定，某些用户的订阅和交易订单没同步下来，这时候users表的expired字段是不准确。
            //如果简单粗暴地只是根据users表的expired字段来判断是否过期，就容易出现误判。
            //本次改造点：针对过期的用户，多执行一下同步订阅和订单，保证跟paypal远端一致后，再执行--fix-info选项，最后执行原来的业务逻辑
            $paymentService = app(\App\Contracts\PaymentService::class);
            $paymentService->setParameter(PaymentService::PARAMETER_FORCE, true);
            $paymentService->setLogger($this);
            //modify by chenxin 20171010    免费用户从一开始就不要select出来，这样能减少内存占用，因为免费用户太多了。
            foreach (User::where('role_id', '>', 3)->cursor() as $user) {
                if ($fix) {
                    $user->fixInfoByPayments();
                }
                //modify by chenxin 20171010
                if ($user->isExpired()){
                    $this->info("{$user->email} may be expired,sync paypal and check again");
                    $paymentService->syncSubscriptions([], $user->subscription);
                    $paymentService->syncPayments([], $user->subscription);
                    $user->fixInfoByPayments();
                    if ($user->resetIfExpired()){
                        $this->info("{$user->email} is really expired");
                    }
                }
            }
        }
    }
}
