<?php

use Illuminate\Database\Seeder;

class GatewayConfigsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('gateway_configs')->delete();
        
        \DB::table('gateway_configs')->insert(array (
            0 => 
            array (
                'id' => 1,
                'gateway_name' => 'paypal_ec',
                'factory_name' => 'paypal_express_checkout',
                'config' => '{"sandbox": true, "password": "GM8G8QUF96Z4SM5K", "username": "95496875-facilitator_api1.qq.com", "signature": "AFcWxV21C7fd0v3bYYYRCpSSRl31AXAjyVXCseIVl89pjDWPgVXyKvaa"}',
                'created_at' => '2018-02-11 00:23:04',
                'updated_at' => '2018-02-11 02:25:37',
            ),
        ));
        
        
    }
}