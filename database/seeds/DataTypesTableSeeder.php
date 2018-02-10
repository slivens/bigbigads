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
                'id' => 1,
                'name' => 'posts',
                'slug' => 'posts',
                'display_name_singular' => 'Post',
                'display_name_plural' => 'Posts',
                'icon' => 'voyager-news',
                'model_name' => 'TCG\\Voyager\\Models\\Post',
                'description' => '',
                'generate_permissions' => 1,
                'server_side' => 0,
                'created_at' => '2017-01-22 08:55:07',
                'updated_at' => '2017-01-22 08:55:07',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'pages',
                'slug' => 'pages',
                'display_name_singular' => 'Page',
                'display_name_plural' => 'Pages',
                'icon' => 'voyager-file-text',
                'model_name' => 'TCG\\Voyager\\Models\\Page',
                'description' => '',
                'generate_permissions' => 1,
                'server_side' => 0,
                'created_at' => '2017-01-22 08:55:07',
                'updated_at' => '2017-01-22 08:55:07',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'users',
                'slug' => 'users',
                'display_name_singular' => 'User',
                'display_name_plural' => 'Users',
                'icon' => 'voyager-person',
                'model_name' => 'TCG\\Voyager\\Models\\User',
                'description' => '',
                'generate_permissions' => 1,
                'server_side' => 1,
                'created_at' => '2017-01-22 08:55:07',
                'updated_at' => '2017-11-14 13:23:50',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'categories',
                'slug' => 'categories',
                'display_name_singular' => 'Category',
                'display_name_plural' => 'Categories',
                'icon' => 'voyager-categories',
                'model_name' => 'TCG\\Voyager\\Models\\Category',
                'description' => '',
                'generate_permissions' => 1,
                'server_side' => 0,
                'created_at' => '2017-01-22 08:55:07',
                'updated_at' => '2017-01-22 08:55:07',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'menus',
                'slug' => 'menus',
                'display_name_singular' => 'Menu',
                'display_name_plural' => 'Menus',
                'icon' => 'voyager-list',
                'model_name' => 'TCG\\Voyager\\Models\\Menu',
                'description' => '',
                'generate_permissions' => 1,
                'server_side' => 0,
                'created_at' => '2017-01-22 08:55:07',
                'updated_at' => '2017-01-22 08:55:07',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'roles',
                'slug' => 'roles',
                'display_name_singular' => 'Role',
                'display_name_plural' => 'Roles',
                'icon' => 'voyager-lock',
                'model_name' => 'TCG\\Voyager\\Models\\Role',
                'description' => '',
                'generate_permissions' => 1,
                'server_side' => 0,
                'created_at' => '2017-01-22 08:55:07',
                'updated_at' => '2017-01-22 08:55:07',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'maillist',
                'slug' => 'maillist',
                'display_name_singular' => '邮件',
                'display_name_plural' => '邮件列表',
                'icon' => 'voyager-mail',
                'model_name' => 'App\\Maillist',
                'description' => '营销发送的自定义邮件列表',
                'generate_permissions' => 1,
                'server_side' => 0,
                'created_at' => '2017-05-08 03:21:35',
                'updated_at' => '2017-05-08 03:23:11',
            ),
            7 => 
            array (
                'id' => 9,
                'name' => 'affiliates',
                'slug' => 'affiliates',
                'display_name_singular' => '联盟会员',
                'display_name_plural' => '联盟会员',
                'icon' => 'voyager-people',
                'model_name' => 'App\\Affiliate',
                'description' => '',
                'generate_permissions' => 1,
                'server_side' => 1,
                'created_at' => '2017-05-30 14:58:36',
                'updated_at' => '2017-08-26 15:00:50',
            ),
            8 => 
            array (
                'id' => 11,
                'name' => 'coupons',
                'slug' => 'coupons',
                'display_name_singular' => '优惠券',
                'display_name_plural' => '优惠券',
                'icon' => 'voyager-ticket',
                'model_name' => 'App\\Coupon',
                'description' => '优惠券功能',
                'generate_permissions' => 1,
                'server_side' => 0,
                'created_at' => '2017-06-12 06:31:07',
                'updated_at' => '2017-06-12 06:38:06',
            ),
            9 => 
            array (
                'id' => 12,
                'name' => 'refunds',
                'slug' => 'refunds',
                'display_name_singular' => '退款申请单',
                'display_name_plural' => '退款申请单',
                'icon' => '',
                'model_name' => 'App\\Refund',
                'description' => 'AAA',
                'generate_permissions' => 1,
                'server_side' => 1,
                'created_at' => '2017-08-26 04:08:59',
                'updated_at' => '2017-08-26 14:57:51',
            ),
            10 => 
            array (
                'id' => 15,
                'name' => 'permissions',
                'slug' => 'permissions',
                'display_name_singular' => '权限',
                'display_name_plural' => '权限',
                'icon' => '',
                'model_name' => 'App\\Permission',
                'description' => '权限管理',
                'generate_permissions' => 1,
                'server_side' => 1,
                'created_at' => '2017-11-16 01:57:32',
                'updated_at' => '2017-11-16 01:58:48',
            ),
            11 => 
            array (
                'id' => 16,
                'name' => 'policies',
                'slug' => 'policies',
                'display_name_singular' => '策略',
                'display_name_plural' => '策略',
                'icon' => '',
                'model_name' => 'App\\Policy',
                'description' => '',
                'generate_permissions' => 1,
                'server_side' => 1,
                'created_at' => '2017-12-05 10:57:12',
                'updated_at' => '2017-12-05 10:57:12',
            ),
            12 => 
            array (
                'id' => 17,
                'name' => 'plans',
                'slug' => 'plans',
                'display_name_singular' => '价格计划',
                'display_name_plural' => '价格计划',
                'icon' => '',
                'model_name' => 'App\\Plan',
                'description' => '',
                'generate_permissions' => 1,
                'server_side' => 0,
                'created_at' => '2018-02-10 17:22:42',
                'updated_at' => '2018-02-10 17:22:42',
            ),
            13 => 
            array (
                'id' => 18,
                'name' => 'gateway_configs',
                'slug' => 'gateway-configs',
                'display_name_singular' => 'Gateway Config',
                'display_name_plural' => 'Gateway Configs',
                'icon' => '',
                'model_name' => 'App\\GatewayConfig',
                'description' => '',
                'generate_permissions' => 1,
                'server_side' => 0,
                'created_at' => '2018-02-11 00:13:41',
                'updated_at' => '2018-02-11 00:13:41',
            ),
        ));
        
        
    }
}