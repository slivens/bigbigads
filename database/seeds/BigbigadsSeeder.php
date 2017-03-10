<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Permission;
use App\Bookmark;
use App\BookmarkItem;
use App\Policy;
use App\Role;
class BigbigadsSeeder extends Seeder
{
    public function insertPermissions($tableName, $list, $permissions, &$roles) 
    {
        foreach($list as $key=>$item) {
            $permision = Permission::firstOrCreate([
                'key'        => $item,
                'table_name' => $tableName
            ]);

            for ($i = 0; $i < count($roles); ++$i) {
                if ($permissions[$item][$i])
                    $roles[$i]->permissions()->attach($permision->id);
            }
        }

    }

    public function insertPolicies(&$policies, &$roles)
    {
        foreach($policies as $key=>$item) {
            $policy = Policy::firstOrCreate([
                'key'   => $key,
                'type'  => $item[0]
            ]);
            for ($i = 0; $i < count($roles); ++$i) {
                $roles[$i]->policies()->attach($policy->id, ['value'=>$item[$i+1]]);
            }
        }
    }

    /**
     * 注入广告分析权限
     */
    public function insertAdAnalysisPermissions(&$roles)
    {
        //广告分析
        $adAnalysis = ['analysis_overview', 'analysis_link', 'analysis_audience', 'analysis_trend', 'analysis_similar', 'ad_analysis_times_perday'];
        $adAnalysisPermission = ['analysis_overview' => [true, true, true, true], 'analysis_link' => [false, true, true, true], 'analysis_audience' => [false, true, true, true], 'analysis_trend' => [false, true, true, true], 'analysis_similar' => [false, true, true, true],'ad_analysis_times_perday' => [true, true, true, true]];
        $policies = ['ad_analysis_times_perday' => [Policy::DAY, 1000, 1000, 1000, 1000]];
        $this->insertPermissions('AdAnalysis', $adAnalysis, $adAnalysisPermission, $roles);
        $this->insertPolicies($policies, $roles);
        echo "insert analysis permissions \n";
    }

    /**
     * 注入广告主权限
     */
    public function insertAdsersPermissions(&$roles)
    {
        $advertiser = ['adser_search', 'adser_search_times_perday'];
        $advertiserPermission = ['adser_search'=>[false, false, false, true], 'adser_search_times_perday' => [true, true, true, true]];
        $policies = ['adser_search_times_perday' => [Policy::DAY, 1000, 1000, 1000, 1000]];
        $this->insertPermissions("advertiser", $advertiser, $advertiserPermission, $roles);
        $this->insertPolicies($policies, $roles);
        echo "insert advertisers permissions \n";
    }

