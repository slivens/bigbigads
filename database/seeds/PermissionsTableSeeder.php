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
                'desc' => '非空词每日搜索请求数',
                'id' => 99,
                'key' => 'search_times_perday',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2018-03-07 19:17:23',
            ),
            1 => 
            array (
                'desc' => '每次搜索结果数量上限，最多显示xx个',
                'id' => 100,
                'key' => 'result_per_search',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2018-03-08 09:27:15',
            ),
            2 => 
            array (
                'desc' => '搜索过滤',
                'id' => 101,
                'key' => 'search_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2018-03-07 19:21:03',
            ),
            3 => 
            array (
                'desc' => '搜索排序',
                'id' => 102,
                'key' => 'search_sortby',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2018-03-07 19:21:16',
            ),
            4 => 
            array (
                'desc' => '进阶搜索/高级搜索',
                'id' => 103,
                'key' => 'advanced_search',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2018-03-07 19:21:53',
            ),
            5 => 
            array (
                'desc' => '收藏夹搜索',
                'id' => 104,
                'key' => 'save_search',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2018-03-07 19:24:44',
            ),
            6 => 
            array (
                'desc' => '广告主搜索',
                'id' => 105,
                'key' => 'advertiser_search',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2018-03-07 19:23:17',
            ),
            7 => 
            array (
                'desc' => NULL,
                'id' => 106,
                'key' => 'dest_site_search',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:19',
            ),
            8 => 
            array (
                'desc' => '广告内容搜索',
                'id' => 107,
                'key' => 'content_search',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2018-03-07 19:24:16',
            ),
            9 => 
            array (
                'desc' => '受众搜索',
                'id' => 108,
                'key' => 'audience_search',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2018-03-07 19:28:49',
            ),
            10 => 
            array (
                'desc' => '按日期过滤',
                'id' => 109,
                'key' => 'date_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2018-03-07 19:28:25',
            ),
            11 => 
            array (
            'desc' => '按广告样式/类型过滤（Ad type)',
            'id' => 110,
            'key' => 'format_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 19:28:17',
        ),
        12 => 
        array (
            'desc' => '按广告投放时间过滤',
            'id' => 111,
            'key' => 'duration_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 19:28:02',
        ),
        13 => 
        array (
            'desc' => '按查看次数过滤',
            'id' => 112,
            'key' => 'see_times_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 19:29:05',
        ),
        14 => 
        array (
            'desc' => '按语言类型过滤',
            'id' => 113,
            'key' => 'lang_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 19:29:18',
        ),
        15 => 
        array (
            'desc' => NULL,
            'id' => 114,
            'key' => 'engagement_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2017-03-06 01:08:19',
        ),
        16 => 
        array (
            'desc' => '按日期排序',
            'id' => 115,
            'key' => 'date_sort',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 19:31:20',
        ),
        17 => 
        array (
            'desc' => '按点赞数量排序',
            'id' => 116,
            'key' => 'likes_sort',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-08 16:05:18',
        ),
        18 => 
        array (
            'desc' => '按分享数量排序',
            'id' => 117,
            'key' => 'shares_sort',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-08 16:05:42',
        ),
        19 => 
        array (
            'desc' => '按视频查看次数排序',
            'id' => 118,
            'key' => 'video_views_sort',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 19:32:26',
        ),
        20 => 
        array (
            'desc' => '按评论数排序',
            'id' => 119,
            'key' => 'comment_sort',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 19:33:16',
        ),
        21 => 
        array (
            'desc' => '按广告发布持续时间排序',
            'id' => 120,
            'key' => 'duration_sort',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 19:33:57',
        ),
        22 => 
        array (
            'desc' => '按广告查看次数排序',
            'id' => 121,
            'key' => 'views_sort',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 19:34:11',
        ),
        23 => 
        array (
            'desc' => NULL,
            'id' => 122,
            'key' => 'engagement_total_sort',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2017-03-06 01:08:20',
        ),
        24 => 
        array (
            'desc' => NULL,
            'id' => 123,
            'key' => 'engagement_inc_sort',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2017-03-06 01:08:20',
        ),
        25 => 
        array (
            'desc' => '按点赞数升序排序',
            'id' => 124,
            'key' => 'likes_inc_sort',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-08 09:31:24',
        ),
        26 => 
        array (
            'desc' => '按视频查看次数升序排序',
            'id' => 125,
            'key' => 'video_views_inc_sort',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-08 09:31:42',
        ),
        27 => 
        array (
            'desc' => '广告图片下载',
            'id' => 126,
            'key' => 'image_download',
            'order' => 0,
            'table_name' => 'export',
            'type' => 0,
            'updated_at' => '2018-03-07 19:37:50',
        ),
        28 => 
        array (
            'desc' => '广告视频下载',
            'id' => 127,
            'key' => 'video_download',
            'order' => 0,
            'table_name' => 'export',
            'type' => 0,
            'updated_at' => '2018-03-07 19:38:05',
        ),
        29 => 
        array (
            'desc' => '高清视频下载',
            'id' => 128,
            'key' => 'HD_video_download',
            'order' => 0,
            'table_name' => 'export',
            'type' => 0,
            'updated_at' => '2018-03-07 19:38:19',
        ),
        30 => 
        array (
        'desc' => '导出(广告数据)',
            'id' => 129,
            'key' => 'Export',
            'order' => 0,
            'table_name' => 'export',
            'type' => 0,
            'updated_at' => '2018-03-08 09:32:09',
        ),
        31 => 
        array (
            'desc' => NULL,
            'id' => 130,
            'key' => 'search_statics',
            'order' => 0,
            'table_name' => 'statics',
            'type' => 0,
            'updated_at' => '2017-03-06 01:08:20',
        ),
        32 => 
        array (
            'desc' => '广告分析',
            'id' => 131,
            'key' => 'ad_analysis',
            'order' => 0,
            'table_name' => 'statics',
            'type' => 0,
            'updated_at' => '2018-03-07 19:40:10',
        ),
        33 => 
        array (
            'desc' => '广告主分析',
            'id' => 132,
            'key' => 'adser_analysis',
            'order' => 0,
            'table_name' => 'statics',
            'type' => 0,
            'updated_at' => '2018-03-07 19:40:22',
        ),
        34 => 
        array (
            'desc' => '实时广告分析',
            'id' => 133,
            'key' => 'Realtime_AD_analysis',
            'order' => 0,
            'table_name' => 'statics',
            'type' => 0,
            'updated_at' => '2018-03-07 19:40:46',
        ),
        35 => 
        array (
            'desc' => '广告排行',
            'id' => 134,
            'key' => 'ranking',
            'order' => 0,
            'table_name' => 'ranking',
            'type' => 0,
            'updated_at' => '2018-03-07 19:41:30',
        ),
        36 => 
        array (
            'desc' => '广告排行导出',
            'id' => 135,
            'key' => 'ranking_export',
            'order' => 0,
            'table_name' => 'ranking',
            'type' => 0,
            'updated_at' => '2018-03-07 19:41:47',
        ),
        37 => 
        array (
            'desc' => '按分类排行',
            'id' => 136,
            'key' => 'ranking_by_category',
            'order' => 0,
            'table_name' => 'ranking',
            'type' => 0,
            'updated_at' => '2018-03-07 19:42:01',
        ),
        38 => 
        array (
            'desc' => '收藏夹使用权限（收藏夹支持？）',
            'id' => 137,
            'key' => 'bookmark_support',
            'order' => 0,
            'table_name' => 'bookmark',
            'type' => 0,
            'updated_at' => '2018-03-07 19:43:07',
        ),
        39 => 
        array (
            'desc' => '收藏夹列表显示',
            'id' => 138,
            'key' => 'bookmark_list',
            'order' => 0,
            'table_name' => 'bookmark',
            'type' => 0,
            'updated_at' => '2018-03-07 19:43:29',
        ),
        40 => 
        array (
            'desc' => '广告主收藏夹使用权限',
            'id' => 139,
            'key' => 'bookmark_adser_support',
            'order' => 0,
            'table_name' => 'bookmark',
            'type' => 0,
            'updated_at' => '2018-03-07 19:44:17',
        ),
        41 => 
        array (
            'desc' => '广告收藏数量上限',
            'id' => 140,
            'key' => 'save_count',
            'order' => 0,
            'table_name' => 'bookmark',
            'type' => 0,
            'updated_at' => '2018-03-07 19:45:01',
        ),
        42 => 
        array (
            'desc' => '监听权限',
            'id' => 141,
            'key' => 'monitor_support',
            'order' => 0,
            'table_name' => 'monitor',
            'type' => 0,
            'updated_at' => '2018-03-07 19:45:55',
        ),
        43 => 
        array (
            'desc' => '广告关键词监听',
            'id' => 142,
            'key' => 'monitor_ad_keyword',
            'order' => 0,
            'table_name' => 'monitor',
            'type' => 0,
            'updated_at' => '2018-03-07 19:56:44',
        ),
        44 => 
        array (
            'desc' => '广告主监听',
            'id' => 143,
            'key' => 'monitor_advertiser',
            'order' => 0,
            'table_name' => 'monitor',
            'type' => 0,
            'updated_at' => '2018-03-07 19:57:04',
        ),
        45 => 
        array (
            'desc' => '调用操作过滤',
            'id' => 144,
            'key' => 'call_action_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 20:02:02',
        ),
        46 => 
        array (
            'desc' => '广告主搜索',
            'id' => 145,
            'key' => 'advertiser_search',
            'order' => 0,
            'table_name' => 'advertiser',
            'type' => 0,
            'updated_at' => '2018-03-07 20:03:23',
        ),
        47 => 
        array (
            'desc' => '按域名查询',
            'id' => 146,
            'key' => 'domain_search',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 20:06:57',
        ),
        48 => 
        array (
            'desc' => NULL,
            'id' => 147,
            'key' => 'engagements_sort',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2017-03-06 13:05:13',
        ),
        49 => 
        array (
            'desc' => '按广告查看次数升序排序',
            'id' => 148,
            'key' => 'views_inc_sort',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-08 09:33:49',
        ),
        50 => 
        array (
            'desc' => '按广告分享数量升序排序',
            'id' => 149,
            'key' => 'shares_inc_sort',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-08 09:34:11',
        ),
        51 => 
        array (
            'desc' => '按评论升序排序',
            'id' => 150,
            'key' => 'comments_inc_sort',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 20:11:06',
        ),
        52 => 
        array (
            'desc' => '广告分析总览',
            'id' => 151,
            'key' => 'analysis_overview',
            'order' => 0,
            'table_name' => 'AdAnalysis',
            'type' => 0,
            'updated_at' => '2018-03-07 20:11:39',
        ),
        53 => 
        array (
            'desc' => '广告链接分析',
            'id' => 152,
            'key' => 'analysis_link',
            'order' => 0,
            'table_name' => 'AdAnalysis',
            'type' => 0,
            'updated_at' => '2018-03-07 20:14:43',
        ),
        54 => 
        array (
            'desc' => '广告受众分析',
            'id' => 153,
            'key' => 'analysis_audience',
            'order' => 0,
            'table_name' => 'AdAnalysis',
            'type' => 0,
            'updated_at' => '2018-03-07 20:15:03',
        ),
        55 => 
        array (
            'desc' => '广告趋势分析',
            'id' => 154,
            'key' => 'analysis_trend',
            'order' => 0,
            'table_name' => 'AdAnalysis',
            'type' => 0,
            'updated_at' => '2018-03-07 20:15:24',
        ),
        56 => 
        array (
            'desc' => '广告相似性分析',
            'id' => 155,
            'key' => 'analysis_similar',
            'order' => 0,
            'table_name' => 'AdAnalysis',
            'type' => 0,
            'updated_at' => '2018-03-07 20:17:27',
        ),
        57 => 
        array (
            'desc' => NULL,
            'id' => 156,
            'key' => 'statics_overview',
            'order' => 0,
            'table_name' => 'Keyword Statics',
            'type' => 0,
            'updated_at' => '2017-03-06 14:32:43',
        ),
        58 => 
        array (
            'desc' => NULL,
            'id' => 157,
            'key' => 'statics_link',
            'order' => 0,
            'table_name' => 'Keyword Statics',
            'type' => 0,
            'updated_at' => '2017-03-06 14:32:43',
        ),
        59 => 
        array (
            'desc' => NULL,
            'id' => 158,
            'key' => 'statics_audience',
            'order' => 0,
            'table_name' => 'Keyword Statics',
            'type' => 0,
            'updated_at' => '2017-03-06 14:32:43',
        ),
        60 => 
        array (
            'desc' => NULL,
            'id' => 159,
            'key' => 'statics_trend',
            'order' => 0,
            'table_name' => 'Keyword Statics',
            'type' => 0,
            'updated_at' => '2017-03-06 14:32:43',
        ),
        61 => 
        array (
            'desc' => NULL,
            'id' => 160,
            'key' => 'statics_all',
            'order' => 0,
            'table_name' => 'Keyword Statics',
            'type' => 0,
            'updated_at' => '2017-03-06 14:32:43',
        ),
        62 => 
        array (
            'desc' => '广告主搜索',
            'id' => 161,
            'key' => 'adser_search',
            'order' => 0,
            'table_name' => 'advertiser',
            'type' => 0,
            'updated_at' => '2018-03-07 20:17:06',
        ),
        63 => 
        array (
            'desc' => '每日关键词搜索次数上限',
            'id' => 162,
            'key' => 'keyword_times_perday',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 20:16:30',
        ),
        64 => 
        array (
            'desc' => '每日广告分析次数上限',
            'id' => 163,
            'key' => 'ad_analysis_times_perday',
            'order' => 0,
            'table_name' => 'AdAnalysis',
            'type' => 0,
            'updated_at' => '2018-03-07 20:16:48',
        ),
        65 => 
        array (
            'desc' => '每日广告主搜索次数上限',
            'id' => 164,
            'key' => 'adser_search_times_perday',
            'order' => 0,
            'table_name' => 'advertiser',
            'type' => 0,
            'updated_at' => '2018-03-07 20:18:18',
        ),
        66 => 
        array (
            'desc' => '广告主收藏权限',
            'id' => 165,
            'key' => 'advertiser_collect',
            'order' => 0,
            'table_name' => 'bookmark',
            'type' => 0,
            'updated_at' => '2018-03-07 20:18:41',
        ),
        67 => 
        array (
            'desc' => '管理模块访问权限',
            'id' => 166,
            'key' => 'browse_admin',
            'order' => 0,
            'table_name' => 'admin',
            'type' => 0,
            'updated_at' => '2018-03-08 09:35:08',
        ),
        68 => 
        array (
            'desc' => '数据库模块访问权限',
            'id' => 167,
            'key' => 'browse_database',
            'order' => 0,
            'table_name' => 'admin',
            'type' => 0,
            'updated_at' => '2018-03-08 09:35:20',
        ),
        69 => 
        array (
            'desc' => '媒体库模块访问权限',
            'id' => 168,
            'key' => 'browse_media',
            'order' => 0,
            'table_name' => 'admin',
            'type' => 0,
            'updated_at' => '2018-03-08 09:35:28',
        ),
        70 => 
        array (
            'desc' => '设置模块访问权限',
            'id' => 169,
            'key' => 'browse_settings',
            'order' => 0,
            'table_name' => 'admin',
            'type' => 0,
            'updated_at' => '2018-03-08 09:35:40',
        ),
        71 => 
        array (
            'desc' => '菜单模块访问权限',
            'id' => 170,
            'key' => 'browse_menus',
            'order' => 0,
            'table_name' => 'menus',
            'type' => 0,
            'updated_at' => '2018-03-08 09:35:53',
        ),
        72 => 
        array (
            'desc' => '读取菜单权限',
            'id' => 171,
            'key' => 'read_menus',
            'order' => 0,
            'table_name' => 'menus',
            'type' => 0,
            'updated_at' => '2018-03-07 20:20:52',
        ),
        73 => 
        array (
            'desc' => '编辑菜单权限',
            'id' => 172,
            'key' => 'edit_menus',
            'order' => 0,
            'table_name' => 'menus',
            'type' => 0,
            'updated_at' => '2018-03-07 20:21:07',
        ),
        74 => 
        array (
            'desc' => '新增菜单权限',
            'id' => 173,
            'key' => 'add_menus',
            'order' => 0,
            'table_name' => 'menus',
            'type' => 0,
            'updated_at' => '2018-03-07 20:21:20',
        ),
        75 => 
        array (
            'desc' => '删除菜单权限',
            'id' => 174,
            'key' => 'delete_menus',
            'order' => 0,
            'table_name' => 'menus',
            'type' => 0,
            'updated_at' => '2018-03-07 20:21:32',
        ),
        76 => 
        array (
            'desc' => '页面模块访问权限',
            'id' => 175,
            'key' => 'browse_pages',
            'order' => 0,
            'table_name' => 'pages',
            'type' => 0,
            'updated_at' => '2018-03-08 09:36:06',
        ),
        77 => 
        array (
            'desc' => '页面读取权限',
            'id' => 176,
            'key' => 'read_pages',
            'order' => 0,
            'table_name' => 'pages',
            'type' => 0,
            'updated_at' => '2018-03-07 20:22:06',
        ),
        78 => 
        array (
            'desc' => '页面编辑权限',
            'id' => 177,
            'key' => 'edit_pages',
            'order' => 0,
            'table_name' => 'pages',
            'type' => 0,
            'updated_at' => '2018-03-07 20:22:25',
        ),
        79 => 
        array (
            'desc' => '新增页面权限',
            'id' => 178,
            'key' => 'add_pages',
            'order' => 0,
            'table_name' => 'pages',
            'type' => 0,
            'updated_at' => '2018-03-07 20:22:36',
        ),
        80 => 
        array (
            'desc' => '删除页面权限',
            'id' => 179,
            'key' => 'delete_pages',
            'order' => 0,
            'table_name' => 'pages',
            'type' => 0,
            'updated_at' => '2018-03-07 20:22:53',
        ),
        81 => 
        array (
            'desc' => '角色模块访问权限',
            'id' => 180,
            'key' => 'browse_roles',
            'order' => 0,
            'table_name' => 'roles',
            'type' => 0,
            'updated_at' => '2018-03-07 20:28:37',
        ),
        82 => 
        array (
            'desc' => '读取角色权限',
            'id' => 181,
            'key' => 'read_roles',
            'order' => 0,
            'table_name' => 'roles',
            'type' => 0,
            'updated_at' => '2018-03-07 20:25:35',
        ),
        83 => 
        array (
            'desc' => '编辑角色权限',
            'id' => 182,
            'key' => 'edit_roles',
            'order' => 0,
            'table_name' => 'roles',
            'type' => 0,
            'updated_at' => '2018-03-07 20:25:46',
        ),
        84 => 
        array (
            'desc' => '新增角色权限',
            'id' => 183,
            'key' => 'add_roles',
            'order' => 0,
            'table_name' => 'roles',
            'type' => 0,
            'updated_at' => '2018-03-07 20:24:47',
        ),
        85 => 
        array (
            'desc' => '删除角色权限',
            'id' => 184,
            'key' => 'delete_roles',
            'order' => 0,
            'table_name' => 'roles',
            'type' => 0,
            'updated_at' => '2018-03-07 20:24:59',
        ),
        86 => 
        array (
            'desc' => '用户模块访问权限',
            'id' => 185,
            'key' => 'browse_users',
            'order' => 0,
            'table_name' => 'users',
            'type' => 0,
            'updated_at' => '2018-03-07 20:26:21',
        ),
        87 => 
        array (
            'desc' => '读取用户权限',
            'id' => 186,
            'key' => 'read_users',
            'order' => 0,
            'table_name' => 'users',
            'type' => 0,
            'updated_at' => '2018-03-07 20:26:32',
        ),
        88 => 
        array (
            'desc' => '编辑用户权限',
            'id' => 187,
            'key' => 'edit_users',
            'order' => 0,
            'table_name' => 'users',
            'type' => 0,
            'updated_at' => '2018-03-07 20:26:44',
        ),
        89 => 
        array (
            'desc' => '新增用户权限',
            'id' => 188,
            'key' => 'add_users',
            'order' => 0,
            'table_name' => 'users',
            'type' => 0,
            'updated_at' => '2018-03-07 20:26:55',
        ),
        90 => 
        array (
            'desc' => '删除用户权限',
            'id' => 189,
            'key' => 'delete_users',
            'order' => 0,
            'table_name' => 'users',
            'type' => 0,
            'updated_at' => '2018-03-07 20:27:08',
        ),
        91 => 
        array (
            'desc' => '文章模块访问权限',
            'id' => 190,
            'key' => 'browse_posts',
            'order' => 0,
            'table_name' => 'posts',
            'type' => 0,
            'updated_at' => '2018-03-07 20:27:23',
        ),
        92 => 
        array (
            'desc' => '读取文章权限',
            'id' => 191,
            'key' => 'read_posts',
            'order' => 0,
            'table_name' => 'posts',
            'type' => 0,
            'updated_at' => '2018-03-07 20:27:33',
        ),
        93 => 
        array (
            'desc' => '编辑文章权限',
            'id' => 192,
            'key' => 'edit_posts',
            'order' => 0,
            'table_name' => 'posts',
            'type' => 0,
            'updated_at' => '2018-03-07 20:27:49',
        ),
        94 => 
        array (
            'desc' => '新增文章权限',
            'id' => 193,
            'key' => 'add_posts',
            'order' => 0,
            'table_name' => 'posts',
            'type' => 0,
            'updated_at' => '2018-03-07 20:28:00',
        ),
        95 => 
        array (
            'desc' => '删除文章权限',
            'id' => 194,
            'key' => 'delete_posts',
            'order' => 0,
            'table_name' => 'posts',
            'type' => 0,
            'updated_at' => '2018-03-07 20:29:02',
        ),
        96 => 
        array (
            'desc' => '分类模块访问权限',
            'id' => 195,
            'key' => 'browse_categories',
            'order' => 0,
            'table_name' => 'categories',
            'type' => 0,
            'updated_at' => '2018-03-07 20:29:16',
        ),
        97 => 
        array (
            'desc' => '读取分类权限',
            'id' => 196,
            'key' => 'read_categories',
            'order' => 0,
            'table_name' => 'categories',
            'type' => 0,
            'updated_at' => '2018-03-07 20:29:29',
        ),
        98 => 
        array (
            'desc' => '编辑分类权限',
            'id' => 197,
            'key' => 'edit_categories',
            'order' => 0,
            'table_name' => 'categories',
            'type' => 0,
            'updated_at' => '2018-03-07 20:29:42',
        ),
        99 => 
        array (
            'desc' => '新增分类权限',
            'id' => 198,
            'key' => 'add_categories',
            'order' => 0,
            'table_name' => 'categories',
            'type' => 0,
            'updated_at' => '2018-03-07 20:29:53',
        ),
        100 => 
        array (
            'desc' => '删除分类权限',
            'id' => 199,
            'key' => 'delete_categories',
            'order' => 0,
            'table_name' => 'categories',
            'type' => 0,
            'updated_at' => '2018-03-07 20:30:05',
        ),
        101 => 
        array (
            'desc' => '按时间线过滤',
            'id' => 200,
            'key' => 'timeline_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 20:30:22',
        ),
        102 => 
        array (
            'desc' => '按电话号码过滤',
            'id' => 201,
            'key' => 'phone_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 20:30:36',
        ),
        103 => 
        array (
        'desc' => '按右列过滤(?)',
            'id' => 202,
            'key' => 'rightcolumn_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 20:31:51',
        ),
        104 => 
        array (
            'desc' => '高级搜索-按点赞数过滤',
            'id' => 203,
            'key' => 'advance_likes_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 20:33:23',
        ),
        105 => 
        array (
            'desc' => '高级搜索-按分享数过滤',
            'id' => 204,
            'key' => 'advance_shares_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 20:33:37',
        ),
        106 => 
        array (
            'desc' => '高级搜索-按评论数过滤',
            'id' => 205,
            'key' => 'advance_comments_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 20:33:09',
        ),
        107 => 
        array (
            'desc' => '高级搜索-按视频播放次数过滤',
            'id' => 206,
            'key' => 'advance_video_views_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 20:33:55',
        ),
        108 => 
        array (
            'desc' => '',
            'id' => 207,
            'key' => 'advance_engagement_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-08 09:36:55',
        ),
        109 => 
        array (
            'desc' => '市场营销-邮件列表模块访问权限',
            'id' => 208,
            'key' => 'browse_maillist',
            'order' => 0,
            'table_name' => 'maillist',
            'type' => 0,
            'updated_at' => '2018-03-08 16:07:00',
        ),
        110 => 
        array (
            'desc' => '读取邮件列表权限',
            'id' => 209,
            'key' => 'read_maillist',
            'order' => 0,
            'table_name' => 'maillist',
            'type' => 0,
            'updated_at' => '2018-03-07 20:34:51',
        ),
        111 => 
        array (
            'desc' => '编辑邮件列表权限',
            'id' => 210,
            'key' => 'edit_maillist',
            'order' => 0,
            'table_name' => 'maillist',
            'type' => 0,
            'updated_at' => '2018-03-07 20:35:02',
        ),
        112 => 
        array (
            'desc' => '新增邮件列表权限',
            'id' => 211,
            'key' => 'add_maillist',
            'order' => 0,
            'table_name' => 'maillist',
            'type' => 0,
            'updated_at' => '2018-03-07 20:35:15',
        ),
        113 => 
        array (
            'desc' => '删除邮件列表权限',
            'id' => 212,
            'key' => 'delete_maillist',
            'order' => 0,
            'table_name' => 'maillist',
            'type' => 0,
            'updated_at' => '2018-03-07 20:35:27',
        ),
        114 => 
        array (
            'desc' => '市场营销-联盟会员模块访问权限',
            'id' => 213,
            'key' => 'browse_affiliates',
            'order' => 0,
            'table_name' => 'affiliates',
            'type' => 0,
            'updated_at' => '2018-03-08 16:07:27',
        ),
        115 => 
        array (
            'desc' => '读取联盟会员权限',
            'id' => 214,
            'key' => 'read_affiliates',
            'order' => 0,
            'table_name' => 'affiliates',
            'type' => 0,
            'updated_at' => '2018-03-07 20:37:32',
        ),
        116 => 
        array (
            'desc' => '编辑联盟会员权限',
            'id' => 215,
            'key' => 'edit_affiliates',
            'order' => 0,
            'table_name' => 'affiliates',
            'type' => 0,
            'updated_at' => '2018-03-07 20:37:55',
        ),
        117 => 
        array (
            'desc' => '新增联盟会员权限',
            'id' => 216,
            'key' => 'add_affiliates',
            'order' => 0,
            'table_name' => 'affiliates',
            'type' => 0,
            'updated_at' => '2018-03-07 20:38:07',
        ),
        118 => 
        array (
            'desc' => '删除联盟会员权限',
            'id' => 217,
            'key' => 'delete_affiliates',
            'order' => 0,
            'table_name' => 'affiliates',
            'type' => 0,
            'updated_at' => '2018-03-07 20:38:20',
        ),
        119 => 
        array (
            'desc' => '市场营销-优惠券模块访问权限',
            'id' => 223,
            'key' => 'browse_coupons',
            'order' => 0,
            'table_name' => 'coupons',
            'type' => 0,
            'updated_at' => '2018-03-08 16:07:48',
        ),
        120 => 
        array (
            'desc' => '读取优惠券权限',
            'id' => 224,
            'key' => 'read_coupons',
            'order' => 0,
            'table_name' => 'coupons',
            'type' => 0,
            'updated_at' => '2018-03-07 20:38:51',
        ),
        121 => 
        array (
            'desc' => '编辑优惠券权限',
            'id' => 225,
            'key' => 'edit_coupons',
            'order' => 0,
            'table_name' => 'coupons',
            'type' => 0,
            'updated_at' => '2018-03-07 20:39:04',
        ),
        122 => 
        array (
            'desc' => '新增优惠券权限',
            'id' => 226,
            'key' => 'add_coupons',
            'order' => 0,
            'table_name' => 'coupons',
            'type' => 0,
            'updated_at' => '2018-03-07 20:39:14',
        ),
        123 => 
        array (
            'desc' => '删除优惠券权限',
            'id' => 227,
            'key' => 'delete_coupons',
            'order' => 0,
            'table_name' => 'coupons',
            'type' => 0,
            'updated_at' => '2018-03-07 20:39:24',
        ),
        124 => 
        array (
            'desc' => '搜索页面初始化每日统计',
            'id' => 228,
            'key' => 'search_init_perday',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 20:40:27',
        ),
        125 => 
        array (
            'desc' => '每日查询上限，发出搜索请求就算有效操作',
            'id' => 229,
            'key' => 'search_limit_perday',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-08 09:42:15',
        ),
        126 => 
        array (
            'desc' => '每日条件查询上限',
            'id' => 230,
            'key' => 'search_where_perday',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 20:44:07',
        ),
        127 => 
        array (
            'desc' => '广告主详情页面初始化每日统计',
            'id' => 231,
            'key' => 'specific_adser_init_perday',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 20:45:44',
        ),
        128 => 
        array (
            'desc' => '广告主详情页面每日搜索上限',
            'id' => 232,
            'key' => 'specific_adser_limit_perday',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 20:46:52',
        ),
        129 => 
        array (
            'desc' => '广告主详情页面每日条件查询数量上限',
            'id' => 233,
            'key' => 'specific_adser_where_perday',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 20:47:16',
        ),
        130 => 
        array (
            'desc' => '按应用过滤',
            'id' => 234,
            'key' => 'app_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 20:47:51',
        ),
        131 => 
        array (
            'desc' => '总搜索次数，永久累计',
            'id' => 235,
            'key' => 'search_total_times',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-08 09:44:26',
        ),
        132 => 
        array (
            'desc' => '按国家过滤',
            'id' => 236,
            'key' => 'country_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 20:48:26',
        ),
        133 => 
        array (
        'desc' => '按网络营销(类型?)过滤',
            'id' => 237,
            'key' => 'emarketing_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 20:57:40',
        ),
        134 => 
        array (
            'desc' => '按推广码过滤',
            'id' => 238,
            'key' => 'tracking_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-08 09:49:23',
        ),
        135 => 
        array (
            'desc' => '按联盟会员类型过滤',
            'id' => 239,
            'key' => 'affiliate_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 20:59:13',
        ),
        136 => 
        array (
            'desc' => '按电商列表过滤',
            'id' => 240,
            'key' => 'e_commerceList_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 20:59:48',
        ),
        137 => 
        array (
            'desc' => '高级过滤权限',
            'id' => 241,
            'key' => 'advance_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 21:00:01',
        ),
        138 => 
        array (
            'desc' => '收藏夹每日收藏初始化',
            'id' => 242,
            'key' => 'bookmark_init_perday',
            'order' => 0,
            'table_name' => 'bookmark',
            'type' => 0,
            'updated_at' => '2018-03-07 21:00:49',
        ),
        139 => 
        array (
            'desc' => '收藏夹每日收藏上限',
            'id' => 243,
            'key' => 'bookmark_limit_perday',
            'order' => 0,
            'table_name' => 'bookmark',
            'type' => 0,
            'updated_at' => '2018-03-07 21:00:59',
        ),
        140 => 
        array (
            'desc' => '按电子商务过滤',
            'id' => 244,
            'key' => 'e_commerce_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 21:01:23',
        ),
        141 => 
        array (
            'desc' => NULL,
            'id' => 245,
            'key' => 'objective_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2017-08-14 02:37:21',
        ),
        142 => 
        array (
            'desc' => '高级搜索-受众查询',
            'id' => 246,
            'key' => 'advance_audience_search',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 21:02:35',
        ),
        143 => 
        array (
            'desc' => '按受众年龄过滤',
            'id' => 247,
            'key' => 'audience_age_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 21:03:50',
        ),
        144 => 
        array (
            'desc' => '按受众性别过滤',
            'id' => 248,
            'key' => 'audience_gender_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 21:03:34',
        ),
        145 => 
        array (
            'desc' => '退款申请单模块访问权限',
            'id' => 249,
            'key' => 'browse_refunds',
            'order' => 0,
            'table_name' => 'refunds',
            'type' => 0,
            'updated_at' => '2018-03-08 16:10:57',
        ),
        146 => 
        array (
            'desc' => '读取退款申请单权限',
            'id' => 250,
            'key' => 'read_refunds',
            'order' => 0,
            'table_name' => 'refunds',
            'type' => 0,
            'updated_at' => '2018-03-08 16:11:10',
        ),
        147 => 
        array (
            'desc' => '修改退款申请单权限',
            'id' => 251,
            'key' => 'edit_refunds',
            'order' => 0,
            'table_name' => 'refunds',
            'type' => 0,
            'updated_at' => '2018-03-08 16:11:59',
        ),
        148 => 
        array (
            'desc' => '新增退款申请单权限',
            'id' => 252,
            'key' => 'add_refunds',
            'order' => 0,
            'table_name' => 'refunds',
            'type' => 0,
            'updated_at' => '2018-03-08 16:12:09',
        ),
        149 => 
        array (
            'desc' => '删除退款申请单权限，删除后买家可以再次发起退款申请',
            'id' => 253,
            'key' => 'delete_refunds',
            'order' => 0,
            'table_name' => 'refunds',
            'type' => 0,
            'updated_at' => '2018-03-08 16:12:29',
        ),
        150 => 
        array (
            'desc' => '按受众兴趣点过滤',
            'id' => 254,
            'key' => 'audience_interest_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 21:05:37',
        ),
        151 => 
        array (
            'desc' => '按搜索模式过滤',
            'id' => 255,
            'key' => 'search_mode_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 21:05:51',
        ),
        152 => 
        array (
            'desc' => '按最初投放时间过滤',
            'id' => 256,
            'key' => 'first_time_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-08 09:20:29',
        ),
        153 => 
        array (
            'desc' => '按最后投放时间过滤',
            'id' => 257,
            'key' => 'last_time_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-08 09:20:45',
        ),
        154 => 
        array (
            'desc' => '按rang过滤',
            'id' => 258,
            'key' => 'rang_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 17:26:58',
        ),
        155 => 
        array (
            'desc' => '广告收藏总数，指账号上收藏的广告的总数',
            'id' => 259,
            'key' => 'save_ad_count',
            'order' => 0,
            'table_name' => 'bookmark',
            'type' => 0,
            'updated_at' => '2018-03-08 09:21:19',
        ),
        156 => 
        array (
            'desc' => '非空词每日查询上限',
            'id' => 260,
            'key' => 'search_limit_keys_perday',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 17:25:39',
        ),
        157 => 
        array (
            'desc' => '空词每日查询上限',
            'id' => 261,
            'key' => 'search_limit_without_keys_perday',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 17:25:51',
        ),
        158 => 
        array (
            'desc' => '空词每日请求总数',
            'id' => 262,
            'key' => 'search_without_key_total_perday',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 17:25:11',
        ),
        159 => 
        array (
            'desc' => '非空词每日请求总数',
            'id' => 263,
            'key' => 'search_key_total_perday',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 17:24:48',
        ),
        160 => 
        array (
            'desc' => '热词每日查询上限',
            'id' => 264,
            'key' => 'hot_search_times_perday',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 17:26:12',
        ),
        161 => 
        array (
            'desc' => '特定广告主搜索请求每日统计',
            'id' => 265,
            'key' => 'specific_adser_times_perday',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 17:27:55',
        ),
        162 => 
        array (
            'desc' => '按查看总数排序',
            'id' => 266,
            'key' => 'view_count_sort',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-08 16:15:42',
        ),
        163 => 
        array (
            'desc' => '默认过滤',
            'id' => 267,
            'key' => 'default_filter',
            'order' => 0,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-08 09:21:49',
        ),
        164 => 
        array (
            'desc' => '浏览权限',
            'id' => 268,
            'key' => 'browse_permissions',
            'order' => 0,
            'table_name' => 'permissions',
            'type' => 0,
            'updated_at' => '2018-03-07 17:31:36',
        ),
        165 => 
        array (
            'desc' => '读取权限',
            'id' => 269,
            'key' => 'read_permissions',
            'order' => 0,
            'table_name' => 'permissions',
            'type' => 0,
            'updated_at' => '2018-03-07 17:31:50',
        ),
        166 => 
        array (
            'desc' => '写入权限',
            'id' => 270,
            'key' => 'edit_permissions',
            'order' => 0,
            'table_name' => 'permissions',
            'type' => 0,
            'updated_at' => '2018-03-07 17:31:59',
        ),
        167 => 
        array (
            'desc' => '新增权限',
            'id' => 271,
            'key' => 'add_permissions',
            'order' => 0,
            'table_name' => 'permissions',
            'type' => 0,
            'updated_at' => '2018-03-07 19:12:16',
        ),
        168 => 
        array (
            'desc' => '删除权限',
            'id' => 272,
            'key' => 'delete_permissions',
            'order' => 0,
            'table_name' => 'permissions',
            'type' => 0,
            'updated_at' => '2018-03-07 17:32:29',
        ),
        169 => 
        array (
            'desc' => '广告主空词每日请求总数',
            'id' => 273,
            'key' => 'adser_without_key_total_perday',
            'order' => 0,
            'table_name' => 'advertiser',
            'type' => 0,
            'updated_at' => '2018-03-07 17:33:36',
        ),
        170 => 
        array (
            'desc' => '广告主非空词每日请求总数',
            'id' => 274,
            'key' => 'adser_key_total_perday',
            'order' => 0,
            'table_name' => 'advertiser',
            'type' => 0,
            'updated_at' => '2018-03-07 17:33:56',
        ),
        171 => 
        array (
            'desc' => '广告主非空词下拉请求每日统计',
            'id' => 275,
            'key' => 'adser_limit_keys_perday',
            'order' => 0,
            'table_name' => 'advertiser',
            'type' => 0,
            'updated_at' => '2018-03-07 17:34:19',
        ),
        172 => 
        array (
            'desc' => '广告主空词下拉请求每日统计',
            'id' => 276,
            'key' => 'adser_limit_without_keys_perday',
            'order' => 0,
            'table_name' => 'advertiser',
            'type' => 0,
            'updated_at' => '2018-03-07 17:34:38',
        ),
        173 => 
        array (
            'desc' => '广告主搜索单个结果最大数量',
            'id' => 277,
            'key' => 'adser_result_per_search',
            'order' => 0,
            'table_name' => 'advertiser',
            'type' => 0,
            'updated_at' => '2018-03-07 17:35:10',
        ),
        174 => 
        array (
            'desc' => '广告主页面初始化每日统计',
            'id' => 278,
            'key' => 'adser_init_perday',
            'order' => 0,
            'table_name' => 'advertiser',
            'type' => 0,
            'updated_at' => '2018-03-07 17:35:38',
        ),
        175 => 
        array (
            'desc' => '广告主分析每日请求数',
            'id' => 279,
            'key' => 'adser_analysis_perday',
            'order' => 0,
            'table_name' => 'advertiser',
            'type' => 0,
            'updated_at' => '2018-03-07 17:36:05',
        ),
        176 => 
        array (
            'desc' => '市场营销-价格计划模块访问权限',
            'id' => 280,
            'key' => 'browse_plans',
            'order' => 0,
            'table_name' => 'plans',
            'type' => 0,
            'updated_at' => '2018-03-08 16:03:45',
        ),
        177 => 
        array (
            'desc' => '读取价格计划权限',
            'id' => 281,
            'key' => 'read_plans',
            'order' => 0,
            'table_name' => 'plans',
            'type' => 0,
            'updated_at' => '2018-03-08 16:04:01',
        ),
        178 => 
        array (
            'desc' => '编辑价格计划权限',
            'id' => 282,
            'key' => 'edit_plans',
            'order' => 0,
            'table_name' => 'plans',
            'type' => 0,
            'updated_at' => '2018-03-08 16:04:15',
        ),
        179 => 
        array (
            'desc' => '新增价格计划权限',
            'id' => 283,
            'key' => 'add_plans',
            'order' => 0,
            'table_name' => 'plans',
            'type' => 0,
            'updated_at' => '2018-03-08 16:04:24',
        ),
        180 => 
        array (
            'desc' => '删除价格计划权限',
            'id' => 284,
            'key' => 'delete_plans',
            'order' => 0,
            'table_name' => 'plans',
            'type' => 0,
            'updated_at' => '2018-03-08 16:04:35',
        ),
        181 => 
        array (
            'desc' => '支付网关配置访问权限',
            'id' => 285,
            'key' => 'browse_gateway_configs',
            'order' => 0,
            'table_name' => 'gateway_configs',
            'type' => 0,
            'updated_at' => '2018-03-08 16:13:39',
        ),
        182 => 
        array (
            'desc' => '读取网关配置',
            'id' => 286,
            'key' => 'read_gateway_configs',
            'order' => 0,
            'table_name' => 'gateway_configs',
            'type' => 0,
            'updated_at' => '2018-03-07 19:13:58',
        ),
        183 => 
        array (
            'desc' => '编辑网关配置',
            'id' => 287,
            'key' => 'edit_gateway_configs',
            'order' => 0,
            'table_name' => 'gateway_configs',
            'type' => 0,
            'updated_at' => '2018-03-07 19:14:14',
        ),
        184 => 
        array (
            'desc' => '新增网关配置',
            'id' => 288,
            'key' => 'add_gateway_configs',
            'order' => 0,
            'table_name' => 'gateway_configs',
            'type' => 0,
            'updated_at' => '2018-03-07 19:14:25',
        ),
        185 => 
        array (
            'desc' => '删除网关配置',
            'id' => 289,
            'key' => 'delete_gateway_configs',
            'order' => 0,
            'table_name' => 'gateway_configs',
            'type' => 0,
            'updated_at' => '2018-03-07 19:14:37',
        ),
        186 => 
        array (
            'desc' => '最近几天的搜索过滤数据。只显示最近天数的数据，0表示不限制',
            'id' => 290,
            'key' => 'search_filter_recent_days',
            'order' => 100,
            'table_name' => 'Advertisement',
            'type' => 0,
            'updated_at' => '2018-03-07 19:16:25',
        ),
        187 => 
        array (
            'desc' => '会话数量控制',
            'id' => 291,
            'key' => 'session_limit',
            'order' => 1,
            'table_name' => 'sessions',
            'type' => 0,
            'updated_at' => '2018-02-13 01:54:45',
        ),
        188 => 
        array (
            'desc' => '广告详情页用户能看到的Audience Targeting Analysis显示行数，策略值为0表示没有限制，有策略值表示限制',
            'id' => 294,
            'key' => 'analysis_audience_list',
            'order' => 0,
            'table_name' => 'AdAnalysis',
            'type' => 0,
            'updated_at' => '2018-03-01 21:15:04',
        ),
        189 => 
        array (
            'desc' => '广告详情页用户能看到的Top countries显示行数，策略值为0表示没有限制，有策略值表示限制',
            'id' => 295,
            'key' => 'analysis_country_list',
            'order' => 0,
            'table_name' => 'AdAnalysis',
            'type' => 0,
            'updated_at' => '2018-03-01 21:14:47',
        ),
        190 => 
        array (
            'desc' => '广告详情页Demography能看到的显示时间限制，格式为2_week，即2周之前的数据，为0表示没有限制',
            'id' => 296,
            'key' => 'analysis_demography_time',
            'order' => 0,
            'table_name' => 'AdAnalysis',
            'type' => 0,
            'updated_at' => '2018-03-01 21:14:20',
        ),
        191 => 
        array (
            'desc' => '广告详情页地图图表显示权限',
            'id' => 297,
            'key' => 'analysis_countrymap_show',
            'order' => 0,
            'table_name' => 'AdAnalysis',
            'type' => 0,
            'updated_at' => '2018-03-01 21:15:24',
        ),
    ));
        
        
    }
}