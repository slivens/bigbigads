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
                'id' => 1,
                'menu_id' => 1,
                'title' => 'Dashboard',
                'url' => '/admin',
                'target' => '_self',
                'icon_class' => 'voyager-boat',
                'color' => NULL,
                'parent_id' => NULL,
                'order' => 1,
                'created_at' => '2017-01-22 16:55:08',
                'updated_at' => '2017-01-22 16:55:08',
                'route' => NULL,
                'parameters' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'menu_id' => 1,
                'title' => 'Media',
                'url' => '/admin/media',
                'target' => '_self',
                'icon_class' => 'voyager-images',
                'color' => NULL,
                'parent_id' => NULL,
                'order' => 4,
                'created_at' => '2017-01-22 16:55:08',
                'updated_at' => '2017-05-17 17:20:29',
                'route' => NULL,
                'parameters' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'menu_id' => 1,
                'title' => 'Posts',
                'url' => '/admin/posts',
                'target' => '_self',
                'icon_class' => 'voyager-news',
                'color' => NULL,
                'parent_id' => NULL,
                'order' => 5,
                'created_at' => '2017-01-22 16:55:08',
                'updated_at' => '2017-05-17 17:20:29',
                'route' => NULL,
                'parameters' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'menu_id' => 1,
                'title' => '用户',
                'url' => '/admin/users',
                'target' => '_self',
                'icon_class' => 'voyager-person',
                'color' => '#000000',
                'parent_id' => NULL,
                'order' => 3,
                'created_at' => '2017-01-22 16:55:08',
                'updated_at' => '2017-06-14 08:17:17',
                'route' => NULL,
                'parameters' => '',
            ),
            4 => 
            array (
                'id' => 5,
                'menu_id' => 1,
                'title' => 'Categories',
                'url' => '/admin/categories',
                'target' => '_self',
                'icon_class' => 'voyager-categories',
                'color' => NULL,
                'parent_id' => NULL,
                'order' => 7,
                'created_at' => '2017-01-22 16:55:08',
                'updated_at' => '2017-05-17 17:20:29',
                'route' => NULL,
                'parameters' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'menu_id' => 1,
                'title' => 'Pages',
                'url' => '/admin/pages',
                'target' => '_self',
                'icon_class' => 'voyager-file-text',
                'color' => NULL,
                'parent_id' => NULL,
                'order' => 6,
                'created_at' => '2017-01-22 16:55:08',
                'updated_at' => '2017-05-17 17:20:29',
                'route' => NULL,
                'parameters' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'menu_id' => 1,
                'title' => '角色',
                'url' => '/admin/roles',
                'target' => '_self',
                'icon_class' => 'voyager-lock',
                'color' => '#000000',
                'parent_id' => NULL,
                'order' => 2,
                'created_at' => '2017-01-22 16:55:08',
                'updated_at' => '2017-06-14 08:17:08',
                'route' => NULL,
                'parameters' => '',
            ),
            7 => 
            array (
                'id' => 8,
                'menu_id' => 1,
                'title' => 'Tools',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-tools',
                'color' => NULL,
                'parent_id' => NULL,
                'order' => 8,
                'created_at' => '2017-01-22 16:55:08',
                'updated_at' => '2017-05-17 17:20:29',
                'route' => NULL,
                'parameters' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'menu_id' => 1,
                'title' => 'Menu Builder',
                'url' => '/admin/menus',
                'target' => '_self',
                'icon_class' => 'voyager-list',
                'color' => NULL,
                'parent_id' => 8,
                'order' => 1,
                'created_at' => '2017-01-22 16:55:08',
                'updated_at' => '2017-05-08 12:09:24',
                'route' => NULL,
                'parameters' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'menu_id' => 1,
                'title' => 'Database',
                'url' => '/admin/database',
                'target' => '_self',
                'icon_class' => 'voyager-data',
                'color' => NULL,
                'parent_id' => 8,
                'order' => 2,
                'created_at' => '2017-01-22 16:55:08',
                'updated_at' => '2017-05-08 12:09:24',
                'route' => NULL,
                'parameters' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'menu_id' => 1,
                'title' => 'Settings',
                'url' => '/admin/settings',
                'target' => '_self',
                'icon_class' => 'voyager-settings',
                'color' => NULL,
                'parent_id' => NULL,
                'order' => 9,
                'created_at' => '2017-01-22 16:55:08',
                'updated_at' => '2017-05-17 17:20:29',
                'route' => NULL,
                'parameters' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'menu_id' => 1,
                'title' => '市场营销',
                'url' => '',
                'target' => '_self',
                'icon_class' => 'voyager-mail',
                'color' => '#ff0000',
                'parent_id' => NULL,
                'order' => 10,
                'created_at' => '2017-05-08 12:09:18',
                'updated_at' => '2017-05-30 22:49:24',
                'route' => NULL,
                'parameters' => '',
            ),
            12 => 
            array (
                'id' => 13,
                'menu_id' => 1,
                'title' => '邮件列表',
                'url' => '/admin/maillist',
                'target' => '_self',
                'icon_class' => 'voyager-mail',
                'color' => '#000000',
                'parent_id' => 12,
                'order' => 1,
                'created_at' => '2017-05-08 12:24:36',
                'updated_at' => '2017-05-08 12:24:46',
                'route' => NULL,
                'parameters' => NULL,
            ),
            13 => 
            array (
                'id' => 14,
                'menu_id' => 1,
                'title' => '邮件营销群发',
                'url' => '/edm',
                'target' => '_self',
                'icon_class' => 'voyager-play',
                'color' => '#000000',
                'parent_id' => 12,
                'order' => 2,
                'created_at' => '2017-05-08 12:25:31',
                'updated_at' => '2017-06-01 23:12:21',
                'route' => NULL,
                'parameters' => '',
            ),
            14 => 
            array (
                'id' => 15,
                'menu_id' => 1,
                'title' => '联盟会员',
                'url' => '/admin/affiliates',
                'target' => '_self',
                'icon_class' => 'voyager-people',
                'color' => '#000000',
                'parent_id' => 12,
                'order' => 3,
                'created_at' => '2017-05-30 23:00:40',
                'updated_at' => '2017-05-30 23:01:32',
                'route' => NULL,
                'parameters' => '',
            ),
            15 => 
            array (
                'id' => 16,
                'menu_id' => 1,
                'title' => '优惠券',
                'url' => '/admin/coupons',
                'target' => '_self',
                'icon_class' => 'voyager-ticket',
                'color' => '#000000',
                'parent_id' => 12,
                'order' => 4,
                'created_at' => '2017-06-12 14:39:03',
                'updated_at' => '2017-06-12 14:39:11',
                'route' => NULL,
                'parameters' => '',
            ),
        ));
        
        
    }
}