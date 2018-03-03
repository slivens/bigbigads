<?php

use Illuminate\Database\Seeder;

class DataTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('data_types')->delete();
        
        \DB::table('data_types')->insert(array (
            0 => 
            array (
                'description' => '',
                'display_name_plural' => 'Posts',
                'display_name_singular' => 'Post',
                'generate_permissions' => 1,
                'icon' => 'voyager-news',
                'id' => 1,
                'model_name' => 'TCG\\Voyager\\Models\\Post',
                'name' => 'posts',
                'server_side' => 0,
                'slug' => 'posts',
                'updated_at' => '2017-01-22 08:55:07',
            ),
            1 => 
            array (
                'description' => '',
                'display_name_plural' => 'Pages',
                'display_name_singular' => 'Page',
                'generate_permissions' => 1,
                'icon' => 'voyager-file-text',
                'id' => 2,
                'model_name' => 'TCG\\Voyager\\Models\\Page',
                'name' => 'pages',
                'server_side' => 0,
                'slug' => 'pages',
                'updated_at' => '2017-01-22 08:55:07',
            ),
            2 => 
            array (
                'description' => '',
                'display_name_plural' => 'Users',
                'display_name_singular' => 'User',
                'generate_permissions' => 1,
                'icon' => 'voyager-person',
                'id' => 3,
                'model_name' => 'TCG\\Voyager\\Models\\User',
                'name' => 'users',
                'server_side' => 1,
                'slug' => 'users',
                'updated_at' => '2017-11-14 13:23:50',
            ),
            3 => 
            array (
                'description' => '',
                'display_name_plural' => 'Categories',
                'display_name_singular' => 'Category',
                'generate_permissions' => 1,
                'icon' => 'voyager-categories',
                'id' => 4,
                'model_name' => 'TCG\\Voyager\\Models\\Category',
                'name' => 'categories',
                'server_side' => 0,
                'slug' => 'categories',
                'updated_at' => '2017-01-22 08:55:07',
            ),
            4 => 
            array (
                'description' => '',
                'display_name_plural' => 'Menus',
                'display_name_singular' => 'Menu',
                'generate_permissions' => 1,
                'icon' => 'voyager-list',
                'id' => 5,
                'model_name' => 'TCG\\Voyager\\Models\\Menu',
                'name' => 'menus',
                'server_side' => 0,
                'slug' => 'menus',
                'updated_at' => '2017-01-22 08:55:07',
            ),
            5 => 
            array (
                'description' => '',
                'display_name_plural' => 'Roles',
                'display_name_singular' => 'Role',
                'generate_permissions' => 1,
                'icon' => 'voyager-lock',
                'id' => 6,
                'model_name' => 'TCG\\Voyager\\Models\\Role',
                'name' => 'roles',
                'server_side' => 0,
                'slug' => 'roles',
                'updated_at' => '2017-01-22 08:55:07',
            ),
            6 => 
            array (
                'description' => '营销发送的自定义邮件列表',
                'display_name_plural' => '邮件列表',
                'display_name_singular' => '邮件',
                'generate_permissions' => 1,
                'icon' => 'voyager-mail',
                'id' => 7,
                'model_name' => 'App\\Maillist',
                'name' => 'maillist',
                'server_side' => 0,
                'slug' => 'maillist',
                'updated_at' => '2017-05-08 03:23:11',
            ),
            7 => 
            array (
                'description' => '',
                'display_name_plural' => '联盟会员',
                'display_name_singular' => '联盟会员',
                'generate_permissions' => 1,
                'icon' => 'voyager-people',
                'id' => 9,
                'model_name' => 'App\\Affiliate',
                'name' => 'affiliates',
                'server_side' => 1,
                'slug' => 'affiliates',
                'updated_at' => '2017-08-26 15:00:50',
            ),
            8 => 
            array (
                'description' => '优惠券功能',
                'display_name_plural' => '优惠券',
                'display_name_singular' => '优惠券',
                'generate_permissions' => 1,
                'icon' => 'voyager-ticket',
                'id' => 11,
                'model_name' => 'App\\Coupon',
                'name' => 'coupons',
                'server_side' => 0,
                'slug' => 'coupons',
                'updated_at' => '2017-06-12 06:38:06',
            ),
            9 => 
            array (
                'description' => 'AAA',
                'display_name_plural' => '退款申请单',
                'display_name_singular' => '退款申请单',
                'generate_permissions' => 1,
                'icon' => '',
                'id' => 12,
                'model_name' => 'App\\Refund',
                'name' => 'refunds',
                'server_side' => 1,
                'slug' => 'refunds',
                'updated_at' => '2017-08-26 14:57:51',
            ),
            10 => 
            array (
                'description' => '权限管理',
                'display_name_plural' => '权限',
                'display_name_singular' => '权限',
                'generate_permissions' => 1,
                'icon' => '',
                'id' => 15,
                'model_name' => 'App\\Permission',
                'name' => 'permissions',
                'server_side' => 1,
                'slug' => 'permissions',
                'updated_at' => '2017-11-16 01:58:48',
            ),
            11 => 
            array (
                'description' => '',
                'display_name_plural' => '策略',
                'display_name_singular' => '策略',
                'generate_permissions' => 1,
                'icon' => '',
                'id' => 16,
                'model_name' => 'App\\Policy',
                'name' => 'policies',
                'server_side' => 1,
                'slug' => 'policies',
                'updated_at' => '2017-12-05 10:57:12',
            ),
            12 => 
            array (
                'description' => '',
                'display_name_plural' => '价格计划',
                'display_name_singular' => '价格计划',
                'generate_permissions' => 1,
                'icon' => '',
                'id' => 17,
                'model_name' => 'App\\Plan',
                'name' => 'plans',
                'server_side' => 0,
                'slug' => 'plans',
                'updated_at' => '2018-02-10 17:22:42',
            ),
            13 => 
            array (
                'description' => '',
                'display_name_plural' => 'Gateway Configs',
                'display_name_singular' => 'Gateway Config',
                'generate_permissions' => 1,
                'icon' => '',
                'id' => 18,
                'model_name' => 'App\\GatewayConfig',
                'name' => 'gateway_configs',
                'server_side' => 0,
                'slug' => 'gateway-configs',
                'updated_at' => '2018-02-11 00:13:41',
            ),
            14 => 
            array (
                'description' => '',
                'display_name_plural' => 'Payments',
                'display_name_singular' => 'Payment',
                'generate_permissions' => 1,
                'icon' => '',
                'id' => 19,
                'model_name' => 'App\\Payment',
                'name' => 'payments',
                'server_side' => 1,
                'slug' => 'payments',
                'updated_at' => '2018-02-27 15:25:28',
            ),
        ));
        
        
    }
}