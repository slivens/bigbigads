<?php

use Illuminate\Database\Seeder;

class PlansTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('plans')->delete();
        
        \DB::table('plans')->insert(array (
            0 => 
            array (
                'id' => 81,
                'role_id' => 3,
                'name' => 'free',
                'display_name' => 'Free Level',
                'desc' => 'Free Plan',
                'display_order' => 0,
                'type' => 'REGULAR',
                'frequency' => 'YEAR',
                'frequency_interval' => 1,
                'cycles' => 0,
                'amount' => 0.0,
                'currency' => 'USD',
                'paypal_id' => '',
                'created_at' => '2018-03-09 10:09:58',
                'updated_at' => '2018-03-09 10:09:58',
                'setup_fee' => NULL,
                'delay_days' => 0,
                'slug' => 'free',
            ),
            1 => 
            array (
                'id' => 82,
                'role_id' => 3,
                'name' => 'free_monthly',
                'display_name' => 'Free Level',
                'desc' => 'Free Plan for an month',
                'display_order' => 0,
                'type' => 'REGULAR',
                'frequency' => 'MONTH',
                'frequency_interval' => 1,
                'cycles' => 0,
                'amount' => 0.0,
                'currency' => 'USD',
                'paypal_id' => '',
                'created_at' => '2018-03-09 10:09:58',
                'updated_at' => '2018-03-09 10:09:58',
                'setup_fee' => NULL,
                'delay_days' => 0,
                'slug' => 'free_monthly',
            ),
            2 => 
            array (
                'id' => 83,
                'role_id' => 10,
                'name' => 'lite_monthly',
                'display_name' => 'Lite Monthly',
                'desc' => 'Lite Plan for one month',
                'display_order' => 1,
                'type' => 'REGULAR',
                'frequency' => 'MONTH',
                'frequency_interval' => 1,
                'cycles' => 0,
                'amount' => 49.0,
                'currency' => 'USD',
                'paypal_id' => '',
                'created_at' => '2018-03-09 10:09:58',
                'updated_at' => '2018-03-08 18:09:00',
                'setup_fee' => 0.0,
                'delay_days' => 0,
                'slug' => 'lite_monthly',
            ),
            3 => 
            array (
                'id' => 84,
                'role_id' => 10,
                'name' => 'lite_quarterly',
                'display_name' => 'Lite Quarterly',
                'desc' => 'Lite Plan for three month',
                'display_order' => 1,
                'type' => 'REGULAR',
                'frequency' => 'MONTH',
                'frequency_interval' => 3,
                'cycles' => 0,
                'amount' => 119.97,
                'currency' => 'USD',
                'paypal_id' => '',
                'created_at' => '2018-03-09 10:09:58',
                'updated_at' => '2018-03-09 10:09:58',
                'setup_fee' => NULL,
                'delay_days' => 0,
                'slug' => 'lite_quarterly',
            ),
            4 => 
            array (
                'id' => 85,
                'role_id' => 10,
                'name' => 'lite_annual',
                'display_name' => 'Lite Annual',
                'desc' => 'Lite Plan for one year',
                'display_order' => 1,
                'type' => 'REGULAR',
                'frequency' => 'YEAR',
                'frequency_interval' => 1,
                'cycles' => 0,
                'amount' => 299.88,
                'currency' => 'USD',
                'paypal_id' => '',
                'created_at' => '2018-03-09 10:09:58',
                'updated_at' => '2018-03-09 10:09:58',
                'setup_fee' => NULL,
                'delay_days' => 0,
                'slug' => 'lite_annual',
            ),
            5 => 
            array (
                'id' => 86,
                'role_id' => 4,
                'name' => 'standard_monthly',
                'display_name' => 'Standard Monthly',
                'desc' => 'standard  Plan for one month',
                'display_order' => 2,
                'type' => 'REGULAR',
                'frequency' => 'MONTH',
                'frequency_interval' => 1,
                'cycles' => 0,
                'amount' => 99.0,
                'currency' => 'USD',
                'paypal_id' => '',
                'created_at' => '2018-03-09 10:09:58',
                'updated_at' => '2018-03-09 10:09:58',
                'setup_fee' => NULL,
                'delay_days' => 0,
                'slug' => 'standard_monthly',
            ),
            6 => 
            array (
                'id' => 87,
                'role_id' => 4,
                'name' => 'standard_quarter_monthly',
                'display_name' => 'Standard Quarterly',
                'desc' => 'standard  Plan for three months',
                'display_order' => 2,
                'type' => 'REGULAR',
                'frequency' => 'MONTH',
                'frequency_interval' => 3,
                'cycles' => 0,
                'amount' => 237.0,
                'currency' => 'USD',
                'paypal_id' => '',
                'created_at' => '2018-03-09 10:09:58',
                'updated_at' => '2018-03-09 10:09:58',
                'setup_fee' => NULL,
                'delay_days' => 0,
                'slug' => 'standard_quarterly',
            ),
            7 => 
            array (
                'id' => 88,
                'role_id' => 4,
                'name' => 'standard',
                'display_name' => 'Standard Annual',
                'desc' => 'standard  Plan for one year',
                'display_order' => 2,
                'type' => 'REGULAR',
                'frequency' => 'YEAR',
                'frequency_interval' => 1,
                'cycles' => 0,
                'amount' => 780.0,
                'currency' => 'USD',
                'paypal_id' => '',
                'created_at' => '2018-03-09 10:09:58',
                'updated_at' => '2018-03-09 10:09:58',
                'setup_fee' => NULL,
                'delay_days' => 0,
                'slug' => 'standard_annual',
            ),
            8 => 
            array (
                'id' => 89,
                'role_id' => 5,
                'name' => 'advanced_monthly',
                'display_name' => 'Plus Plan',
                'desc' => 'Plus Plan for one month',
                'display_order' => 3,
                'type' => 'REGULAR',
                'frequency' => 'MONTH',
                'frequency_interval' => 1,
                'cycles' => 0,
                'amount' => 149.0,
                'currency' => 'USD',
                'paypal_id' => '',
                'created_at' => '2018-03-09 10:09:58',
                'updated_at' => '2018-03-08 18:09:00',
                'setup_fee' => 0.0,
                'delay_days' => 0,
                'slug' => 'advanced_monthly',
            ),
            9 => 
            array (
                'id' => 90,
                'role_id' => 5,
                'name' => 'advanced',
                'display_name' => 'advanced Plan',
                'desc' => 'advanced Plan for one year',
                'display_order' => 3,
                'type' => 'REGULAR',
                'frequency' => 'YEAR',
                'frequency_interval' => 1,
                'cycles' => 0,
                'amount' => 1668.0,
                'currency' => 'USD',
                'paypal_id' => '',
                'created_at' => '2018-03-09 10:09:58',
                'updated_at' => '2018-03-09 10:09:58',
                'setup_fee' => NULL,
                'delay_days' => 0,
                'slug' => 'advanced',
            ),
            10 => 
            array (
                'id' => 91,
                'role_id' => 0,
                'name' => 'vip_monthly',
                'display_name' => 'vip Plan',
                'desc' => 'vip Plan for one month',
                'display_order' => 4,
                'type' => 'REGULAR',
                'frequency' => 'MONTH',
                'frequency_interval' => 1,
                'cycles' => 0,
                'amount' => 299.0,
                'currency' => 'USD',
                'paypal_id' => '',
                'created_at' => '2018-03-09 10:09:58',
                'updated_at' => '2018-03-09 10:09:58',
                'setup_fee' => NULL,
                'delay_days' => 0,
                'slug' => 'vip_monthly',
            ),
            11 => 
            array (
                'id' => 92,
                'role_id' => 0,
                'name' => 'vip',
                'display_name' => 'vip Plan',
                'desc' => 'vip Plan for one year',
                'display_order' => 4,
                'type' => 'REGULAR',
                'frequency' => 'YEAR',
                'frequency_interval' => 1,
                'cycles' => 0,
                'amount' => 3588.0,
                'currency' => 'USD',
                'paypal_id' => '',
                'created_at' => '2018-03-09 10:09:58',
                'updated_at' => '2018-03-09 10:09:58',
                'setup_fee' => NULL,
                'delay_days' => 0,
                'slug' => 'vip',
            ),
        ));
        
        
    }
}