<?php

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('settings')->delete();
        
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 1,
                'key' => 'email_verification',
                'display_name' => 'Email Verification',
                'value' => 'false',
                'details' => '',
                'type' => 'text',
                'order' => 0,
            ),
            1 => 
            array (
                'id' => 2,
                'key' => 'free_role_get_all_ads',
                'display_name' => 'Free Role Get All Ads',
                'value' => 'true',
                'details' => '',
                'type' => 'text',
                'order' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'key' => 'captcha',
                'display_name' => '验证码启用',
                'value' => '',
                'details' => '',
                'type' => 'text',
                'order' => 0,
            ),
            3 => 
            array (
                'id' => 4,
                'key' => 'title',
                'display_name' => 'Site Title',
                'value' => 'Site Title',
                'details' => '',
                'type' => 'text',
                'order' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'key' => 'description',
                'display_name' => 'Site Description',
                'value' => 'Site Description',
                'details' => '',
                'type' => 'text',
                'order' => 2,
            ),
            5 => 
            array (
                'id' => 6,
                'key' => 'logo',
                'display_name' => 'Site Logo',
                'value' => '',
                'details' => '',
                'type' => 'image',
                'order' => 3,
            ),
            6 => 
            array (
                'id' => 7,
                'key' => 'admin_bg_image',
                'display_name' => 'Admin Background Image',
                'value' => '',
                'details' => '',
                'type' => 'image',
                'order' => 9,
            ),
            7 => 
            array (
                'id' => 8,
                'key' => 'admin_title',
                'display_name' => 'Admin Title',
                'value' => 'Voyager',
                'details' => '',
                'type' => 'text',
                'order' => 4,
            ),
            8 => 
            array (
                'id' => 9,
                'key' => 'admin_description',
                'display_name' => 'Admin Description',
                'value' => 'Welcome to Voyager. The Missing Admin for Laravel',
                'details' => '',
                'type' => 'text',
                'order' => 5,
            ),
            9 => 
            array (
                'id' => 10,
                'key' => 'admin_loader',
                'display_name' => 'Admin Loader',
                'value' => '',
                'details' => '',
                'type' => 'image',
                'order' => 6,
            ),
            10 => 
            array (
                'id' => 11,
                'key' => 'admin_icon_image',
                'display_name' => 'Admin Icon Image',
                'value' => '',
                'details' => '',
                'type' => 'image',
                'order' => 7,
            ),
            11 => 
            array (
                'id' => 12,
                'key' => 'google_analytics_client_id',
                'display_name' => 'Google Analytics Client ID',
                'value' => '',
                'details' => '',
                'type' => 'text',
                'order' => 9,
            ),
            12 => 
            array (
                'id' => 15,
                'key' => 'captcha_type',
                'display_name' => '验证码类型',
                'value' => '',
                'details' => '',
                'type' => 'text',
                'order' => 10,
            ),
            13 => 
            array (
                'id' => 17,
                'key' => 'global_session_count',
                'display_name' => '全局Session限制数量',
                'value' => '10',
                'details' => '',
                'type' => 'text',
                'order' => 12,
            ),
            14 => 
            array (
                'id' => 18,
                'key' => 'global_session_ip_count',
                'display_name' => '全局单Session的IP数量',
                'value' => '5',
                'details' => '',
                'type' => 'text',
                'order' => 13,
            ),
            15 => 
            array (
                'id' => 19,
                'key' => 'check_email_validity',
                'display_name' => '用户邮箱有效性检查',
                'value' => '1',
                'details' => '',
                'type' => 'text',
                'order' => 14,
            ),
        ));
        
        
    }
}