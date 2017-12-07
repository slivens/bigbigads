<?php

use App\User;
use App\Role;

abstract class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    public function __construct()
    {
        $dotenv = new Dotenv\Dotenv(__DIR__ . '/../', '.' . getenv('APP_ENV') . '.env'); 
        $dotenv->load();
    }

    /**
     * 创建指定角色的用户
     */
    public function fakeUser($roleName = 'Free', $fields = [])
    {
        $user = factory(App\User::class)->create($fields);
        if (!($user instanceof User)) {
           $this->assertTrue(false);
           return; 
        }
        $role = Role::where('name', $roleName)->first();
        if (!($role instanceof Role)) {
           $this->assertTrue(false);
           return; 
        }
        $role->generateCache();
        $user->role()->associate($role);
        $user->setCachePolicies();
        $user->reInitUsage();
        return $user;
    }

    /**
     * 获取有效付款的一个用户
     *
     * @return App\User|null
     */
    public function getPayedUser()
    {
        foreach (Payment::where('status', Payment::STATE_COMPLETED)->where('created_at', '<', Carbon::now())->inRandomOrder()->cursor() as $payment) {
            if (!$payment->isEffective()) 
                continue;

            $user = $payment->client;
            if ($user->role_id != $user->getEffectiveSub()->getPlan()->role_id)
                continue;
            if ($user->expired != (new Carbon($payment->end_date))->addDay())
                continue;
            return $user;
        }
        return null;
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
}
