<?php

use Illuminate\Database\Seeder;

class MenuItemsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('menu_items')->delete();
        
        \DB::table('menu_items')->insert(array (
            0 => 
            array (
                'color' => NULL,
                'icon_class' => 'voyager-boat',
                'id' => 1,
                'menu_id' => 1,
                'order' => 1,
                'parameters' => NULL,
                'parent_id' => NULL,
                'route' => NULL,
                'target' => '_self',
                'title' => 'Dashboard',
                'updated_at' => '2017-01-22 08:55:08',
                'url' => '/backend',
            ),
            1 => 
            array (
                'color' => NULL,
                'icon_class' => 'voyager-images',
                'id' => 2,
                'menu_id' => 1,
                'order' => 9,
                'parameters' => NULL,
                'parent_id' => NULL,
                'route' => NULL,
                'target' => '_self',
                'title' => 'Media',
                'updated_at' => '2018-02-11 00:15:08',
                'url' => '/backend/media',
            ),
            2 => 
            array (
                'color' => NULL,
                'icon_class' => 'voyager-news',
                'id' => 3,
                'menu_id' => 1,
                'order' => 10,
                'parameters' => NULL,
                'parent_id' => NULL,
                'route' => NULL,
                'target' => '_self',
                'title' => 'Posts',
                'updated_at' => '2018-02-11 00:15:08',
                'url' => '/backend/posts',
            ),
            3 => 
            array (
                'color' => '#000000',
                'icon_class' => 'voyager-person',
                'id' => 4,
                'menu_id' => 1,
                'order' => 3,
                'parameters' => '',
                'parent_id' => NULL,
                'route' => NULL,
                'target' => '_self',
                'title' => '用户',
                'updated_at' => '2017-06-14 00:17:17',
                'url' => '/backend/users',
            ),
            4 => 
            array (
                'color' => NULL,
                'icon_class' => 'voyager-categories',
                'id' => 5,
                'menu_id' => 1,
                'order' => 12,
                'parameters' => NULL,
                'parent_id' => NULL,
                'route' => NULL,
                'target' => '_self',
                'title' => 'Categories',
                'updated_at' => '2018-02-11 00:15:06',
                'url' => '/backend/categories',
            ),
            5 => 
            array (
                'color' => NULL,
                'icon_class' => 'voyager-file-text',
                'id' => 6,
                'menu_id' => 1,
                'order' => 11,
                'parameters' => NULL,
                'parent_id' => NULL,
                'route' => NULL,
                'target' => '_self',
                'title' => 'Pages',
                'updated_at' => '2018-02-11 00:15:08',
                'url' => '/backend/pages',
            ),
            6 => 
            array (
                'color' => '#000000',
                'icon_class' => 'voyager-lock',
                'id' => 7,
                'menu_id' => 1,
                'order' => 2,
                'parameters' => '',
                'parent_id' => NULL,
                'route' => NULL,
                'target' => '_self',
                'title' => '角色',
                'updated_at' => '2017-06-14 00:17:08',
                'url' => '/backend/roles',
            ),
            7 => 
            array (
                'color' => NULL,
                'icon_class' => 'voyager-tools',
                'id' => 8,
                'menu_id' => 1,
                'order' => 13,
                'parameters' => NULL,
                'parent_id' => NULL,
                'route' => NULL,
                'target' => '_self',
                'title' => 'Tools',
                'updated_at' => '2018-02-11 00:15:06',
                'url' => '',
            ),
            8 => 
            array (
                'color' => NULL,
                'icon_class' => 'voyager-list',
                'id' => 9,
                'menu_id' => 1,
                'order' => 1,
                'parameters' => NULL,
                'parent_id' => 8,
                'route' => NULL,
                'target' => '_self',
                'title' => 'Menu Builder',
                'updated_at' => '2017-05-08 04:09:24',
                'url' => '/backend/menus',
            ),
            9 => 
            array (
                'color' => NULL,
                'icon_class' => 'voyager-data',
                'id' => 10,
                'menu_id' => 1,
                'order' => 2,
                'parameters' => NULL,
                'parent_id' => 8,
                'route' => NULL,
                'target' => '_self',
                'title' => 'Database',
                'updated_at' => '2017-05-08 04:09:24',
                'url' => '/backend/database',
            ),
            10 => 
            array (
                'color' => NULL,
                'icon_class' => 'voyager-settings',
                'id' => 11,
                'menu_id' => 1,
                'order' => 14,
                'parameters' => NULL,
                'parent_id' => NULL,
                'route' => NULL,
                'target' => '_self',
                'title' => 'Settings',
                'updated_at' => '2018-02-11 00:15:06',
                'url' => '/backend/settings',
            ),
            11 => 
            array (
                'color' => '#ff0000',
                'icon_class' => 'voyager-mail',
                'id' => 12,
                'menu_id' => 1,
                'order' => 4,
                'parameters' => '',
                'parent_id' => NULL,
                'route' => NULL,
                'target' => '_self',
                'title' => '市场营销',
                'updated_at' => '2017-08-26 04:38:34',
                'url' => '',
            ),
            12 => 
            array (
                'color' => '#000000',
                'icon_class' => 'voyager-mail',
                'id' => 13,
                'menu_id' => 1,
                'order' => 2,
                'parameters' => NULL,
                'parent_id' => 12,
                'route' => NULL,
                'target' => '_self',
                'title' => '邮件列表',
                'updated_at' => '2018-02-10 17:25:05',
                'url' => '/backend/maillist',
            ),
            13 => 
            array (
                'color' => '#000000',
                'icon_class' => 'voyager-play',
                'id' => 14,
                'menu_id' => 1,
                'order' => 3,
                'parameters' => '',
                'parent_id' => 12,
                'route' => NULL,
                'target' => '_self',
                'title' => '邮件营销群发',
                'updated_at' => '2018-02-10 17:25:05',
                'url' => '/edm',
            ),
            14 => 
            array (
                'color' => '#000000',
                'icon_class' => 'voyager-people',
                'id' => 15,
                'menu_id' => 1,
                'order' => 4,
                'parameters' => '',
                'parent_id' => 12,
                'route' => NULL,
                'target' => '_self',
                'title' => '联盟会员',
                'updated_at' => '2018-02-10 17:25:05',
                'url' => '/backend/affiliates',
            ),
            15 => 
            array (
                'color' => '#000000',
                'icon_class' => 'voyager-ticket',
                'id' => 16,
                'menu_id' => 1,
                'order' => 5,
                'parameters' => '',
                'parent_id' => 12,
                'route' => NULL,
                'target' => '_self',
                'title' => '优惠券',
                'updated_at' => '2018-02-10 17:25:05',
                'url' => '/backend/coupons',
            ),
            16 => 
            array (
                'color' => '',
                'icon_class' => 'voyager-hotdog',
                'id' => 17,
                'menu_id' => 1,
                'order' => 5,
                'parameters' => '',
                'parent_id' => NULL,
                'route' => NULL,
                'target' => '_self',
                'title' => '退款申请单',
                'updated_at' => '2018-02-10 17:25:05',
                'url' => '/backend/refunds',
            ),
            17 => 
            array (
                'color' => '#000000',
                'icon_class' => 'voyager-bulb',
                'id' => 18,
                'menu_id' => 1,
                'order' => 6,
                'parameters' => '',
                'parent_id' => NULL,
                'route' => NULL,
                'target' => '_self',
                'title' => '权限管理',
                'updated_at' => '2018-02-10 17:25:05',
                'url' => '/backend/permissions',
            ),
            18 => 
            array (
                'color' => '#000000',
                'icon_class' => 'voyager-key',
                'id' => 19,
                'menu_id' => 1,
                'order' => 7,
                'parameters' => '',
                'parent_id' => NULL,
                'route' => NULL,
                'target' => '_self',
                'title' => '策略管理',
                'updated_at' => '2018-02-10 17:25:05',
                'url' => '/backend/policies',
            ),
            19 => 
            array (
                'color' => '#000000',
                'icon_class' => 'voyager-receipt',
                'id' => 20,
                'menu_id' => 1,
                'order' => 1,
                'parameters' => '',
                'parent_id' => 12,
                'route' => NULL,
                'target' => '_self',
                'title' => '价格计划',
                'updated_at' => '2018-02-10 17:25:05',
                'url' => '/backend/plans',
            ),
            20 => 
            array (
                'color' => '#000000',
                'icon_class' => 'voyager-dollar',
                'id' => 21,
                'menu_id' => 1,
                'order' => 8,
                'parameters' => '',
                'parent_id' => NULL,
                'route' => NULL,
                'target' => '_self',
                'title' => '支付网关配置',
                'updated_at' => '2018-02-11 00:16:24',
                'url' => '/backend/gateway-configs',
            ),
        ));
        
        
    }
}