<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Permission;
use App\Bookmark;
use App\BookmarkItem;
class BigbigadsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 1:永久累计 2:按月累计 3:按日累计 4.按小时累计 5.固定数值 6.期限
     * @return void
     */
    public function run()
    {
        //权限分配控制有与没有
        //策略分配控制多少
        $dashboard = ["online_users", "duration", "advertise_date", "advertise_update", "platform"];
        $dashboardPolicy = ['online_users' => 5, "duration"=>5];
       //dashboard permission
       foreach($dashboard as $key=>$item) {
           Permission::firstOrCreate([
                'key'        => $item,
                'table_name' => 'dashboard',
            ]);
       }

        $search = ['times_perday', 'result_per_search', 'search_filter', 'search_sortby', 'advanced_search', 'advertiser_search', 'save_search'];
        foreach($search as $key=>$item) {
            Permission::firstOrCreate([
                'key'        => $item,
                'table_name' => 'search',
            ]);
        }

        $export = ['image_download', 'video_download', 'HD_video_download', 'export'];
        foreach($export as $key=>$item) {
            Permission::firstOrCreate([
                'key'        => $item,
                'table_name' => 'export',
            ]);
        }

        $statics = ['search_statics', 'advertise_analysis', 'advertiser_analysis', 'realtime_ad_analysis'];
        foreach($statics as $key=>$item) {
            Permission::firstOrCreate([
                'key'        => $item,
                'table_name' => 'statics',
            ]);
        }

        $ranking = ['ranking', 'ranking_export', 'ranking_by_category'];
        foreach($ranking as $key=>$item) {
            Permission::firstOrCreate([
                'key'        => $item,
                'table_name' => 'ranking',
            ]);
        }

        $bookmark = ['bookmark_support', 'bookmark_list', 'save_ad_count', 'save_adser_count'];
        foreach($bookmark as $key=>$item) {
            Permission::firstOrCreate([
                'key'        => $item,
                'table_name' => 'bookmark',
            ]);
        }

        $monitor = ['monitor_support', 'monitor_ad_keyword', 'monitor_advertiser'];
        foreach($monitor as $key=>$item) {
            Permission::firstOrCreate([
                'key'        => $item,
                'table_name' => 'monitor',
            ]);
        }
        echo "insert permission data\n";
    

        //收藏夹测试项
        Bookmark::firstOrCreate(['uid'=>1, 'name'=>'Sport']);
        Bookmark::firstOrCreate(['uid'=>1, 'name'=>'Technology']);

        //添加收藏项
        BookmarkItem::firstOrCreate(['uid'=>1, 'bid'=>1, 'type'=>0, 'ident'=> '23842544524540050']);
        BookmarkItem::firstOrCreate(['uid'=>1, 'bid'=>1, 'type'=>1, 'ident'=> 'coxcommunications']);
        echo "insert bookmarks\n";
    }
}
