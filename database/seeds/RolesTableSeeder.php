<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('roles')->delete();
        
        \DB::table('roles')->insert(array (
            0 => 
            array (
                'display_name' => 'Administrator',
                'id' => 1,
                'name' => 'admin',
                'plan' => NULL,
                'updated_at' => '2017-01-22 08:55:08',
            ),
            1 => 
            array (
                'display_name' => 'Normal User',
                'id' => 2,
                'name' => 'user',
                'plan' => NULL,
                'updated_at' => '2017-01-22 08:55:08',
            ),
            2 => 
            array (
                'display_name' => 'Free',
                'id' => 3,
                'name' => 'Free',
                'plan' => 'free',
                'updated_at' => '2018-02-10 10:37:05',
            ),
            3 => 
            array (
                'display_name' => 'Standard',
                'id' => 4,
                'name' => 'Standard',
                'plan' => 'standard',
                'updated_at' => '2017-12-15 03:08:59',
            ),
            4 => 
            array (
                'display_name' => 'Advanced',
                'id' => 5,
                'name' => 'Advanced',
                'plan' => 'advanced',
                'updated_at' => '2018-02-10 10:42:09',
            ),
            5 => 
            array (
                'display_name' => 'Lite',
                'id' => 10,
                'name' => 'Lite',
                'plan' => 'lite',
                'updated_at' => '2017-12-15 03:08:59',
            ),
        ));
        
        
    }
}