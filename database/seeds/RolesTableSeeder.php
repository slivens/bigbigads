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
                'id' => 1,
                'name' => 'admin',
                'display_name' => 'Administrator',
                'created_at' => '2017-01-22 08:55:08',
                'updated_at' => '2017-01-22 08:55:08',
                'plan' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'user',
                'display_name' => 'Normal User',
                'created_at' => '2017-01-22 08:55:08',
                'updated_at' => '2017-01-22 08:55:08',
                'plan' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Free',
                'display_name' => 'Free',
                'created_at' => '2017-01-28 05:19:31',
                'updated_at' => '2018-02-10 10:37:05',
                'plan' => 'free',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Standard',
                'display_name' => 'Standard',
                'created_at' => '2017-01-28 05:19:31',
                'updated_at' => '2017-12-15 03:08:59',
                'plan' => 'standard',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Advanced',
                'display_name' => 'Advanced',
                'created_at' => '2017-01-28 05:19:31',
                'updated_at' => '2018-02-10 10:42:09',
                'plan' => 'advanced',
            ),
            5 => 
            array (
                'id' => 10,
                'name' => 'Lite',
                'display_name' => 'Lite',
                'created_at' => '2017-10-21 13:56:25',
                'updated_at' => '2017-12-15 03:08:59',
                'plan' => 'lite',
            ),
        ));
        
        
    }
}