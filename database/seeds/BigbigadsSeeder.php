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
        $adAnalysisPermission = ['analysis_overview' => [true, true, true, true, true], 'analysis_link' => [false, false, true, true, false], 'analysis_audience' => [false, false, true, true, true], 'analysis_trend' => [false, false, true, true, false], 'analysis_similar' => [false, false, true, true, false],'ad_analysis_times_perday' => [true, true, true, true, true]];
        $policies = ['ad_analysis_times_perday' => [Policy::DAY, 1000, 1000, 1000, 1000, 1000]];
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
        $advertiserPermission = ['adser_search'=>[false, false, false, true, false], 'adser_search_times_perday' => [true, true, true, true, true]];
        $policies = ['adser_search_times_perday' => [Policy::DAY, 1000, 1000, 1000, 1000, 1000]];
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
        $permission = ['statics_overview' => [true, true, true, true, true], 'statics_link' => [false, true, true, true, true], 'statics_audience' => [false, true, true, true, true], 'statics_trend' => [false, true, true, true, true], 'statics_all' => [false, true, true, true, true]];
        $this->insertPermissions('Keyword Statics', $list, $permission, $roles);
        echo "insert analysis permissions \n";
    }

    /**
     * 权限列表被重新构建，后台管理员权限也应该重建
     */
    public function generateAdminPermissions()
    {
        $this->call(PermissionsTableSeeder::class);
        $this->call(PermissionRoleTableSeeder::class);
        echo "admin permissions generated\n";
    }
    /**
     * Run the database seeds.
     * 1:永久累计 2:按月累计 3:按日累计 4.按小时累计 5.固定数值 6.期限
     * @return void
     */
    public function run()
    {
        try {
            //创建角色,有几档权限（未登陆与第一档合用Free权限)就有几个角色，如果要增加角色。就扩展该数组即可。
            //需要注意的是，数组是[key=>value]形式。一旦执行填充命令，key部分就不能改，否则用户已经绑定的角色将失效。因此下面的Free,Standard,Advanced,Pro的key部分都不应该改动。
            $roleNames = ["Free" => "Free", "Standard"=>"Standard", "Advanced" => "Plus", "Pro" => "Premium", "OuterTester" => "OuterTester"];
            $roles = [];
            foreach($roleNames as $key=>$item) {
                if (Role::where('name', $key)->count() > 0) {
                    $role = Role::where('name', $key)->first();
                    $role->update(['display_name' => $item]);
                    $role->cleanCache();
                    array_push($roles, $role);
                } else {
                    array_push($roles, Role::create([
                        'name' => $key,
                        'display_name' => $item
                    ]));
                }
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
        //权限列表
        $search = ['search_times_perday', 'result_per_search', 'search_filter', 'search_sortby', 'advanced_search', 'save_search', 'keyword_times_perday', 
            'advertiser_search', 'dest_site_search', 'domain_search', 'content_search', 'audience_search', 
            'date_filter', 'format_filter', 'call_action_filter', 'duration_filter', 'see_times_filter', 'lang_filter', 'engagement_filter', 
            'date_sort', 'likes_sort','shares_sort',  'comment_sort', 'duration_sort', 'views_sort', 'engagements_sort', 'engagement_inc_sort', 'likes_inc_sort', 'views_inc_sort', 'shares_inc_sort', 'comments_inc_sort', 
            'timeline_filter', 'phone_filter', 'rightcolumn_filter', 'advance_likes_filter', 'advance_shares_filter', 'advance_comments_filter', 'advance_video_views_filter', 'advance_engagement_filter'];
        //将权限分配到角色，有多少角色，数组长度就有多少，与上面的角色一一对应。true表示该角色有该权限，false表示该角色无此权限。
        $searchPermission = ['search_times_perday'=>[true, true, true, true, true], 'result_per_search'=>[true, true, true, true, true], 'search_filter'=>[false, true, true, true, true], 'search_sortby'=>[false, false, true, true, false], 'advanced_search'=>[false, false, true, true, false], 'save_search' => [false, true, true, true, true], 'keyword_times_perday' => [true, true, true, true, true],
            'advertiser_search' => [true, true, true, true, true], 'dest_site_search' => [true, true, true, true, true], 'domain_search' => [true, true, true, true, true], 'content_search' => [true, true, true, true, true], 'audience_search' => [false, false, true, true, true], 
            'date_filter' => [true, true, true, true, true], 'format_filter' => [true, true, true, true, true], 'call_action_filter' => [false, true, true, true, true], 'duration_filter' => [false, false, true, true, true], 'see_times_filter' => [false, false, true, true, true], 'lang_filter' => [false, true, true, true, true], 'engagement_filter' => [false, false, true, true, false], 
            'date_sort' => [true, true, true, true, true], 'likes_sort' => [true, true, true, true, true], 'shares_sort' => [true, true, true, true, true],  'comment_sort' => [true, true, true, true, true], 'duration_sort'=>[false, false, true, true, true], 'views_sort' => [true, true, true, true, true], 'engagements_sort' => [false, false, true, true, false], 'engagement_inc_sort' => [false, false, true, true, false], 'likes_inc_sort' => [false, false, true, true, false], 'views_inc_sort' => [false, false, true, true, false], 'shares_inc_sort'=>[false, false, true, true, false], 'comments_inc_sort' => [false, false, true, true, false],
            'timeline_filter' => [true, true, true, true, true], 'phone_filter' => [false, false, true, true, false], 'rightcolumn_filter' => [true, true, true, true, true], 'advance_likes_filter' => [false, false, true, true, true], 'advance_shares_filter' => [false, false, true, true, true], 'advance_comments_filter' => [false, false, true, true, true], 'advance_video_views_filter' => [false, false, true, true, true], 'advance_engagement_filter' => [false, false, true, true, true]];
        //给权限指定策略，策略数组的第一个数值表示策略类型，Policy::DAY表示按天累计，Policy::VALUE表示是一个固定值，Policy::PERMANENT表示永久累计，后面数值同上。需要注意的是，只有角色有对应的权限，才会有检查策略。
        $searchPolicy = ['search_times_perday' => [Policy::DAY, 100, 1000, 3000, 5000, 1000], 'result_per_search' => [Policy::VALUE, 100, 300, 2000, 5000, 300], 'keyword_times_perday' => [Policy::DAY, 1000, 1000, 1000, 1000, 1000]];
        $this->insertPermissions('Advertisement', $search, $searchPermission,  $roles);
        $this->insertPolicies($searchPolicy, $roles);

        //广告分析
        $this->insertAdAnalysisPermissions($roles);
        //广告统计
        $this->insertAdStaticsPermissions($roles);
        //广告主分析
        $this->insertAdsersPermissions($roles);
        //Export Permissions And Policies
        
        $export = ['image_download', 'video_download', 'HD_video_download', 'Export'];
        $exportPermission = ['image_download'=>[false, true, true, true, true], 'video_download'=>[false, true, true, true, true], 'HD_video_download'=>[false, false, true, true, false], 'Export'=>[false, false, true, true, false]];
        $exportPolicy = ['image_download'=>[Policy::DAY, 0, 100, 500, 1000, 100], 'video_download'=>[Policy::DAY, 0, 100, 500, 1000, 100], 'HD_video_download'=>[Policy::DAY, 0, 0, 100, 500, 0], 'Export'=>[Policy::DAY, 0, 0, 10, 100, 0]];
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
        $staticsPermission = ['search_statics'=>[false, true, true, true, true], 'ad_analysis'=>[false, false, false, true, false], 'adser_analysis'=>[false, true, true, true, true], 'Realtime_AD_analysis'=>[false, false, false, true, false]];
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
        $rankingPermission = ['ranking'=>[false, false, true, true, false], 'ranking_export'=>[false, true, true, true, true], 'ranking_by_category'=>[false, false, true, true, false]];
        $rankingPolicy = ['ranking'=>[Policy::VALUE, 50, 100, 500, 10000, 100], 'ranking_export'=>[Policy::VALUE, 0, 100, 500, 10000, 100]];
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
        $bookmark = ['bookmark_support', 'bookmark_list', 'bookmark_adser_support', 'save_count','advertiser_collect'];
        $bookmarkPermission = ['bookmark_support'=>[false, true, true, true, true], 'bookmark_list'=>[true, true, true, true, true], 'bookmark_adser_support'=>[false, true, true, true, true], 'save_count'=>[false, true, true, true, true],'advertiser_collect'=>[false, false, true, true, false]];
        $bookmarkPolicy = ['bookmark_list'=>[Policy::PERMANENT, 1, 10, 100, 10000, 10], 'save_ad_count'=>[Policy::PERMANENT, 0, 35, 1000, 5000, 35], 'save_count'=>[Policy::PERMANENT, 0, 100, 1000, 5000, 100]];
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
        $monitorPermission = ['monitor_support'=>[false, false, false, true, false], 'monitor_ad_keyword'=>[false, false, false, true, false], 'monitor_advertiser'=>[false, false, false, true, false]];
        $monitorPolicy = ['monitor_ad_keyword'=>[Policy::PERMANENT, 0, 0, 0, 20, 0], 'monitor_advertiser'=>[Policy::PERMANENT, 0, 0, 0, 20, 0]];
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
    

            $this->generateAdminPermissions();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        //该部分应独立出来，但暂时不会编写种子填充的php artisan db:seed ，就先放到此处，这个日后教程做完补上
        $role = Role::where('name', '=', 'OuterTester')->first();
        if ($role instanceof Role) {
            $role->plan = 'standard';
            $role->save();
        }
    }
}
