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
        $user = factory(App\User::class)->make(['email' => 'faker@bigbigads.com']);
        if (!($user instanceof User)) {
           $this->assertTrue(false);
           return; 
        }
        $role = Role::where('name', $roleName)->first();
        if (!($role instanceof Role)) {
           $this->assertTrue(false);
           return; 
        }
        $user->role_id = $role->id;
        $user->initUsageByRole($user->role);
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
