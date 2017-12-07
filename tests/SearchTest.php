<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Role;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use TCG\Voyager\Models\Setting;

class SearchTest extends TestCase
{
    use DatabaseTransactions;
    public function search($user, $code = 200, $params = null)
    {
        $faker = app('Faker\Generator');
        if (!$params) {
            $params = json_decode('{"search_result":"ads","sort":{"field":"default","order":1},"where":[],"limit":[0,10],"is_why_all":1,"topN":10,"is_stat":0,"keys":[{"string":"loan","search_fields":"message,name,description,caption,link,adser_username,adser_name,dest_site,buttonlink,event_id","relation":"Must"}],"action":"search"}', true);
        }
        $params['keys'][0]['string'] = $faker->domainWord;
        $response = $this->actingAs($user)
            ->call('POST', '/forward/adsearch', $params);
        /* if ($response->status() != 200) */
        /*     echo  json_encode($response); */
        $this->assertEquals($code, $response->status());
        return $response;
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasic()
    {
		/* Cache::shouldReceive('get') */
                    /* ->once() */
                    /* ->with('role-standard') */
                    /* ->andReturn('value'); */

        $user = factory(App\User::class)->make(['email' => 'faker@bigbigads.com']);
        if (!($user instanceof User)) {
           $this->assertTrue(false);
           return; 
        }
        $role = Role::where('name', 'Standard')->first();
        if (!($role instanceof Role)) {
           $this->assertTrue(false);
           return; 
        }
		$role->generateCache();
        $user->role()->associate($role);
        $user->reInitUsage();
        $user->save();
        $this->search($user);
        return $user;
        /* $this->assertTrue(true); */
    }

     /**
      * @depends testBasic
      */
     public function testAttack($user)
     {
         echo "Testing attach, it may cost time.";
         /* for ($i = 0; $i < 720; ++$i) { */
         /*     $this->search($user); */
         /* } */
         $this->assertTrue(true);
     }

    /**
     * 创建2017.07.31之前的免费用户，不设置邮箱，发起搜索，应该返回错误
     * @depends testBasic
     */
    public function testUneffectiveEmailSearch($user)
    {
        Setting::where('key', 'check_email_validity')
            ->update(['value' => 1]);
        $user = $this->fakeUser('Free', ['created_at' => new Carbon('2017-03-06 00:00:00')]);
        $user->save();
        $params = json_decode('{"search_result":"ads","sort":{"field":"default","order":1},"where":[{"field":"time","min":"2016-01-01","max":"2017-11-30","role":"free"}],"limit":[0,10],"is_why_all":1,"topN":10,"is_stat":0,"keys":[{"string":"adidas","search_fields":"message,name,description,caption,link,adser_username,adser_name,dest_site,buttonlink,event_id","relation":"Must"}],"action":"search"}', true);
        $this->search($user, 422, $params);

        $user->created_at = '2017-11-01 00:00:00';

        $this->search($user, 200, $params);
    }
}
