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
        $users = User::where('aff_id', null)->get();

        foreach ($users as $user) {
            $aff = Affiliate::create([
                'name'      => $user->name,
                'email'     => $user->email,
                'password'  => $user->password,
                'track'     => str_random(10),
                'status'    => 1,
                'type'      => 0
            ]);

            $user->aff_id = $aff->id;
            $user->save();

            echo 'Generate track "' . $aff->track . '" to ' . $user->email . "\n";
        }

        echo 'Generate track complete!' . "\n";
    }
}
