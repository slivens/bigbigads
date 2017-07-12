<?php

use App\User;
use App\Affiliate;
use Illuminate\Database\Seeder;

class UsersGenerateTrackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::has('affiliates', '=', 0)->get();

        foreach ($users as $user) {
            $affiliate = $user->affiliates()->create([
                'name'      => $user->name,
                'email'     => $user->email,
                'password'  => $user->password,
                'track'     => str_random(10),
                'status'    => 1,
                'type'      => 1
            ]);

            echo 'Generate track "' . $affiliate->track . '" to ' . $user->email . "\n";
        }

        echo 'Generate track complete!' . "\n";
    }
}
