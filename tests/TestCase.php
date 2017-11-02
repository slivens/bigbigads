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

    public function fakeUser($roleName = 'Free')
    {
        $user = factory(App\User::class)->create(['email' => 'faker@bigbigads.com']);
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
