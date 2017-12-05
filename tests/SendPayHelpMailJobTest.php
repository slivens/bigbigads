<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Contracts\Bus\Dispatcher;
use App\Jobs\SendPayHelpMail;
use App\User;

/**
 * 发送注册邮件Job测试
 * 
 * 邮件接收人由于是不定的，需要在.testing.env文件中指定USER_EMAIL。
 *
 * USER_EMAIL为空，表示不测试。
 * USER_EMAIL不为空，则为正常测试。
 * 如果使用了mailgun，则可通过mailgun自动检查邮件是否送到mailgun;
 * 如果使用的是smtp，则需要自己手动确认邮件发送情况;
 */
class SendPayHelpMailJobTest extends TestCase
{
    use DatabaseTransactions;

    public function testBasic()
    {
        if (!env('USER_EMAIL')) {
            $this->assertTrue(true);
            return;
        }
        $user = User::where('email', env('USER_EMAIL'))->first();
        $this->assertTrue($user instanceof User);
        $res = app(Dispatcher::class)->dispatchNow(new SendPayHelpMail($user));
        if (app('app.service.user')->mailDriver() == 'mailgun') {
            $this->assertTrue($res->status == 200);
        }
    }
}
