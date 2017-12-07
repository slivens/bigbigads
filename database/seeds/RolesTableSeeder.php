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
                'created_at' => '2017-01-22 16:55:08',
                'updated_at' => '2017-01-22 16:55:08',
                'plan' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'user',
                'display_name' => 'Normal User',
                'created_at' => '2017-01-22 16:55:08',
                'updated_at' => '2017-01-22 16:55:08',
                'plan' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Free',
                'display_name' => 'Free',
                'created_at' => '2017-01-28 13:19:31',
                'updated_at' => '2017-11-12 19:12:54',
                'plan' => 'free',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Standard',
                'display_name' => 'Standard',
                'created_at' => '2017-01-28 13:19:31',
                'updated_at' => '2017-11-12 19:12:54',
                'plan' => 'standard',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Advanced',
                'display_name' => 'Plus',
                'created_at' => '2017-01-28 13:19:31',
                'updated_at' => '2017-11-12 19:12:54',
                'plan' => 'advanced',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Pro',
                'display_name' => 'Premium',
                'created_at' => '2017-01-28 13:19:31',
                'updated_at' => '2017-11-12 19:12:54',
                'plan' => 'vip',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'OuterTester',
                'display_name' => 'OuterTester',
                'created_at' => '2017-04-18 09:42:37',
                'updated_at' => '2017-11-30 21:59:24',
                'plan' => 'standard',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'OuterTester_feishu',
                'display_name' => 'OuterTester_feishu',
                'created_at' => '2017-08-07 20:42:40',
                'updated_at' => '2017-11-30 21:59:24',
                'plan' => 'standard',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Light',
                'display_name' => 'Light',
                'created_at' => '2017-08-07 20:42:40',
                'updated_at' => '2017-11-30 21:59:24',
                'plan' => 'standard',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'Lite',
                'display_name' => 'Lite',
                'created_at' => '2017-10-21 21:56:25',
                'updated_at' => '2017-11-30 21:59:12',
                'plan' => 'lite',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'HiddenRole',
                'display_name' => '测试用户',
                'created_at' => '2017-12-05 17:46:19',
                'updated_at' => '2017-12-05 17:46:19',
                'plan' => NULL,
            ),
        ));
        
        
    }
}