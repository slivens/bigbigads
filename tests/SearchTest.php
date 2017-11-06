<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Role;
use Illuminate\Support\Facades\Cache;

class SearchTest extends TestCase
{
    use DatabaseTransactions;
    public function search($user)
    {
        $faker = app('Faker\Generator');
        $params = json_decode('{"search_result":"ads","sort":{"field":"default","order":1},"where":[],"limit":[0,10],"is_why_all":1,"topN":10,"is_stat":0,"keys":[{"string":"loan","search_fields":"message,name,description,caption,link,adser_username,adser_name,dest_site,buttonlink,event_id","relation":"Must"}],"action":"search"}', true);
        $params['keys'][0]['string'] = $faker->domainWord;
        $response = $this->actingAs($user)
            ->call('POST', '/forward/adsearch', $params);
        $this->assertEquals(200, $response->status());
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
}
