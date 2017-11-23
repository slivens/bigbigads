<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Permission;
use App\Bookmark;
use App\BookmarkItem;
use App\Policy;
use App\Role;
use App\User;

class BigbigadsSeeder extends Seeder
{
    public function insertPermissions($tableName, $permissions, &$roles) 
    {
        foreach(array_keys($permissions) as $key=>$item) {
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
        $adAnalysisPermission = [
            'analysis_overview'         => [true,  true,  true,  true,  true,  true,  true],
            'analysis_link'             => [false, false, true,  true,  false, false, false],
            'analysis_audience'         => [false, false, true,  true,  true,  true,  false],
            'analysis_trend'            => [false, false, true,  true,  false, false, false],
            'analysis_similar'          => [false, false, true,  true,  false, true,  false],
            'ad_analysis_times_perday'  => [true,  true,  true,  true,  true,  true,  true]
        ];
        $policies = [
            'ad_analysis_times_perday'  => [Policy::DAY, 100, 600, 1000, 1000, 1000, 1000, 300]
        ];
        // $this->insertPermissions('AdAnalysis', $adAnalysis, $adAnalysisPermission, $roles);
        $this->insertPermissions('AdAnalysis', $adAnalysisPermission, $roles);
        $this->insertPolicies($policies, $roles);
        echo "insert analysis permissions \n";
    }

    /**
     * 注入广告主权限
     */
    public function insertAdsersPermissions(&$roles)
    {
        $advertiserPermission = [
            'adser_search'              => [false, false, false, true,  false, false, false],
            'adser_search_times_perday' => [true,  true,  true,  true,  true,  true,  true]
        ];
        $policies = [
            'adser_search_times_perday' => [Policy::DAY, 1000, 1000, 1000, 1000, 1000, 1000, 1000]
        ];
        // $this->insertPermissions("advertiser", $advertiser, $advertiserPermission, $roles);
        $this->insertPermissions("advertiser", $advertiserPermission, $roles);
        $this->insertPolicies($policies, $roles);
        echo "insert advertisers permissions \n";
    }

    /**
     * 注入广告统计权限
     */
    public function insertAdStaticsPermissions(&$roles)
    {
        //广告分析
        $permission = [
            'statics_overview'  => [true,  true,  true,  true,  true,  true,  true],
            'statics_link'      => [false, true,  true,  true,  true,  true,  true],
            'statics_audience'  => [false, true,  true,  true,  true,  true,  true],
            'statics_trend'     => [false, true,  true,  true,  true,  true,  true],
            'statics_all'       => [false, true,  true,  true,  true,  true,  true]
        ];
        // $this->insertPermissions('Keyword Statics', $list, $permission, $roles);
        $this->insertPermissions('Keyword Statics', $permission, $roles);
        echo "insert analysis permissions \n";
    }

    /**
     * 注入export权限
     */
    public function insertExportPermissions(&$roles)
    {
        $export = ['image_download', 'video_download', 'HD_video_download', 'Export'];
        $exportPermission = [
            'image_download'    => [false, true,  true,  true,  true,  true,  true],
            'video_download'    => [false, true,  true,  true,  true,  true,  true],
            'HD_video_download' => [false, false, true,  true,  false, false, true],
            'Export'            => [false, false, true,  true,  false, false, false]
        ];
        $exportPolicy = [
            'image_download'    => [Policy::DAY, 0, 100, 500, 1000, 100, 100, 100],
            'video_download'    => [Policy::DAY, 0, 100, 500, 1000, 100, 100, 100],
            'HD_video_download' => [Policy::DAY, 0, 0, 100, 500, 0, 0, 0],
            'Export'            => [Policy::DAY, 0, 0, 10, 100, 0, 0, 0]
        ];
        $this->insertPermissions('export', $exportPermission, $roles);
        $this->insertPolicies($exportPolicy, $roles);
    }

    public function insertStaticsPermissions(&$roles)
    {
        $staticsPermission = [
            'search_statics'        => [false, false, true,  true,  true,  true,  false],
            'ad_analysis'           => [false, false, false, true,  false, false, false],
            'adser_analysis'        => [false, false, true,  true,  true,  true,  false],
            'Realtime_AD_analysis'  => [false, false, false, true,  false, false, false]
        ];
        $this->insertPermissions('statics', $staticsPermission, $roles);
    }

    public function insertRankingPermissions(&$roles)
    {
        $rankingPermission = [
            'ranking'               => [false, false,  true,  true, false, false, false],
            'ranking_export'        => [false, false,  true,  true, true,  true,  false],
            'ranking_by_category'   => [false, false,  true,  true, false, false, false]
        ];
        $rankingPolicy = [
            'ranking'               => [Policy::VALUE, 50, 100, 500, 10000, 100, 100, 100],
            'ranking_export'        => [Policy::VALUE, 0, 100, 500, 10000, 100, 100, 100]
        ];
        $this->insertPermissions('ranking', $rankingPermission, $roles);
        $this->insertPolicies($rankingPolicy, $roles);
    }

    public function insertBookmarkPermissions(&$roles)
    {
        $bookmarkPermission = [
            'bookmark_support'          => [false, true,  true,  true,  true,  true,  true],
            'bookmark_list'             => [true,  true,  true,  true,  true,  true,  true],
            'bookmark_adser_support'    => [false, true,  true,  true,  true,  true,  true],
            'save_count'                => [false, true,  true,  true,  true,  true,  true],
            'advertiser_collect'        => [false, false, true,  true,  false, false, false],
            'bookmark_init_perday'      => [true,  true,  true,  true,  true,  true,  true],
            'bookmark_limit_perday'     => [true,  true,  true,  true,  true,  true,  true],
            'save_ad_count'             => [true,  true,  true,  true,  true,  true,  true]
        ];
        $bookmarkPolicy = [
            'bookmark_list'             => [Policy::PERMANENT, 1, 10, 5, 10, 10, 1, 5],
            'save_ad_count'             => [Policy::PERMANENT, 50, 500, 150, 5000, 500, 30, 250],
            'save_count'                => [Policy::PERMANENT, 0, 100, 1000, 5000, 100, 100, 100],
            'bookmark_init_perday'      => [Policy::DAY, 10000, 10000, 10000, 10000, 10000, 10000, 10000],
            'bookmark_limit_perday'     => [Policy::DAY, 10000, 10000, 10000, 10000, 10000, 10000, 10000]
        ];
        $this->insertPermissions('bookmark', $bookmarkPermission, $roles);
        $this->insertPolicies($bookmarkPolicy, $roles);
    }

    public function insertMonitorPermissions(&$roles)
    {
        $monitorPermission = [
            'monitor_support'       => [false, false, false,  true, false, false, false],
            'monitor_ad_keyword'    => [false, false, false,  true, false, false, false],
            'monitor_advertiser'    => [false, false, false,  true, false, false, false]
        ];
        $monitorPolicy = [
            'monitor_ad_keyword'    => [Policy::PERMANENT, 0, 0, 0, 20, 0, 0, 0],
            'monitor_advertiser'    => [Policy::PERMANENT, 0, 0, 0, 20, 0, 0, 0]
        ];
        $this->insertPermissions('monitor', $monitorPermission, $roles);
        $this->insertPolicies($monitorPolicy, $roles);
    }

    public function insertSearchPermissions(&$roles)
    {
        //将权限分配到角色，有多少角色，数组长度就有多少，与上面的角色一一对应。true表示该角色有该权限，false表示该角色无此权限.
        $searchPermission = [
            'search_times_perday'               => [true,  true,  true,  true,  true,  true,  true],
            'result_per_search'                 => [true,  true,  true,  true,  true,  true,  true],
            'search_filter'                     => [false, true,  true,  true,  true,  true,  true],
            'search_sortby'                     => [false, false, true,  true,  false, false, false],
            'advanced_search'                   => [false, false, true,  true,  false, false, false],
            'save_search'                       => [false, true,  true,  true,  true,  true,  true],
            'keyword_times_perday'              => [true,  true,  true,  true,  true,  true,  true],
            'advertiser_search'                 => [true,  true,  true,  true,  true,  true,  true],
            'dest_site_search'                  => [true,  true,  true,  true,  true,  true,  true],
            'domain_search'                     => [true,  true,  true,  true,  true,  true,  true],
            'content_search'                    => [true,  true,  true,  true,  true,  true,  true],
            'audience_search'                   => [false, true,  true,  true,  true,  true,  true],
            'date_filter'                       => [true,  true,  true,  true,  true,  true,  true],    // last see time frame
            'format_filter'                     => [false, true,  true,  true,  true,  true,  true],    // Ad type      ng-mode:formatSelected
            'call_action_filter'                => [true,  true,  true,  true,  true,  true,  true],    // call action filter   ng-mode:callToAction
            'duration_filter'                   => [false, true,  true,  true,  true,  true,  true],    // Customized Ad Run duration fliter
            'see_times_filter'                  => [false, true,  true,  true,  true,  true,  true],    // Customized Ad Run see_times
            'lang_filter'                       => [true,  true,  true,  true,  true,  true,  true],    // language filter
            'engagement_filter'                 => [false, true,  true,  true,  false, false, true],    // engagement filter
            'default_filter'                    => [true,  true,  true,  true,  true,  true,  true],    // sort by: Default
            'view_count_sort'                   => [false, false, true,  true,  true,  true,  false],   //          View Count
            'date_sort'                         => [true,  true,  true,  true,  true,  true,  true],    //          Last_Seen 
            'likes_sort'                        => [true,  true,  true,  true,  true,  true,  true],    //          Like
            'shares_sort'                       => [true,  true,  true,  true,  true,  true,  true],    //          Share
            'comment_sort'                      => [true,  true,  true,  true,  true,  true,  true],    //          Comment
            'duration_sort'                     => [false, true,  true,  true,  true,  true,  false],   //          Duration
            'views_sort'                        => [false, true,  true,  true,  true,  true,  false],   //          Views
            'engagements_sort'                  => [false, false, true,  true,  false, false, false],   //          Engagements
            'engagement_inc_sort'               => [false, false, true,  true,  false, false, false],   //          Engagements Growth
            'likes_inc_sort'                    => [false, false, true,  true,  false, false, false],   //          Likes Growth
            'views_inc_sort'                    => [false, false, true,  true,  false, false, false],   //          View Growth
            'shares_inc_sort'                   => [false, false, true,  true,  false, false, false],   //          Share Growth
            'comments_inc_sort'                 => [false, false, true,  true,  false, false, false],   //          Comments Growth
            'timeline_filter'                   => [false, true,  true,  true,  true,  true,  true],    // Ad Position: Newsfeed
            'phone_filter'                      => [false, true,  true,  true,  true,  true,  true],    //              Mobile
            'rightcolumn_filter'                => [false, true,  true,  true,  true,  true,  true],    //              Right Column
            'app_filter'                        => [false, true,  true,  true,  true,  true,  false],   //              App
            'advance_likes_filter'              => [false, true,  true,  true,  true,  true,  true],
            'advance_shares_filter'             => [false, true,  true,  true,  true,  true,  true],
            'advance_comments_filter'           => [false, true,  true,  true,  true,  true,  true],
            'advance_video_views_filter'        => [false, true,  true,  true,  true,  true,  true],
            'advance_engagement_filter'         => [false, true,  true,  true,  true,  true,  true],
            'search_init_perday'                => [true,  true,  true,  true,  true,  true,  true],
            'search_limit_keys_perday'          => [true,  true,  true,  true,  true,  true,  true],
            'search_limit_without_keys_perday'  => [true,  true,  true,  true,  true,  true,  true],
            'search_where_perday'               => [true,  true,  true,  true,  true,  true,  true],
            'specific_adser_init_perday'        => [true,  true,  true,  true,  true,  true,  true],
            'specific_adser_limit_perday'       => [true,  true,  true,  true,  true,  true,  true],
            'specific_adser_where_perday'       => [true,  true,  true,  true,  true,  true,  true],
            'search_total_times'                => [true,  true,  true,  true,  true,  true,  true],    // 累计搜索总数
            'country_filter'                    => [false, true,  true,  true,  true,  true,  false],    // country filter   ng-mode:state
            'emarketing_filter'                 => [false, true,  true,  true,  true,  true,  false],   // emarketing filter
            'tracking_filter'                   => [false, true,  true,  true,  true,  true,  false],   // tracking filter
            'affiliate_filter'                  => [false, true,  true,  true,  true,  true,  false],   // affiliate filter
            'e_commerce_filter'                 => [true,  true,  true,  true,  true,  true,  false],   // Eshop Platform: e_commerce filter
            'advance_filter'                    => [false, true,  true,  true,  true,  true,  true],    // advance filter
            'objective_filter'                  => [false, true,  true,  true,  true,  true,  true],    // objective filter
            'advance_audience_search'           => [false, true,  true,  true,  true,  true,  false],   // advance audience filter
            'audience_age_filter'               => [true,  true,  true,  true,  true,  true,  false],   // audience age
            'audience_gender_filter'            => [true,  true,  true,  true,  true,  true,  false],   // audience gender
            'search_mode_filter'                => [true,  true,  true,  true,  true,  true,  true],    // search mode      ng-mode:rangeselected
            'audience_interest_filter'          => [false, true,  true,  true,  true,  true,  false],   // audience interest
            'first_time_filter'                 => [false, true,  true,  true,  true,  true,  false],   // first time
            'last_time_filter'                  => [false, true,  true,  true,  true,  true,  false],   // last time
            'rang_filter'                       => [true,  true,  true,  true,  true,  true,  true],    // rang
            'search_without_key_total_perday'   => [true,  true,  true,  true,  true,  true,  true],    // 空词搜索请求每日统计
            'search_key_total_perday'           => [true,  true,  true,  true,  true,  true,  true],    // 非空词搜索请求每日统计
            'hot_search_times_perday'           => [true,  true,  true,  true,  true,  true,  true],    // 热词搜索请求每日统计
            'specific_adser_times_perday'       => [true,  true,  true,  true,  true,  true,  true],    // 热词搜索请求每日统计
        ];
        //给权限指定策略，策略数组的第一个数值表示策略类型，Policy::DAY表示按天累计，Policy::VALUE表示是一个固定值，Policy::PERMANENT表示永久累计，后面数值同上。需要注意的是，只有角色有对应的权限，才会有检查策略。
        $searchPolicy = [
            'search_times_perday'               => [Policy::DAY, 50, 300, 3000, 5000, 1000, 1000, 200],    // 非空词每日搜索请求数
            'result_per_search'                 => [Policy::VALUE, 100, 300, 2000, 5000, 300, 300, 300],
            'keyword_times_perday'              => [Policy::DAY, 1000, 1000, 1000, 1000, 1000, 1000, 1000],
            'search_init_perday'                => [Policy::DAY, 10000, 10000, 10000, 10000, 10000, 10000, 10000],
            'search_limit_keys_perday'          => [Policy::DAY, 10000, 10000, 10000, 10000, 10000, 10000, 10000],
            'search_limit_without_keys_perday'  => [Policy::DAY, 10000, 10000, 10000, 10000, 10000, 10000, 10000],
            'search_where_perday'               => [Policy::DAY, 10000, 10000, 10000, 10000, 10000, 10000, 10000],
            'specific_adser_init_perday'        => [Policy::DAY, 10000, 10000, 10000, 10000, 10000, 10000, 10000],
            'specific_adser_limit_perday'       => [Policy::DAY, 10000, 10000, 10000, 10000, 10000, 10000, 10000],
            'specific_adser_where_perday'       => [Policy::DAY, 10000, 10000, 10000, 10000, 10000, 10000, 10000],
            'search_total_times'                => [Policy::PERMANENT, 0, 0, 0, 0, 0, 0, 0],
            'search_without_key_total_perday'   => [Policy::DAY, 100, 600, 5000, 5000, 5000, 5000, 300],    // 空词每日请求总数
            'search_key_total_perday'           => [Policy::DAY, 100, 800, 5000, 5000, 5000, 5000, 500],    // 非空词每日请求总数
            'hot_search_times_perday'           => [Policy::DAY, 5000, 5000, 5000, 5000, 5000, 5000, 5000],
            'specific_adser_times_perday'       => [Policy::DAY, 100, 600, 5000, 5000, 5000, 5000, 300],   // 广告主下所有广告请求总数
        ];
        // $this->insertPermissions('Advertisement', $search, $searchPermission,  $roles);
        $this->insertPermissions('Advertisement', $searchPermission,  $roles);
        $this->insertPolicies($searchPolicy, $roles);
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
            $roleNames = [
                "Free"                  => "Free",
                "Standard"              => "Standard",
                "Advanced"              => "Plus",
                "Pro"                   => "Premium",
                "OuterTester"           => "OuterTester",
                "OuterTester_feishu"    => "OuterTester_feishu",
                "Lite"                  => "Lite"
            ];
            $roles = [];
            foreach($roleNames as $key=>$item) {
                if (Role::where('name', $key)->count() > 0) {
                    $role = Role::where('name', $key)->first();
                    $role->update(['display_name' => $item]);
                    /* $role->cleanCache(); */
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

            $this->insertSearchPermissions($roles);    
            //广告分析
            $this->insertAdAnalysisPermissions($roles);
            //广告统计
            $this->insertAdStaticsPermissions($roles);
            //广告主分析
 
            $this->insertAdsersPermissions($roles);
            //Export Permissions And Policies

            $this->insertExportPermissions($roles);

            $this->insertStaticsPermissions($roles);
            
            $this->insertRankingPermissions($roles);
            
            $this->insertBookmarkPermissions($roles);
            
            $this->insertMonitorPermissions($roles);
            
            echo "insert permission data\n";
            $this->generateAdminPermissions();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        $standard_role = ["OuterTester", "OuterTester_feishu", "Light"];
        foreach ($standard_role as $key) {
            $role = Role::where('name', '=', $key)->first();
            if ($role instanceof Role) {
                $role->plan = 'standard';
                $role->save();
            }
        }

        $this->command->getOutput()->writeln("<info>Generate cache for Roles and fix Users's usage</info>");
        // 重新为所有角色生成cache(必需的操作，用户只从缓存中读取权限数据)
        foreach ($roles as $role) {
            $role->generateCache();
        }
        // 对所有用户重新初始化它们的usage(只有真正变化的用户才会被更新，只更新初始化，它们的用量是保持的)
        User::chunk(100, function ($users) {
            foreach ($users as $user) {
                $user->reInitUsage();
            }
        });
    }
}
