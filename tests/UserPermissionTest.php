<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserPermissionTest extends TestCase
{
     use DatabaseTransactions;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $user = $this->fakeUser();
        $this->assertTrue(!$user->can('monitor_support'));
        
        $this->assertTrue(true);
    }
}
