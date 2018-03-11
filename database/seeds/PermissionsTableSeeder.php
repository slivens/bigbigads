<?php

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('permissions')->delete();
        
        \DB::table('permissions')->insert(array (
            0 => 
            array (
                'id' => 99,
                'key' => 'search_times_perday',
                'table_name' => 'Advertisement',
                'desc' => '非空词每日搜索请求数',
                'order' => 0,
                'type' => 0,
                'updated_at' => '2018-03-07 19:17:23',
            ),
            1 => 
            array (
                'id' => 100,
                'key' => 'result_per_search',
                'table_name' => 'Advertisement',
                'desc' => '每次搜索结果数量上限，最多显示xx个',
                'order' => 0,
                'type' => 0,
                'updated_at' => '2018-03-08 09:27:15',
            ),
            2 => 
            array (
                'id' => 101,
                'key' => 'search_filter',
                'table_name' => 'Advertisement',
                'desc' => '搜索过滤',
                'order' => 0,
                'type' => 0,
                'updated_at' => '2018-03-07 19:21:03',
            ),
            3 => 
            array (
                'id' => 102,
                'key' => 'search_sortby',
                'table_name' => 'Advertisement',
                'desc' => '搜索排序',
                'order' => 0,
                'type' => 0,
                'updated_at' => '2018-03-07 19:21:16',
            ),
            4 => 
            array (
                'id' => 103,
                'key' => 'advanced_search',
                'table_name' => 'Advertisement',
                'desc' => '进阶搜索/高级搜索',
                'order' => 0,
                'type' => 0,
                'updated_at' => '2018-03-07 19:21:53',
            ),
            5 => 
            array (
                'id' => 104,
                'key' => 'save_search',
                'table_name' => 'Advertisement',
                'desc' => '收藏夹搜索',
                'order' => 0,
                'type' => 0,
                'updated_at' => '2018-03-07 19:24:44',
            ),
            6 => 
            array (
                'id' => 105,
                'key' => 'advertiser_search',
                'table_name' => 'Advertisement',
                'desc' => '广告主搜索',
                'order' => 0,
                'type' => 0,
                'updated_at' => '2018-03-07 19:23:17',
            ),
            7 => 
            array (
                'id' => 106,
                'key' => 'dest_site_search',
                'table_name' => 'Advertisement',
                'desc' => NULL,
                'order' => 0,
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:19',
            ),
            8 => 
            array (
                'id' => 107,
                'key' => 'content_search',
                'table_name' => 'Advertisement',
                'desc' => '广告内容搜索',
                'order' => 0,
                'type' => 0,
                'updated_at' => '2018-03-07 19:24:16',
            ),
            9 => 
            array (
                'id' => 108,
                'key' => 'audience_search',
                'table_name' => 'Advertisement',
                'desc' => '受众搜索',
                'order' => 0,
                'type' => 0,
                'updated_at' => '2018-03-07 19:28:49',
            ),
            10 => 
            array (
                'id' => 109,
                'key' => 'date_filter',
                'table_name' => 'Advertisement',
                'desc' => '按日期过滤',
                'order' => 0,
                'type' => 0,
                'updated_at' => '2018-03-07 19:28:25',
            ),
            11 => 
            array (
                'id' => 110,
                'key' => 'format_filter',
                'table_name' => 'Advertisement',
            'desc' => '按广告样式/类型过滤（Ad type)',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:28:17',
        ),
        12 => 
        array (
            'id' => 111,
            'key' => 'duration_filter',
            'table_name' => 'Advertisement',
            'desc' => '按广告投放时间过滤',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:28:02',
        ),
        13 => 
        array (
            'id' => 112,
            'key' => 'see_times_filter',
            'table_name' => 'Advertisement',
            'desc' => '按查看次数过滤',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:29:05',
        ),
        14 => 
        array (
            'id' => 113,
            'key' => 'lang_filter',
            'table_name' => 'Advertisement',
            'desc' => '按语言类型过滤',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:29:18',
        ),
        15 => 
        array (
            'id' => 114,
            'key' => 'engagement_filter',
            'table_name' => 'Advertisement',
            'desc' => NULL,
            'order' => 0,
            'type' => 0,
            'updated_at' => '2017-03-06 01:08:19',
        ),
        16 => 
        array (
            'id' => 115,
            'key' => 'date_sort',
            'table_name' => 'Advertisement',
            'desc' => '按日期排序',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:31:20',
        ),
        17 => 
        array (
            'id' => 116,
            'key' => 'likes_sort',
            'table_name' => 'Advertisement',
            'desc' => '按点赞数量排序',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 16:05:18',
        ),
        18 => 
        array (
            'id' => 117,
            'key' => 'shares_sort',
            'table_name' => 'Advertisement',
            'desc' => '按分享数量排序',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 16:05:42',
        ),
        19 => 
        array (
            'id' => 118,
            'key' => 'video_views_sort',
            'table_name' => 'Advertisement',
            'desc' => '按视频查看次数排序',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:32:26',
        ),
        20 => 
        array (
            'id' => 119,
            'key' => 'comment_sort',
            'table_name' => 'Advertisement',
            'desc' => '按评论数排序',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:33:16',
        ),
        21 => 
        array (
            'id' => 120,
            'key' => 'duration_sort',
            'table_name' => 'Advertisement',
            'desc' => '按广告发布持续时间排序',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:33:57',
        ),
        22 => 
        array (
            'id' => 121,
            'key' => 'views_sort',
            'table_name' => 'Advertisement',
            'desc' => '按广告查看次数排序',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:34:11',
        ),
        23 => 
        array (
            'id' => 122,
            'key' => 'engagement_total_sort',
            'table_name' => 'Advertisement',
            'desc' => NULL,
            'order' => 0,
            'type' => 0,
            'updated_at' => '2017-03-06 01:08:20',
        ),
        24 => 
        array (
            'id' => 123,
            'key' => 'engagement_inc_sort',
            'table_name' => 'Advertisement',
            'desc' => NULL,
            'order' => 0,
            'type' => 0,
            'updated_at' => '2017-03-06 01:08:20',
        ),
        25 => 
        array (
            'id' => 124,
            'key' => 'likes_inc_sort',
            'table_name' => 'Advertisement',
            'desc' => '按点赞数升序排序',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 09:31:24',
        ),
        26 => 
        array (
            'id' => 125,
            'key' => 'video_views_inc_sort',
            'table_name' => 'Advertisement',
            'desc' => '按视频查看次数升序排序',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 09:31:42',
        ),
        27 => 
        array (
            'id' => 126,
            'key' => 'image_download',
            'table_name' => 'export',
            'desc' => '广告图片下载',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:37:50',
        ),
        28 => 
        array (
            'id' => 127,
            'key' => 'video_download',
            'table_name' => 'export',
            'desc' => '广告视频下载',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:38:05',
        ),
        29 => 
        array (
            'id' => 128,
            'key' => 'HD_video_download',
            'table_name' => 'export',
            'desc' => '高清视频下载',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:38:19',
        ),
        30 => 
        array (
            'id' => 129,
            'key' => 'Export',
            'table_name' => 'export',
        'desc' => '导出(广告数据)',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 09:32:09',
        ),
        31 => 
        array (
            'id' => 130,
            'key' => 'search_statics',
            'table_name' => 'statics',
            'desc' => NULL,
            'order' => 0,
            'type' => 0,
            'updated_at' => '2017-03-06 01:08:20',
        ),
        32 => 
        array (
            'id' => 131,
            'key' => 'ad_analysis',
            'table_name' => 'statics',
            'desc' => '广告分析',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:40:10',
        ),
        33 => 
        array (
            'id' => 132,
            'key' => 'adser_analysis',
            'table_name' => 'statics',
            'desc' => '广告主分析',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:40:22',
        ),
        34 => 
        array (
            'id' => 133,
            'key' => 'Realtime_AD_analysis',
            'table_name' => 'statics',
            'desc' => '实时广告分析',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:40:46',
        ),
        35 => 
        array (
            'id' => 134,
            'key' => 'ranking',
            'table_name' => 'ranking',
            'desc' => '广告排行',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:41:30',
        ),
        36 => 
        array (
            'id' => 135,
            'key' => 'ranking_export',
            'table_name' => 'ranking',
            'desc' => '广告排行导出',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:41:47',
        ),
        37 => 
        array (
            'id' => 136,
            'key' => 'ranking_by_category',
            'table_name' => 'ranking',
            'desc' => '按分类排行',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:42:01',
        ),
        38 => 
        array (
            'id' => 137,
            'key' => 'bookmark_support',
            'table_name' => 'bookmark',
            'desc' => '收藏夹使用权限（收藏夹支持？）',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:43:07',
        ),
        39 => 
        array (
            'id' => 138,
            'key' => 'bookmark_list',
            'table_name' => 'bookmark',
            'desc' => '收藏夹列表显示',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:43:29',
        ),
        40 => 
        array (
            'id' => 139,
            'key' => 'bookmark_adser_support',
            'table_name' => 'bookmark',
            'desc' => '广告主收藏夹使用权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:44:17',
        ),
        41 => 
        array (
            'id' => 140,
            'key' => 'save_count',
            'table_name' => 'bookmark',
            'desc' => '广告收藏数量上限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:45:01',
        ),
        42 => 
        array (
            'id' => 141,
            'key' => 'monitor_support',
            'table_name' => 'monitor',
            'desc' => '监听权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:45:55',
        ),
        43 => 
        array (
            'id' => 142,
            'key' => 'monitor_ad_keyword',
            'table_name' => 'monitor',
            'desc' => '广告关键词监听',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:56:44',
        ),
        44 => 
        array (
            'id' => 143,
            'key' => 'monitor_advertiser',
            'table_name' => 'monitor',
            'desc' => '广告主监听',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:57:04',
        ),
        45 => 
        array (
            'id' => 144,
            'key' => 'call_action_filter',
            'table_name' => 'Advertisement',
            'desc' => '调用操作过滤',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:02:02',
        ),
        46 => 
        array (
            'id' => 145,
            'key' => 'advertiser_search',
            'table_name' => 'advertiser',
            'desc' => '广告主搜索',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:03:23',
        ),
        47 => 
        array (
            'id' => 146,
            'key' => 'domain_search',
            'table_name' => 'Advertisement',
            'desc' => '按域名查询',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:06:57',
        ),
        48 => 
        array (
            'id' => 147,
            'key' => 'engagements_sort',
            'table_name' => 'Advertisement',
            'desc' => NULL,
            'order' => 0,
            'type' => 0,
            'updated_at' => '2017-03-06 13:05:13',
        ),
        49 => 
        array (
            'id' => 148,
            'key' => 'views_inc_sort',
            'table_name' => 'Advertisement',
            'desc' => '按广告查看次数升序排序',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 09:33:49',
        ),
        50 => 
        array (
            'id' => 149,
            'key' => 'shares_inc_sort',
            'table_name' => 'Advertisement',
            'desc' => '按广告分享数量升序排序',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 09:34:11',
        ),
        51 => 
        array (
            'id' => 150,
            'key' => 'comments_inc_sort',
            'table_name' => 'Advertisement',
            'desc' => '按评论升序排序',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:11:06',
        ),
        52 => 
        array (
            'id' => 151,
            'key' => 'analysis_overview',
            'table_name' => 'AdAnalysis',
            'desc' => '广告分析总览',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:11:39',
        ),
        53 => 
        array (
            'id' => 152,
            'key' => 'analysis_link',
            'table_name' => 'AdAnalysis',
            'desc' => '广告链接分析',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:14:43',
        ),
        54 => 
        array (
            'id' => 153,
            'key' => 'analysis_audience',
            'table_name' => 'AdAnalysis',
            'desc' => '广告受众分析',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:15:03',
        ),
        55 => 
        array (
            'id' => 154,
            'key' => 'analysis_trend',
            'table_name' => 'AdAnalysis',
            'desc' => '广告趋势分析',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:15:24',
        ),
        56 => 
        array (
            'id' => 155,
            'key' => 'analysis_similar',
            'table_name' => 'AdAnalysis',
            'desc' => '广告相似性分析',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:17:27',
        ),
        57 => 
        array (
            'id' => 156,
            'key' => 'statics_overview',
            'table_name' => 'Keyword Statics',
            'desc' => NULL,
            'order' => 0,
            'type' => 0,
            'updated_at' => '2017-03-06 14:32:43',
        ),
        58 => 
        array (
            'id' => 157,
            'key' => 'statics_link',
            'table_name' => 'Keyword Statics',
            'desc' => NULL,
            'order' => 0,
            'type' => 0,
            'updated_at' => '2017-03-06 14:32:43',
        ),
        59 => 
        array (
            'id' => 158,
            'key' => 'statics_audience',
            'table_name' => 'Keyword Statics',
            'desc' => NULL,
            'order' => 0,
            'type' => 0,
            'updated_at' => '2017-03-06 14:32:43',
        ),
        60 => 
        array (
            'id' => 159,
            'key' => 'statics_trend',
            'table_name' => 'Keyword Statics',
            'desc' => NULL,
            'order' => 0,
            'type' => 0,
            'updated_at' => '2017-03-06 14:32:43',
        ),
        61 => 
        array (
            'id' => 160,
            'key' => 'statics_all',
            'table_name' => 'Keyword Statics',
            'desc' => NULL,
            'order' => 0,
            'type' => 0,
            'updated_at' => '2017-03-06 14:32:43',
        ),
        62 => 
        array (
            'id' => 161,
            'key' => 'adser_search',
            'table_name' => 'advertiser',
            'desc' => '广告主搜索',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:17:06',
        ),
        63 => 
        array (
            'id' => 162,
            'key' => 'keyword_times_perday',
            'table_name' => 'Advertisement',
            'desc' => '每日关键词搜索次数上限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:16:30',
        ),
        64 => 
        array (
            'id' => 163,
            'key' => 'ad_analysis_times_perday',
            'table_name' => 'AdAnalysis',
            'desc' => '每日广告分析次数上限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:16:48',
        ),
        65 => 
        array (
            'id' => 164,
            'key' => 'adser_search_times_perday',
            'table_name' => 'advertiser',
            'desc' => '每日广告主搜索次数上限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:18:18',
        ),
        66 => 
        array (
            'id' => 165,
            'key' => 'advertiser_collect',
            'table_name' => 'bookmark',
            'desc' => '广告主收藏权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:18:41',
        ),
        67 => 
        array (
            'id' => 166,
            'key' => 'browse_admin',
            'table_name' => 'admin',
            'desc' => '管理模块访问权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 09:35:08',
        ),
        68 => 
        array (
            'id' => 167,
            'key' => 'browse_database',
            'table_name' => 'admin',
            'desc' => '数据库模块访问权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 09:35:20',
        ),
        69 => 
        array (
            'id' => 168,
            'key' => 'browse_media',
            'table_name' => 'admin',
            'desc' => '媒体库模块访问权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 09:35:28',
        ),
        70 => 
        array (
            'id' => 169,
            'key' => 'browse_settings',
            'table_name' => 'admin',
            'desc' => '设置模块访问权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 09:35:40',
        ),
        71 => 
        array (
            'id' => 170,
            'key' => 'browse_menus',
            'table_name' => 'menus',
            'desc' => '菜单模块访问权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 09:35:53',
        ),
        72 => 
        array (
            'id' => 171,
            'key' => 'read_menus',
            'table_name' => 'menus',
            'desc' => '读取菜单权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:20:52',
        ),
        73 => 
        array (
            'id' => 172,
            'key' => 'edit_menus',
            'table_name' => 'menus',
            'desc' => '编辑菜单权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:21:07',
        ),
        74 => 
        array (
            'id' => 173,
            'key' => 'add_menus',
            'table_name' => 'menus',
            'desc' => '新增菜单权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:21:20',
        ),
        75 => 
        array (
            'id' => 174,
            'key' => 'delete_menus',
            'table_name' => 'menus',
            'desc' => '删除菜单权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:21:32',
        ),
        76 => 
        array (
            'id' => 175,
            'key' => 'browse_pages',
            'table_name' => 'pages',
            'desc' => '页面模块访问权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 09:36:06',
        ),
        77 => 
        array (
            'id' => 176,
            'key' => 'read_pages',
            'table_name' => 'pages',
            'desc' => '页面读取权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:22:06',
        ),
        78 => 
        array (
            'id' => 177,
            'key' => 'edit_pages',
            'table_name' => 'pages',
            'desc' => '页面编辑权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:22:25',
        ),
        79 => 
        array (
            'id' => 178,
            'key' => 'add_pages',
            'table_name' => 'pages',
            'desc' => '新增页面权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:22:36',
        ),
        80 => 
        array (
            'id' => 179,
            'key' => 'delete_pages',
            'table_name' => 'pages',
            'desc' => '删除页面权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:22:53',
        ),
        81 => 
        array (
            'id' => 180,
            'key' => 'browse_roles',
            'table_name' => 'roles',
            'desc' => '角色模块访问权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:28:37',
        ),
        82 => 
        array (
            'id' => 181,
            'key' => 'read_roles',
            'table_name' => 'roles',
            'desc' => '读取角色权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:25:35',
        ),
        83 => 
        array (
            'id' => 182,
            'key' => 'edit_roles',
            'table_name' => 'roles',
            'desc' => '编辑角色权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:25:46',
        ),
        84 => 
        array (
            'id' => 183,
            'key' => 'add_roles',
            'table_name' => 'roles',
            'desc' => '新增角色权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:24:47',
        ),
        85 => 
        array (
            'id' => 184,
            'key' => 'delete_roles',
            'table_name' => 'roles',
            'desc' => '删除角色权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:24:59',
        ),
        86 => 
        array (
            'id' => 185,
            'key' => 'browse_users',
            'table_name' => 'users',
            'desc' => '用户模块访问权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:26:21',
        ),
        87 => 
        array (
            'id' => 186,
            'key' => 'read_users',
            'table_name' => 'users',
            'desc' => '读取用户权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:26:32',
        ),
        88 => 
        array (
            'id' => 187,
            'key' => 'edit_users',
            'table_name' => 'users',
            'desc' => '编辑用户权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:26:44',
        ),
        89 => 
        array (
            'id' => 188,
            'key' => 'add_users',
            'table_name' => 'users',
            'desc' => '新增用户权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:26:55',
        ),
        90 => 
        array (
            'id' => 189,
            'key' => 'delete_users',
            'table_name' => 'users',
            'desc' => '删除用户权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:27:08',
        ),
        91 => 
        array (
            'id' => 190,
            'key' => 'browse_posts',
            'table_name' => 'posts',
            'desc' => '文章模块访问权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:27:23',
        ),
        92 => 
        array (
            'id' => 191,
            'key' => 'read_posts',
            'table_name' => 'posts',
            'desc' => '读取文章权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:27:33',
        ),
        93 => 
        array (
            'id' => 192,
            'key' => 'edit_posts',
            'table_name' => 'posts',
            'desc' => '编辑文章权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:27:49',
        ),
        94 => 
        array (
            'id' => 193,
            'key' => 'add_posts',
            'table_name' => 'posts',
            'desc' => '新增文章权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:28:00',
        ),
        95 => 
        array (
            'id' => 194,
            'key' => 'delete_posts',
            'table_name' => 'posts',
            'desc' => '删除文章权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:29:02',
        ),
        96 => 
        array (
            'id' => 195,
            'key' => 'browse_categories',
            'table_name' => 'categories',
            'desc' => '分类模块访问权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:29:16',
        ),
        97 => 
        array (
            'id' => 196,
            'key' => 'read_categories',
            'table_name' => 'categories',
            'desc' => '读取分类权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:29:29',
        ),
        98 => 
        array (
            'id' => 197,
            'key' => 'edit_categories',
            'table_name' => 'categories',
            'desc' => '编辑分类权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:29:42',
        ),
        99 => 
        array (
            'id' => 198,
            'key' => 'add_categories',
            'table_name' => 'categories',
            'desc' => '新增分类权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:29:53',
        ),
        100 => 
        array (
            'id' => 199,
            'key' => 'delete_categories',
            'table_name' => 'categories',
            'desc' => '删除分类权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:30:05',
        ),
        101 => 
        array (
            'id' => 200,
            'key' => 'timeline_filter',
            'table_name' => 'Advertisement',
            'desc' => '按时间线过滤',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:30:22',
        ),
        102 => 
        array (
            'id' => 201,
            'key' => 'phone_filter',
            'table_name' => 'Advertisement',
            'desc' => '按电话号码过滤',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:30:36',
        ),
        103 => 
        array (
            'id' => 202,
            'key' => 'rightcolumn_filter',
            'table_name' => 'Advertisement',
        'desc' => '按右列过滤(?)',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:31:51',
        ),
        104 => 
        array (
            'id' => 203,
            'key' => 'advance_likes_filter',
            'table_name' => 'Advertisement',
            'desc' => '高级搜索-按点赞数过滤',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:33:23',
        ),
        105 => 
        array (
            'id' => 204,
            'key' => 'advance_shares_filter',
            'table_name' => 'Advertisement',
            'desc' => '高级搜索-按分享数过滤',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:33:37',
        ),
        106 => 
        array (
            'id' => 205,
            'key' => 'advance_comments_filter',
            'table_name' => 'Advertisement',
            'desc' => '高级搜索-按评论数过滤',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:33:09',
        ),
        107 => 
        array (
            'id' => 206,
            'key' => 'advance_video_views_filter',
            'table_name' => 'Advertisement',
            'desc' => '高级搜索-按视频播放次数过滤',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:33:55',
        ),
        108 => 
        array (
            'id' => 207,
            'key' => 'advance_engagement_filter',
            'table_name' => 'Advertisement',
            'desc' => '',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 09:36:55',
        ),
        109 => 
        array (
            'id' => 208,
            'key' => 'browse_maillist',
            'table_name' => 'maillist',
            'desc' => '市场营销-邮件列表模块访问权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 16:07:00',
        ),
        110 => 
        array (
            'id' => 209,
            'key' => 'read_maillist',
            'table_name' => 'maillist',
            'desc' => '读取邮件列表权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:34:51',
        ),
        111 => 
        array (
            'id' => 210,
            'key' => 'edit_maillist',
            'table_name' => 'maillist',
            'desc' => '编辑邮件列表权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:35:02',
        ),
        112 => 
        array (
            'id' => 211,
            'key' => 'add_maillist',
            'table_name' => 'maillist',
            'desc' => '新增邮件列表权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:35:15',
        ),
        113 => 
        array (
            'id' => 212,
            'key' => 'delete_maillist',
            'table_name' => 'maillist',
            'desc' => '删除邮件列表权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:35:27',
        ),
        114 => 
        array (
            'id' => 213,
            'key' => 'browse_affiliates',
            'table_name' => 'affiliates',
            'desc' => '市场营销-联盟会员模块访问权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 16:07:27',
        ),
        115 => 
        array (
            'id' => 214,
            'key' => 'read_affiliates',
            'table_name' => 'affiliates',
            'desc' => '读取联盟会员权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:37:32',
        ),
        116 => 
        array (
            'id' => 215,
            'key' => 'edit_affiliates',
            'table_name' => 'affiliates',
            'desc' => '编辑联盟会员权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:37:55',
        ),
        117 => 
        array (
            'id' => 216,
            'key' => 'add_affiliates',
            'table_name' => 'affiliates',
            'desc' => '新增联盟会员权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:38:07',
        ),
        118 => 
        array (
            'id' => 217,
            'key' => 'delete_affiliates',
            'table_name' => 'affiliates',
            'desc' => '删除联盟会员权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:38:20',
        ),
        119 => 
        array (
            'id' => 223,
            'key' => 'browse_coupons',
            'table_name' => 'coupons',
            'desc' => '市场营销-优惠券模块访问权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 16:07:48',
        ),
        120 => 
        array (
            'id' => 224,
            'key' => 'read_coupons',
            'table_name' => 'coupons',
            'desc' => '读取优惠券权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:38:51',
        ),
        121 => 
        array (
            'id' => 225,
            'key' => 'edit_coupons',
            'table_name' => 'coupons',
            'desc' => '编辑优惠券权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:39:04',
        ),
        122 => 
        array (
            'id' => 226,
            'key' => 'add_coupons',
            'table_name' => 'coupons',
            'desc' => '新增优惠券权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:39:14',
        ),
        123 => 
        array (
            'id' => 227,
            'key' => 'delete_coupons',
            'table_name' => 'coupons',
            'desc' => '删除优惠券权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:39:24',
        ),
        124 => 
        array (
            'id' => 228,
            'key' => 'search_init_perday',
            'table_name' => 'Advertisement',
            'desc' => '搜索页面初始化每日统计',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:40:27',
        ),
        125 => 
        array (
            'id' => 229,
            'key' => 'search_limit_perday',
            'table_name' => 'Advertisement',
            'desc' => '每日查询上限，发出搜索请求就算有效操作',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 09:42:15',
        ),
        126 => 
        array (
            'id' => 230,
            'key' => 'search_where_perday',
            'table_name' => 'Advertisement',
            'desc' => '每日条件查询上限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:44:07',
        ),
        127 => 
        array (
            'id' => 231,
            'key' => 'specific_adser_init_perday',
            'table_name' => 'Advertisement',
            'desc' => '广告主详情页面初始化每日统计',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:45:44',
        ),
        128 => 
        array (
            'id' => 232,
            'key' => 'specific_adser_limit_perday',
            'table_name' => 'Advertisement',
            'desc' => '广告主详情页面每日搜索上限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:46:52',
        ),
        129 => 
        array (
            'id' => 233,
            'key' => 'specific_adser_where_perday',
            'table_name' => 'Advertisement',
            'desc' => '广告主详情页面每日条件查询数量上限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:47:16',
        ),
        130 => 
        array (
            'id' => 234,
            'key' => 'app_filter',
            'table_name' => 'Advertisement',
            'desc' => '按应用过滤',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:47:51',
        ),
        131 => 
        array (
            'id' => 235,
            'key' => 'search_total_times',
            'table_name' => 'Advertisement',
            'desc' => '总搜索次数，永久累计',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 09:44:26',
        ),
        132 => 
        array (
            'id' => 236,
            'key' => 'country_filter',
            'table_name' => 'Advertisement',
            'desc' => '按国家过滤',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:48:26',
        ),
        133 => 
        array (
            'id' => 237,
            'key' => 'emarketing_filter',
            'table_name' => 'Advertisement',
        'desc' => '按网络营销(类型?)过滤',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:57:40',
        ),
        134 => 
        array (
            'id' => 238,
            'key' => 'tracking_filter',
            'table_name' => 'Advertisement',
            'desc' => '按推广码过滤',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 09:49:23',
        ),
        135 => 
        array (
            'id' => 239,
            'key' => 'affiliate_filter',
            'table_name' => 'Advertisement',
            'desc' => '按联盟会员类型过滤',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:59:13',
        ),
        136 => 
        array (
            'id' => 240,
            'key' => 'e_commerceList_filter',
            'table_name' => 'Advertisement',
            'desc' => '按电商列表过滤',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 20:59:48',
        ),
        137 => 
        array (
            'id' => 241,
            'key' => 'advance_filter',
            'table_name' => 'Advertisement',
            'desc' => '高级过滤权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 21:00:01',
        ),
        138 => 
        array (
            'id' => 242,
            'key' => 'bookmark_init_perday',
            'table_name' => 'bookmark',
            'desc' => '收藏夹每日收藏初始化',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 21:00:49',
        ),
        139 => 
        array (
            'id' => 243,
            'key' => 'bookmark_limit_perday',
            'table_name' => 'bookmark',
            'desc' => '收藏夹每日收藏上限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 21:00:59',
        ),
        140 => 
        array (
            'id' => 244,
            'key' => 'e_commerce_filter',
            'table_name' => 'Advertisement',
            'desc' => '按电子商务过滤',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 21:01:23',
        ),
        141 => 
        array (
            'id' => 245,
            'key' => 'objective_filter',
            'table_name' => 'Advertisement',
            'desc' => NULL,
            'order' => 0,
            'type' => 0,
            'updated_at' => '2017-08-14 02:37:21',
        ),
        142 => 
        array (
            'id' => 246,
            'key' => 'advance_audience_search',
            'table_name' => 'Advertisement',
            'desc' => '高级搜索-受众查询',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 21:02:35',
        ),
        143 => 
        array (
            'id' => 247,
            'key' => 'audience_age_filter',
            'table_name' => 'Advertisement',
            'desc' => '按受众年龄过滤',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 21:03:50',
        ),
        144 => 
        array (
            'id' => 248,
            'key' => 'audience_gender_filter',
            'table_name' => 'Advertisement',
            'desc' => '按受众性别过滤',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 21:03:34',
        ),
        145 => 
        array (
            'id' => 249,
            'key' => 'browse_refunds',
            'table_name' => 'refunds',
            'desc' => '退款申请单模块访问权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 16:10:57',
        ),
        146 => 
        array (
            'id' => 250,
            'key' => 'read_refunds',
            'table_name' => 'refunds',
            'desc' => '读取退款申请单权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 16:11:10',
        ),
        147 => 
        array (
            'id' => 251,
            'key' => 'edit_refunds',
            'table_name' => 'refunds',
            'desc' => '修改退款申请单权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 16:11:59',
        ),
        148 => 
        array (
            'id' => 252,
            'key' => 'add_refunds',
            'table_name' => 'refunds',
            'desc' => '新增退款申请单权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 16:12:09',
        ),
        149 => 
        array (
            'id' => 253,
            'key' => 'delete_refunds',
            'table_name' => 'refunds',
            'desc' => '删除退款申请单权限，删除后买家可以再次发起退款申请',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 16:12:29',
        ),
        150 => 
        array (
            'id' => 254,
            'key' => 'audience_interest_filter',
            'table_name' => 'Advertisement',
            'desc' => '按受众兴趣点过滤',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 21:05:37',
        ),
        151 => 
        array (
            'id' => 255,
            'key' => 'search_mode_filter',
            'table_name' => 'Advertisement',
            'desc' => '按搜索模式过滤',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 21:05:51',
        ),
        152 => 
        array (
            'id' => 256,
            'key' => 'first_time_filter',
            'table_name' => 'Advertisement',
            'desc' => '按最初投放时间过滤',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 09:20:29',
        ),
        153 => 
        array (
            'id' => 257,
            'key' => 'last_time_filter',
            'table_name' => 'Advertisement',
            'desc' => '按最后投放时间过滤',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 09:20:45',
        ),
        154 => 
        array (
            'id' => 258,
            'key' => 'rang_filter',
            'table_name' => 'Advertisement',
            'desc' => '按rang过滤',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 17:26:58',
        ),
        155 => 
        array (
            'id' => 259,
            'key' => 'save_ad_count',
            'table_name' => 'bookmark',
            'desc' => '广告收藏总数，指账号上收藏的广告的总数',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 09:21:19',
        ),
        156 => 
        array (
            'id' => 260,
            'key' => 'search_limit_keys_perday',
            'table_name' => 'Advertisement',
            'desc' => '非空词每日查询上限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 17:25:39',
        ),
        157 => 
        array (
            'id' => 261,
            'key' => 'search_limit_without_keys_perday',
            'table_name' => 'Advertisement',
            'desc' => '空词每日查询上限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 17:25:51',
        ),
        158 => 
        array (
            'id' => 262,
            'key' => 'search_without_key_total_perday',
            'table_name' => 'Advertisement',
            'desc' => '空词每日请求总数',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 17:25:11',
        ),
        159 => 
        array (
            'id' => 263,
            'key' => 'search_key_total_perday',
            'table_name' => 'Advertisement',
            'desc' => '非空词每日请求总数',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 17:24:48',
        ),
        160 => 
        array (
            'id' => 264,
            'key' => 'hot_search_times_perday',
            'table_name' => 'Advertisement',
            'desc' => '热词每日查询上限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 17:26:12',
        ),
        161 => 
        array (
            'id' => 265,
            'key' => 'specific_adser_times_perday',
            'table_name' => 'Advertisement',
            'desc' => '特定广告主搜索请求每日统计',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 17:27:55',
        ),
        162 => 
        array (
            'id' => 266,
            'key' => 'view_count_sort',
            'table_name' => 'Advertisement',
            'desc' => '按查看总数排序',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 16:15:42',
        ),
        163 => 
        array (
            'id' => 267,
            'key' => 'default_filter',
            'table_name' => 'Advertisement',
            'desc' => '默认过滤',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 09:21:49',
        ),
        164 => 
        array (
            'id' => 268,
            'key' => 'browse_permissions',
            'table_name' => 'permissions',
            'desc' => '浏览权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 17:31:36',
        ),
        165 => 
        array (
            'id' => 269,
            'key' => 'read_permissions',
            'table_name' => 'permissions',
            'desc' => '读取权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 17:31:50',
        ),
        166 => 
        array (
            'id' => 270,
            'key' => 'edit_permissions',
            'table_name' => 'permissions',
            'desc' => '写入权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 17:31:59',
        ),
        167 => 
        array (
            'id' => 271,
            'key' => 'add_permissions',
            'table_name' => 'permissions',
            'desc' => '新增权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:12:16',
        ),
        168 => 
        array (
            'id' => 272,
            'key' => 'delete_permissions',
            'table_name' => 'permissions',
            'desc' => '删除权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 17:32:29',
        ),
        169 => 
        array (
            'id' => 273,
            'key' => 'adser_without_key_total_perday',
            'table_name' => 'advertiser',
            'desc' => '广告主空词每日请求总数',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 17:33:36',
        ),
        170 => 
        array (
            'id' => 274,
            'key' => 'adser_key_total_perday',
            'table_name' => 'advertiser',
            'desc' => '广告主非空词每日请求总数',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 17:33:56',
        ),
        171 => 
        array (
            'id' => 275,
            'key' => 'adser_limit_keys_perday',
            'table_name' => 'advertiser',
            'desc' => '广告主非空词下拉请求每日统计',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 17:34:19',
        ),
        172 => 
        array (
            'id' => 276,
            'key' => 'adser_limit_without_keys_perday',
            'table_name' => 'advertiser',
            'desc' => '广告主空词下拉请求每日统计',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 17:34:38',
        ),
        173 => 
        array (
            'id' => 277,
            'key' => 'adser_result_per_search',
            'table_name' => 'advertiser',
            'desc' => '广告主搜索单个结果最大数量',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 17:35:10',
        ),
        174 => 
        array (
            'id' => 278,
            'key' => 'adser_init_perday',
            'table_name' => 'advertiser',
            'desc' => '广告主页面初始化每日统计',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 17:35:38',
        ),
        175 => 
        array (
            'id' => 279,
            'key' => 'adser_analysis_perday',
            'table_name' => 'advertiser',
            'desc' => '广告主分析每日请求数',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 17:36:05',
        ),
        176 => 
        array (
            'id' => 280,
            'key' => 'browse_plans',
            'table_name' => 'plans',
            'desc' => '市场营销-价格计划模块访问权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 16:03:45',
        ),
        177 => 
        array (
            'id' => 281,
            'key' => 'read_plans',
            'table_name' => 'plans',
            'desc' => '读取价格计划权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 16:04:01',
        ),
        178 => 
        array (
            'id' => 282,
            'key' => 'edit_plans',
            'table_name' => 'plans',
            'desc' => '编辑价格计划权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 16:04:15',
        ),
        179 => 
        array (
            'id' => 283,
            'key' => 'add_plans',
            'table_name' => 'plans',
            'desc' => '新增价格计划权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 16:04:24',
        ),
        180 => 
        array (
            'id' => 284,
            'key' => 'delete_plans',
            'table_name' => 'plans',
            'desc' => '删除价格计划权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 16:04:35',
        ),
        181 => 
        array (
            'id' => 285,
            'key' => 'browse_gateway_configs',
            'table_name' => 'gateway_configs',
            'desc' => '支付网关配置访问权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-08 16:13:39',
        ),
        182 => 
        array (
            'id' => 286,
            'key' => 'read_gateway_configs',
            'table_name' => 'gateway_configs',
            'desc' => '读取网关配置',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:13:58',
        ),
        183 => 
        array (
            'id' => 287,
            'key' => 'edit_gateway_configs',
            'table_name' => 'gateway_configs',
            'desc' => '编辑网关配置',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:14:14',
        ),
        184 => 
        array (
            'id' => 288,
            'key' => 'add_gateway_configs',
            'table_name' => 'gateway_configs',
            'desc' => '新增网关配置',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:14:25',
        ),
        185 => 
        array (
            'id' => 289,
            'key' => 'delete_gateway_configs',
            'table_name' => 'gateway_configs',
            'desc' => '删除网关配置',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-07 19:14:37',
        ),
        186 => 
        array (
            'id' => 290,
            'key' => 'search_filter_recent_days',
            'table_name' => 'Advertisement',
            'desc' => '最近几天的搜索过滤数据。只显示最近天数的数据，0表示不限制',
            'order' => 100,
            'type' => 0,
            'updated_at' => '2018-03-07 19:16:25',
        ),
        187 => 
        array (
            'id' => 291,
            'key' => 'session_limit',
            'table_name' => 'sessions',
            'desc' => '会话数量控制',
            'order' => 1,
            'type' => 0,
            'updated_at' => '2018-02-13 01:54:45',
        ),
        188 => 
        array (
            'id' => 294,
            'key' => 'analysis_audience_list',
            'table_name' => 'AdAnalysis',
            'desc' => '广告详情页用户能看到的Audience Targeting Analysis显示行数，策略值为0表示没有限制，有策略值表示限制',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-01 21:15:04',
        ),
        189 => 
        array (
            'id' => 295,
            'key' => 'analysis_country_list',
            'table_name' => 'AdAnalysis',
            'desc' => '广告详情页用户能看到的Top countries显示行数，策略值为0表示没有限制，有策略值表示限制',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-01 21:14:47',
        ),
        190 => 
        array (
            'id' => 296,
            'key' => 'analysis_demography_time',
            'table_name' => 'AdAnalysis',
            'desc' => '广告详情页Demography能看到的显示时间限制，格式为2_week，即2周之前的数据，为0表示没有限制',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-01 21:14:20',
        ),
        191 => 
        array (
            'id' => 297,
            'key' => 'analysis_countrymap_show',
            'table_name' => 'AdAnalysis',
            'desc' => '广告详情页地图图表显示权限',
            'order' => 0,
            'type' => 0,
            'updated_at' => '2018-03-01 21:15:24',
        ),
    ));
        
        
    }
}