    /**
     * 注入广告统计权限
     */
    public function insertAdStaticsPermissions(&$roles)
    {
        //广告分析
        $list = ['statics_overview', 'statics_link', 'statics_audience', 'statics_trend', 'statics_all'];
        $permission = ['statics_overview' => [true, true, true, true], 'statics_link' => [false, true, true, true], 'statics_audience' => [false, true, true, true], 'statics_trend' => [false, true, true, true], 'statics_all' => [false, true, true, true]];
        $this->insertPermissions('Keyword Statics', $list, $permission, $roles);
        echo "insert analysis permissions \n";
    }
    /**
     * Run the database seeds.
     * 1:永久累计 2:按月累计 3:按日累计 4.按小时累计 5.固定数值 6.期限
     * @return void
     */
    public function run()
    {
        try {
        //创建角色 
        $roleNames = ["Free" => "Free", "Standard"=>"Standard", "Advanced" => "Advanced", "Pro" => "Pro"];
        $roles = [];
        foreach($roleNames as $key=>$item) {
            array_push($roles, Role::firstOrCreate([
               'name' => $key,
               'display_name' => $item
            ]));
        }

        for ($i = 0; $i < count($roles); ++$i) {
            $roles[$i]->permissions()->detach();
            $roles[$i]->policies()->detach();
        }
        /* $this->info("Insert Roles"); */
        $role = Role::where('name', 'Standard')->first();
        //权限分配控制有与没有
        //策略分配控制多少
        /* $dashboard = ["online_users", "duration", "ad_date", "ad_update", "platform"]; */
        /* $dashboardPolicy = ['online_users' => [Policy::VALUE, 1, 1, 1, 3], "duration" => [Policy::VALUE, 5, 30, 30, 30], "ad_date" => [Policy::VALUE, 90, 180, 360, 0], "ad_update" => [Policy::VALUE, 'month', 'daily', 'daily', 'real time'], "platform" => [Policy::VALUE, 5, 5, 7, 7]]; */
        /* //dashboard permission */
        /* foreach($dashboard as $key=>$item) { */
        /*     $permision = Permission::firstOrCreate([ */
        /*         'key'        => $item, */
        /*         'table_name' => 'dashboard' */
        /*     ]); */
        /*     for ($i = 0; $i < count($roles); ++$i) { */
        /*         $roles[$i]->permissions()->attach($permision->id); */
        /*     } */
        /* } */
        /* foreach($dashboardPolicy as $key=>$item) { */
        /*     $policy = Policy::firstOrCreate([ */
        /*         'key'   => $key, */
        /*         'type'  => $item[0] */
        /*     ]); */
        /*     for ($i = 0; $i < count($roles); ++$i) { */
        /*         $roles[$i]->policies()->attach($policy->id, ['value'=>$item[$i+1]]); */
        /*     } */
        /* } */
        
        //广告搜索及策略 Search Permissions And Policies
        $search = ['search_times_perday', 'result_per_search', 'search_filter', 'search_sortby', 'advanced_search', 'save_search', 'keyword_times_perday', 
            'advertiser_search', 'dest_site_search', 'domain_search', 'content_search', 'audience_search', 
            'date_filter', 'format_filter', 'call_action_filter', 'duration_filter', 'see_times_filter', 'lang_filter', 'engagement_filter', 
            'date_sort', 'likes_sort','shares_sort',  'comment_sort', 'duration_sort', 'views_sort', 'engagements_sort', 'engagement_inc_sort', 'likes_inc_sort', 'views_inc_sort', 'shares_inc_sort', 'comments_inc_sort'];
        $searchPermission = ['search_times_perday'=>[true, true, true, true], 'result_per_search'=>[true, true, true, true], 'search_filter'=>[false, true, true, true], 'search_sortby'=>[false, false, true, true], 'advanced_search'=>[false, false, true, true], 'save_search' => [false, true, true, true], 'keyword_times_perday' => [true, true, true, true],
            'advertiser_search' => [false, true, true, true], 'dest_site_search' => [true, true, true, true], 'domain_search' => [true, true, true, true], 'content_search' => [true, true, true, true], 'audience_search' => [false, true, true, true], 
            'date_filter' => [true, true, true, true], 'format_filter' => [true, true, true, true], 'call_action_filter' => [false, true, true, true], 'duration_filter' => [false, true, true, true], 'see_times_filter' => [false, true, true, true], 'lang_filter' => [false, true, true, true], 'engagement_filter' => [false, true, true, true], 
            'date_sort' => [true, true, true, true], 'likes_sort' => [true, true, true, true], 'shares_sort' => [true, true, true, true],  'comment_sort' => [true, true, true, true], 'duration_sort'=>[false, true, true, true], 'views_sort' => [true, true, true, true], 'engagements_sort' => [false, true, true, true], 'engagement_inc_sort' => [false, true, true, true], 'likes_inc_sort' => [false, true, true, true], 'views_inc_sort' => [false, true, true, true], 'shares_inc_sort'=>[false, true, true, true], 'comments_inc_sort' => [false, true, true, true]];
        $searchPolicy = ['search_times_perday' => [Policy::DAY, 20,100, 500, 1000], 'result_per_search' => [Policy::VALUE, 500, 1000, 2000, 5000], 'keyword_times_perday' => [Policy::DAY, 1000, 1000, 1000, 1000]];
        foreach($search as $key=>$item) {
            $permision = Permission::firstOrCreate([
                'key'        => $item,
                'table_name' => 'Advertisement',
            ]);

            for ($i = 0; $i < count($roles); ++$i) {
                if ($searchPermission[$item][$i])
                    $roles[$i]->permissions()->attach($permision->id);
            }
        }
        foreach($searchPolicy as $key=>$item) {
            $policy = Policy::firstOrCreate([
                'key'   => $key,
                'type'  => $item[0]
            ]);
            for ($i = 0; $i < count($roles); ++$i) {
                $roles[$i]->policies()->attach($policy->id, ['value'=>$item[$i+1]]);
            }
        }
        //广告分析
        $this->insertAdAnalysisPermissions($roles);
        //广告统计
        $this->insertAdStaticsPermissions($roles);
        //广告主分析
        $this->insertAdsersPermissions($roles);
        //Export Permissions And Policies
        $export = ['image_download', 'video_download', 'HD_video_download', 'Export'];
        $exportPermission = ['image_download'=>[false, true,true,true], 'video_download'=>[false, true, true, true], 'HD_video_download'=>[false, false, true,true], 'Export'=>[false, false, true,true]];
        $exportPolicy = ['image_download'=>[Policy::DAY, 0, 100, 500, 1000], 'video_download'=>[Policy::DAY, 0, 100, 500, 1000], 'HD_video_download'=>[Policy::DAY, 0, 0, 100, 500], 'Export'=>[Policy::DAY, 0, 0, 10, 100]];
        foreach($export as $key=>$item) {
            $permision = Permission::firstOrCreate([
                'key'        => $item,
                'table_name' => 'export',
            ]);

            for ($i = 0; $i < count($roles); ++$i) {
                if ($exportPermission[$item][$i])
                    $roles[$i]->permissions()->attach($permision->id);
            }
        }
        
        foreach($exportPolicy as $key=>$item) {
            $policy = Policy::firstOrCreate([
                'key'   => $key,
                'type'  => $item[0]
            ]);
            for ($i = 0; $i < count($roles); ++$i) {
                $roles[$i]->policies()->attach($policy->id, ['value'=>$item[$i+1]]);
            }
        }

        $statics = ['search_statics', 'ad_analysis', 'adser_analysis', 'Realtime_AD_analysis'];
        $staticsPermission = ['search_statics'=>[false, true, true, true], 'ad_analysis'=>[false, true, true, true], 'adser_analysis'=>[false, true, true, true], 'Realtime_AD_analysis'=>[false, false, false, true]];
        foreach($statics as $key=>$item) {
            $permision = Permission::firstOrCreate([
                'key'        => $item,
                'table_name' => 'statics',
            ]);

            for ($i = 0; $i < count($roles); ++$i) {
                if ($staticsPermission[$item][$i])
                    $roles[$i]->permissions()->attach($permision->id);
            }
        }
        
        //排名权限与策略
        $ranking = ['ranking', 'ranking_export', 'ranking_by_category'];
        $rankingPermission = ['ranking'=>[false, false, true, true], 'ranking_export'=>[false, true, true, true], 'ranking_by_category'=>[false, false, true,true]];
        $rankingPolicy = ['ranking'=>[Policy::VALUE, 50, 100, 500, 10000], 'ranking_export'=>[Policy::VALUE, 0, 100, 500, 10000]];
        foreach($ranking as $key=>$item) {
            $permision = Permission::firstOrCreate([
                'key'        => $item,
                'table_name' => 'ranking',
            ]);

            for ($i = 0; $i < count($roles); ++$i) {
                if ($rankingPermission[$item][$i])
                    $roles[$i]->permissions()->attach($permision->id);
            }
        }

        foreach($rankingPolicy as $key=>$item) {
            $policy = Policy::firstOrCreate([
                'key'   => $key,
                'type'  => $item[0]
            ]);
            for ($i = 0; $i < count($roles); ++$i) {
                $roles[$i]->policies()->attach($policy->id, ['value'=>$item[$i+1]]);
            }
        }

        //收藏夹权限与策略
        $bookmark = ['bookmark_support', 'bookmark_list', 'bookmark_adser_support', 'save_count'];
        $bookmarkPermission = ['bookmark_support'=>[false, true, true, true], 'bookmark_list'=>[true, true, true, true], 'bookmark_adser_support'=>[false, true, true, true], 'save_count'=>[false, true, true, true]];
        $bookmarkPolicy = ['bookmark_list'=>[Policy::PERMANENT, 1, 10, 100, 10000], 'save_ad_count'=>[Policy::PERMANENT, 0, 100, 1000, 5000], 'save_count'=>[Policy::PERMANENT, 0, 100, 1000, 5000]];
        foreach($bookmark as $key=>$item) {
            $permision = Permission::firstOrCreate([
                'key'        => $item,
                'table_name' => 'bookmark',
            ]);

            for ($i = 0; $i < count($roles); ++$i) {
                if ($bookmarkPermission[$item][$i])
                    $roles[$i]->permissions()->attach($permision->id);
            }
        }
        foreach($bookmarkPolicy as $key=>$item) {
            $policy = Policy::firstOrCreate([
                'key'   => $key,
                'type'  => $item[0]
            ]);
            for ($i = 0; $i < count($roles); ++$i) {
                $roles[$i]->policies()->attach($policy->id, ['value'=>$item[$i+1]]);
            }
        }
        //moniter权限与策略
        $monitor = ['monitor_support', 'monitor_ad_keyword', 'monitor_advertiser'];
        $monitorPermission = ['monitor_support'=>[false, false, false, true], 'monitor_ad_keyword'=>[false, false, false, true], 'monitor_advertiser'=>[false, false, false, true]];
        $monitorPolicy = ['monitor_ad_keyword'=>[Policy::PERMANENT, 0, 0, 0, 20], 'monitor_advertiser'=>[Policy::PERMANENT, 0, 0, 0, 20]];
        foreach($monitor as $key=>$item) {
            $permision = Permission::firstOrCreate([
                'key'        => $item,
                'table_name' => 'monitor',
            ]);

            for ($i = 0; $i < count($roles); ++$i) {
                if ($monitorPermission[$item][$i])
                    $roles[$i]->permissions()->attach($permision->id);
            }
        }

        foreach($monitorPolicy as $key=>$item) {
            $policy = Policy::firstOrCreate([
                'key'   => $key,
                'type'  => $item[0]
            ]);
            for ($i = 0; $i < count($roles); ++$i) {
                $roles[$i]->policies()->attach($policy->id, ['value'=>$item[$i+1]]);
            }
        }
        echo "insert permission data\n";
    

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
