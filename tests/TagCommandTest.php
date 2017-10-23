<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;

/**
 * 设置黑名单、白名单、默认值测试
 */
class TagCommandTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $user = factory(App\User::class)->create(['email' => 'faker-tag@bigbigads.com']);
        Artisan::call('bba:tag', [
            'user_email' => $user->email,
            'tag' => User::TAG_BLACKLIST
        ]);
        $this->seeInDatabase('users', ['email' => $user->email, 'tag' => User::TAG_BLACKLIST]);

        Artisan::call('bba:tag', [
            'user_email' => $user->email,
            'tag' => User::TAG_WHITELIST
        ]);
        $this->seeInDatabase('users', ['email' => $user->email, 'tag' => User::TAG_WHITELIST]);

        Artisan::call('bba:tag', [
            'user_email' => $user->email,
            'tag' => User::TAG_DEFAULT
        ]);
        $this->seeInDatabase('users', ['email' => $user->email, 'tag' => User::TAG_DEFAULT]);
    }
}
