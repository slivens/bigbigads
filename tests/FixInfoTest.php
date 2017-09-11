<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Subscription;
use App\User;
use App\Role;
use Carbon\Carbon;
use App\Payment;

/**
 * 修复信息的测试,测试之前应保证系统中至少有正常付费的用户。
 * 完成以下测试：
 * - 没有购买订阅的白名单用户，角色设置为Pro，过期时间设置为当前时间+1年，同步后预期结果为不受影响；
 * - 没有购买订阅的非白名单用户，角色设置为Pro，过期时间设置为当前时间+1年，同步后预期结果为该用角色被重置为Free,过期时间为null或者0000-00-00 00:00:00;
 * - 购买standard订阅的用户，角色与过期时间都正确，同步后预期结果为无变化（updated_at不更新)(OK)
 * - 购买standard订阅的用户，角色为standard, expired为null，同步后预期结果为expired不为null;(OK)
 * - 购买standard月付订阅的用户，expired预期值应为2017-09-01,实际为2017-12-01, 同步后expired被修正为预期值；(OK)
 * - 购买standard订阅的用户，角色为free, expired为null, 同步后角色为standard, expired不为null;(OK)
 * - 购买standard订阅的用户，角色为Pro, expired不为null，同步后角色为standard, expired修正为正常值;(OK)
 */
class FixInfoTest extends TestCase
{
    use DatabaseTransactions;

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
     * 正常用户测试
     * 前置条件：数据库中存在有效订单的用户
     */
    public function testNormal()
    {
        $user = $this->getPayedUser();
        $this->assertTrue($user instanceof User);
        $old = $user->toArray();
        $user->fixInfoByPayments();
        $this->assertEquals($old, $user->toArray());
        return $user;
    }

    /**
     * 测试没有过期时间以及过期时间错误的问题
     * @depends testNormal
     */
    public function testWrongExpired(User $user)
    {
        $expired = $user->expired;
        $user->expired = null;
        $this->assertNotEquals($expired, $user->expired);
        $user->fixInfoByPayments();
        $this->assertEquals($expired, $user->expired);

        $user->expired = (new Carbon($expired))->addYear();
        $this->assertNotEquals($expired, $user->expired);
        $user->fixInfoByPayments();
        $this->assertEquals($expired, $user->expired);
        return $user;
    }

    /**
     * 测试错误角色的问题
     * @depends testWrongExpired
     */
    public function testWrongRole(User $user)
    {
        $roleId = $user->role_id;
        $user->role()->associate(Role::where('name', 'Free')->first());
        $this->assertNotEquals($roleId, $user->role_id);
        $user->fixInfoByPayments();
        $this->assertEquals($roleId, $user->role_id);

        $standardRole = Role::where('name', 'Standard')->first();
        if ($roleId != $standardRole->id) {
            $user->role()->associate($standardRole);
            $this->assertNotEquals($roleId, $user->role_id);
            $user->fixInfoByPayments();
            $this->assertEquals($roleId, $user->role_id);
        }
        $proRole = Role::where('name', 'Pro')->first();
        if ($roleId != $proRole->id) {
            $user->role()->associate($proRole);
            $this->assertNotEquals($roleId, $user->role_id);
            $user->fixInfoByPayments();
            $this->assertEquals($roleId, $user->role_id);
        }

    }

    /**
     * 白名单测试
     * 前置条件：创建一个白名单用户，角色为Pro,过期时间为当前时间+1年
     * 执行测试：修复该用户的用户信息
     * 执行结果：用户信息所有字段无改变
     */
    public function testWhitelist()
    {
        $faker = app('Faker\Generator');
        $user = factory(\App\User::class)->create([
            'tag' => User::TAG_WHITELIST,
            'role_id' => Role::where('name', 'Pro')->first()->id,
            'expired' => Carbon::now()->addYear()
        ]);
        $oldInfo = $user->toArray();
        $user->fixInfoByPayments();
        $newInfo = $user->toArray();
        $this->assertTrue($oldInfo == $newInfo);
    }

    /**
     * 非白名单用户重置测试
     * 前置条件：创建一个白名单用户，角色为Pro,过期时间为当前时间+1年
     * 执行测试：修复该用户的用户信息
     * 执行结果：用户的role角色变free, 过期时间为null或者0000-00-00 00:00:00
     */
    public function testRestNoWhitelist()
    {
        $faker = app('Faker\Generator');
        $user = factory(\App\User::class)->create([
            'role_id' => Role::where('name', 'Pro')->first()->id,
            'expired' => Carbon::now()->addYear()
        ]);
        $oldInfo = $user->toArray();
        $user->fixInfoByPayments();
        $newInfo = $user->toArray();
        $this->assertTrue($oldInfo != $newInfo);
        $this->assertTrue($user->role()->first()->name == 'Free');
        $this->assertTrue($user->expired == null || $user->expired == '0000-00-00 00:00:00');
    }
}
