<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Affiliate;

class AffiliateTestCase extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAffiliate()
    {
        
        $faker = app('Faker\Generator');
        // 创建User时，自动生成Affiliate的测试 
        $user = factory(\App\User::class)->create();
        $affiliate = $user->affiliates()->first();
        $this->assertTrue($affiliate instanceof Affiliate);
        $this->assertTrue($affiliate->name == $user->name);
        $this->assertTrue($affiliate->password == $user->password);
        // 改变user email时，affiliate的email应跟着改变的测试
        $user->email = $faker->email;
        $user->save();
        
        $affiliate = $user->affiliates()->first();
        $this->assertTrue($affiliate instanceof Affiliate);

        // 检查track中间件是否正常工作
        $trackCode = $affiliate->track;
        $this->call('POST', '/trackState?track=' . $trackCode);
        $affiliate = Affiliate::find($affiliate->id);
        $this->assertTrue($affiliate->click > 0);
    }
}
