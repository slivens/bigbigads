<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\ActionLog;
use App\Jobs\SendRegistMail;

/**
 * 注册单元测试：
 * 1. 正常注册，预期结果：页面跳到'/welcome?socialite=email'
 * 2. 注册测试账号(@bigbigadstest.com)，预期结果：页面跳到'/welcome?socialite=email'，同时数据库生成ActionLog
 * 3. 禁止使用的账号测试，预期结果：页面提示错误(TODO)
 */
class SignupTest extends TestCase
{
    use DatabaseTransactions;


    /**
     * 正常的注册测试
     */
    protected function signup($email, $username)
    {
        $this->visit('/register')
            ->type($email, 'email')
            ->type($username, 'name')
            ->type('123456', 'password')
            ->press('signup')
            ->seePageIs('/welcome?socialite=email');
    }

    /**
     * 正常的注册测试
     * @return void
     */
    public function testBasic()
    {
        // 正常注册测试不希望触发发送邮件的Job，发送注册邮件Job由SendRegistMailJobTest单独测试
        $this->expectsJobs(SendRegistMail::class);
        $faker = app('Faker\Generator');
        $this->signup($faker->unique()->safeEmail, $faker->username);
    }

    /**
     * 注册账号的注册
     */
    public function testSignupTest()
    {
        $faker = app('Faker\Generator');
        $username = $faker->username;
        $email = $username . '@bigbigadstest.com';
        $this->signup($email, $username);
        // 测试账号，数据库应该要有ActionLog
        $this->seeInDatabase('action_logs', ['type' => ActionLog::ACTION_REGISTERED_BY_BIGBIGADSTEST, 'param' => $email]);
    }

    /**
     * 测试不支持的Email
     * 该测试框架只能测后端，实测后端未检查
     */
    /* public function testUnsupportEmail() */
    /* { */
    /*     $faker = app('Faker\Generator'); */
    /*     $username = $faker->username; */
    /*     $email = $username . '@hotmail.com'; */

    /*     $this->visit('/register') */
    /*         ->type($email, 'email') */
    /*         ->type($username, 'name') */
    /*         ->type('123456', 'password') */
    /*         ->see("Sorry, we don't support hotmail/outlook/live now."); */
    /* } */   
}
