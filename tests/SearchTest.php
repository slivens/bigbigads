<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Role;

class SearchTest extends TestCase
{
     use DatabaseTransactions;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $user = factory(App\User::class)->make(['email' => 'faker@bigbigads.com']);
        if (!($user instanceof User)) {
           $this->assertTrue(false);
           return; 
        }
        $role = Role::where('name', 'Pro')->first();
        if (!($role instanceof Role)) {
           $this->assertTrue(false);
           return; 
        }
        $user->role_id = $role->id;
        $user->initUsageByRole($user->role);
        $faker = app('Faker\Generator');
        $params = json_decode('{"search_result":"ads","sort":{"field":"last_view_date","order":1},"where":[],"limit":[0,10],"is_why_all":1,"topN":10,"is_stat":0,"keys":[{"string":"adidas","search_fields":"message,name,description,caption,link,adser_username,adser_name,dest_site,buttonlink,event_id","relation":"Must"}],"action":"search"}', true);
        $params['keys'][0]['string'] = $faker->domainWord;
        $response = $this->actingAs($user)
            ->call('POST', '/forward/adsearch', $params);
        $this->assertEquals(200, $response->status());
        /* $this->assertTrue(true); */
    }
}
