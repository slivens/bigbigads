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
                'desc' => NULL,
                'id' => 99,
                'key' => 'search_times_perday',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:18',
            ),
            1 => 
            array (
                'desc' => NULL,
                'id' => 100,
                'key' => 'result_per_search',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:18',
            ),
            2 => 
            array (
                'desc' => NULL,
                'id' => 101,
                'key' => 'search_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:18',
            ),
            3 => 
            array (
                'desc' => NULL,
                'id' => 102,
                'key' => 'search_sortby',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:18',
            ),
            4 => 
            array (
                'desc' => NULL,
                'id' => 103,
                'key' => 'advanced_search',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:19',
            ),
            5 => 
            array (
                'desc' => NULL,
                'id' => 104,
                'key' => 'save_search',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:19',
            ),
            6 => 
            array (
                'desc' => NULL,
                'id' => 105,
                'key' => 'advertiser_search',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:19',
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
                'desc' => NULL,
                'id' => 107,
                'key' => 'content_search',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:19',
            ),
            9 => 
            array (
                'desc' => NULL,
                'id' => 108,
                'key' => 'audience_search',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:19',
            ),
            10 => 
            array (
                'desc' => NULL,
                'id' => 109,
                'key' => 'date_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:19',
            ),
            11 => 
            array (
                'desc' => NULL,
                'id' => 110,
                'key' => 'format_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:19',
            ),
            12 => 
            array (
                'desc' => NULL,
                'id' => 111,
                'key' => 'duration_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:19',
            ),
            13 => 
            array (
                'desc' => NULL,
                'id' => 112,
                'key' => 'see_times_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:19',
            ),
            14 => 
            array (
                'desc' => NULL,
                'id' => 113,
                'key' => 'lang_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:19',
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
                'desc' => NULL,
                'id' => 115,
                'key' => 'date_sort',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:19',
            ),
            17 => 
            array (
                'desc' => NULL,
                'id' => 116,
                'key' => 'likes_sort',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:19',
            ),
            18 => 
            array (
                'desc' => NULL,
                'id' => 117,
                'key' => 'shares_sort',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:19',
            ),
            19 => 
            array (
                'desc' => NULL,
                'id' => 118,
                'key' => 'video_views_sort',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:20',
            ),
            20 => 
            array (
                'desc' => NULL,
                'id' => 119,
                'key' => 'comment_sort',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:20',
            ),
            21 => 
            array (
                'desc' => NULL,
                'id' => 120,
                'key' => 'duration_sort',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:20',
            ),
            22 => 
            array (
                'desc' => NULL,
                'id' => 121,
                'key' => 'views_sort',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:20',
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
                'desc' => NULL,
                'id' => 124,
                'key' => 'likes_inc_sort',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:20',
            ),
            26 => 
            array (
                'desc' => NULL,
                'id' => 125,
                'key' => 'video_views_inc_sort',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:20',
            ),
            27 => 
            array (
                'desc' => NULL,
                'id' => 126,
                'key' => 'image_download',
                'order' => 0,
                'table_name' => 'export',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:20',
            ),
            28 => 
            array (
                'desc' => NULL,
                'id' => 127,
                'key' => 'video_download',
                'order' => 0,
                'table_name' => 'export',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:20',
            ),
            29 => 
            array (
                'desc' => NULL,
                'id' => 128,
                'key' => 'HD_video_download',
                'order' => 0,
                'table_name' => 'export',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:20',
            ),
            30 => 
            array (
                'desc' => NULL,
                'id' => 129,
                'key' => 'Export',
                'order' => 0,
                'table_name' => 'export',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:20',
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
                'desc' => NULL,
                'id' => 131,
                'key' => 'ad_analysis',
                'order' => 0,
                'table_name' => 'statics',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:21',
            ),
            33 => 
            array (
                'desc' => NULL,
                'id' => 132,
                'key' => 'adser_analysis',
                'order' => 0,
                'table_name' => 'statics',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:21',
            ),
            34 => 
            array (
                'desc' => NULL,
                'id' => 133,
                'key' => 'Realtime_AD_analysis',
                'order' => 0,
                'table_name' => 'statics',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:21',
            ),
            35 => 
            array (
                'desc' => NULL,
                'id' => 134,
                'key' => 'ranking',
                'order' => 0,
                'table_name' => 'ranking',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:21',
            ),
            36 => 
            array (
                'desc' => NULL,
                'id' => 135,
                'key' => 'ranking_export',
                'order' => 0,
                'table_name' => 'ranking',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:21',
            ),
            37 => 
            array (
                'desc' => NULL,
                'id' => 136,
                'key' => 'ranking_by_category',
                'order' => 0,
                'table_name' => 'ranking',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:21',
            ),
            38 => 
            array (
                'desc' => NULL,
                'id' => 137,
                'key' => 'bookmark_support',
                'order' => 0,
                'table_name' => 'bookmark',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:21',
            ),
            39 => 
            array (
                'desc' => NULL,
                'id' => 138,
                'key' => 'bookmark_list',
                'order' => 0,
                'table_name' => 'bookmark',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:21',
            ),
            40 => 
            array (
                'desc' => NULL,
                'id' => 139,
                'key' => 'bookmark_adser_support',
                'order' => 0,
                'table_name' => 'bookmark',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:21',
            ),
            41 => 
            array (
                'desc' => NULL,
                'id' => 140,
                'key' => 'save_count',
                'order' => 0,
                'table_name' => 'bookmark',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:21',
            ),
            42 => 
            array (
                'desc' => NULL,
                'id' => 141,
                'key' => 'monitor_support',
                'order' => 0,
                'table_name' => 'monitor',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:21',
            ),
            43 => 
            array (
                'desc' => NULL,
                'id' => 142,
                'key' => 'monitor_ad_keyword',
                'order' => 0,
                'table_name' => 'monitor',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:21',
            ),
            44 => 
            array (
                'desc' => NULL,
                'id' => 143,
                'key' => 'monitor_advertiser',
                'order' => 0,
                'table_name' => 'monitor',
                'type' => 0,
                'updated_at' => '2017-03-06 01:08:21',
            ),
            45 => 
            array (
                'desc' => NULL,
                'id' => 144,
                'key' => 'call_action_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 01:22:39',
            ),
            46 => 
            array (
                'desc' => NULL,
                'id' => 145,
                'key' => 'advertiser_search',
                'order' => 0,
                'table_name' => 'advertiser',
                'type' => 0,
                'updated_at' => '2017-03-06 03:20:53',
            ),
            47 => 
            array (
                'desc' => NULL,
                'id' => 146,
                'key' => 'domain_search',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 06:07:21',
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
                'desc' => NULL,
                'id' => 148,
                'key' => 'views_inc_sort',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 13:05:13',
            ),
            50 => 
            array (
                'desc' => NULL,
                'id' => 149,
                'key' => 'shares_inc_sort',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 13:12:34',
            ),
            51 => 
            array (
                'desc' => NULL,
                'id' => 150,
                'key' => 'comments_inc_sort',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-06 13:12:35',
            ),
            52 => 
            array (
                'desc' => NULL,
                'id' => 151,
                'key' => 'analysis_overview',
                'order' => 0,
                'table_name' => 'AdAnalysis',
                'type' => 0,
                'updated_at' => '2017-03-06 13:20:59',
            ),
            53 => 
            array (
                'desc' => NULL,
                'id' => 152,
                'key' => 'analysis_link',
                'order' => 0,
                'table_name' => 'AdAnalysis',
                'type' => 0,
                'updated_at' => '2017-03-06 13:20:59',
            ),
            54 => 
            array (
                'desc' => NULL,
                'id' => 153,
                'key' => 'analysis_audience',
                'order' => 0,
                'table_name' => 'AdAnalysis',
                'type' => 0,
                'updated_at' => '2017-03-06 13:20:59',
            ),
            55 => 
            array (
                'desc' => NULL,
                'id' => 154,
                'key' => 'analysis_trend',
                'order' => 0,
                'table_name' => 'AdAnalysis',
                'type' => 0,
                'updated_at' => '2017-03-06 13:20:59',
            ),
            56 => 
            array (
                'desc' => NULL,
                'id' => 155,
                'key' => 'analysis_similar',
                'order' => 0,
                'table_name' => 'AdAnalysis',
                'type' => 0,
                'updated_at' => '2017-03-06 13:21:25',
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
                'desc' => NULL,
                'id' => 161,
                'key' => 'adser_search',
                'order' => 0,
                'table_name' => 'advertiser',
                'type' => 0,
                'updated_at' => '2017-03-06 15:13:12',
            ),
            63 => 
            array (
                'desc' => NULL,
                'id' => 162,
                'key' => 'keyword_times_perday',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-03-07 07:04:38',
            ),
            64 => 
            array (
                'desc' => NULL,
                'id' => 163,
                'key' => 'ad_analysis_times_perday',
                'order' => 0,
                'table_name' => 'AdAnalysis',
                'type' => 0,
                'updated_at' => '2017-03-07 07:05:05',
            ),
            65 => 
            array (
                'desc' => NULL,
                'id' => 164,
                'key' => 'adser_search_times_perday',
                'order' => 0,
                'table_name' => 'advertiser',
                'type' => 0,
                'updated_at' => '2017-03-07 13:05:24',
            ),
            66 => 
            array (
                'desc' => NULL,
                'id' => 165,
                'key' => 'advertiser_collect',
                'order' => 0,
                'table_name' => 'bookmark',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:46',
            ),
            67 => 
            array (
                'desc' => NULL,
                'id' => 166,
                'key' => 'browse_admin',
                'order' => 0,
                'table_name' => 'admin',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:46',
            ),
            68 => 
            array (
                'desc' => NULL,
                'id' => 167,
                'key' => 'browse_database',
                'order' => 0,
                'table_name' => 'admin',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:46',
            ),
            69 => 
            array (
                'desc' => NULL,
                'id' => 168,
                'key' => 'browse_media',
                'order' => 0,
                'table_name' => 'admin',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:46',
            ),
            70 => 
            array (
                'desc' => NULL,
                'id' => 169,
                'key' => 'browse_settings',
                'order' => 0,
                'table_name' => 'admin',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:46',
            ),
            71 => 
            array (
                'desc' => NULL,
                'id' => 170,
                'key' => 'browse_menus',
                'order' => 0,
                'table_name' => 'menus',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:46',
            ),
            72 => 
            array (
                'desc' => NULL,
                'id' => 171,
                'key' => 'read_menus',
                'order' => 0,
                'table_name' => 'menus',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:46',
            ),
            73 => 
            array (
                'desc' => NULL,
                'id' => 172,
                'key' => 'edit_menus',
                'order' => 0,
                'table_name' => 'menus',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:46',
            ),
            74 => 
            array (
                'desc' => NULL,
                'id' => 173,
                'key' => 'add_menus',
                'order' => 0,
                'table_name' => 'menus',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:46',
            ),
            75 => 
            array (
                'desc' => NULL,
                'id' => 174,
                'key' => 'delete_menus',
                'order' => 0,
                'table_name' => 'menus',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:46',
            ),
            76 => 
            array (
                'desc' => NULL,
                'id' => 175,
                'key' => 'browse_pages',
                'order' => 0,
                'table_name' => 'pages',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:46',
            ),
            77 => 
            array (
                'desc' => NULL,
                'id' => 176,
                'key' => 'read_pages',
                'order' => 0,
                'table_name' => 'pages',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:46',
            ),
            78 => 
            array (
                'desc' => NULL,
                'id' => 177,
                'key' => 'edit_pages',
                'order' => 0,
                'table_name' => 'pages',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:46',
            ),
            79 => 
            array (
                'desc' => NULL,
                'id' => 178,
                'key' => 'add_pages',
                'order' => 0,
                'table_name' => 'pages',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:46',
            ),
            80 => 
            array (
                'desc' => NULL,
                'id' => 179,
                'key' => 'delete_pages',
                'order' => 0,
                'table_name' => 'pages',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:46',
            ),
            81 => 
            array (
                'desc' => NULL,
                'id' => 180,
                'key' => 'browse_roles',
                'order' => 0,
                'table_name' => 'roles',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:46',
            ),
            82 => 
            array (
                'desc' => NULL,
                'id' => 181,
                'key' => 'read_roles',
                'order' => 0,
                'table_name' => 'roles',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:46',
            ),
            83 => 
            array (
                'desc' => NULL,
                'id' => 182,
                'key' => 'edit_roles',
                'order' => 0,
                'table_name' => 'roles',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:46',
            ),
            84 => 
            array (
                'desc' => NULL,
                'id' => 183,
                'key' => 'add_roles',
                'order' => 0,
                'table_name' => 'roles',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:46',
            ),
            85 => 
            array (
                'desc' => NULL,
                'id' => 184,
                'key' => 'delete_roles',
                'order' => 0,
                'table_name' => 'roles',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:46',
            ),
            86 => 
            array (
                'desc' => NULL,
                'id' => 185,
                'key' => 'browse_users',
                'order' => 0,
                'table_name' => 'users',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:46',
            ),
            87 => 
            array (
                'desc' => NULL,
                'id' => 186,
                'key' => 'read_users',
                'order' => 0,
                'table_name' => 'users',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:46',
            ),
            88 => 
            array (
                'desc' => NULL,
                'id' => 187,
                'key' => 'edit_users',
                'order' => 0,
                'table_name' => 'users',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:46',
            ),
            89 => 
            array (
                'desc' => NULL,
                'id' => 188,
                'key' => 'add_users',
                'order' => 0,
                'table_name' => 'users',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:46',
            ),
            90 => 
            array (
                'desc' => NULL,
                'id' => 189,
                'key' => 'delete_users',
                'order' => 0,
                'table_name' => 'users',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:47',
            ),
            91 => 
            array (
                'desc' => NULL,
                'id' => 190,
                'key' => 'browse_posts',
                'order' => 0,
                'table_name' => 'posts',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:47',
            ),
            92 => 
            array (
                'desc' => NULL,
                'id' => 191,
                'key' => 'read_posts',
                'order' => 0,
                'table_name' => 'posts',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:47',
            ),
            93 => 
            array (
                'desc' => NULL,
                'id' => 192,
                'key' => 'edit_posts',
                'order' => 0,
                'table_name' => 'posts',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:47',
            ),
            94 => 
            array (
                'desc' => NULL,
                'id' => 193,
                'key' => 'add_posts',
                'order' => 0,
                'table_name' => 'posts',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:47',
            ),
            95 => 
            array (
                'desc' => NULL,
                'id' => 194,
                'key' => 'delete_posts',
                'order' => 0,
                'table_name' => 'posts',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:47',
            ),
            96 => 
            array (
                'desc' => NULL,
                'id' => 195,
                'key' => 'browse_categories',
                'order' => 0,
                'table_name' => 'categories',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:47',
            ),
            97 => 
            array (
                'desc' => NULL,
                'id' => 196,
                'key' => 'read_categories',
                'order' => 0,
                'table_name' => 'categories',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:47',
            ),
            98 => 
            array (
                'desc' => NULL,
                'id' => 197,
                'key' => 'edit_categories',
                'order' => 0,
                'table_name' => 'categories',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:47',
            ),
            99 => 
            array (
                'desc' => NULL,
                'id' => 198,
                'key' => 'add_categories',
                'order' => 0,
                'table_name' => 'categories',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:47',
            ),
            100 => 
            array (
                'desc' => NULL,
                'id' => 199,
                'key' => 'delete_categories',
                'order' => 0,
                'table_name' => 'categories',
                'type' => 0,
                'updated_at' => '2017-03-24 02:42:47',
            ),
            101 => 
            array (
                'desc' => NULL,
                'id' => 200,
                'key' => 'timeline_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-04-09 11:52:52',
            ),
            102 => 
            array (
                'desc' => NULL,
                'id' => 201,
                'key' => 'phone_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-04-09 11:52:52',
            ),
            103 => 
            array (
                'desc' => NULL,
                'id' => 202,
                'key' => 'rightcolumn_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-04-09 11:52:52',
            ),
            104 => 
            array (
                'desc' => NULL,
                'id' => 203,
                'key' => 'advance_likes_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-04-18 08:30:25',
            ),
            105 => 
            array (
                'desc' => NULL,
                'id' => 204,
                'key' => 'advance_shares_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-04-18 08:30:25',
            ),
            106 => 
            array (
                'desc' => NULL,
                'id' => 205,
                'key' => 'advance_comments_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-04-18 08:30:25',
            ),
            107 => 
            array (
                'desc' => NULL,
                'id' => 206,
                'key' => 'advance_video_views_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-04-18 08:30:26',
            ),
            108 => 
            array (
                'desc' => NULL,
                'id' => 207,
                'key' => 'advance_engagement_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-04-18 08:30:26',
            ),
            109 => 
            array (
                'desc' => NULL,
                'id' => 208,
                'key' => 'browse_maillist',
                'order' => 0,
                'table_name' => 'maillist',
                'type' => 0,
                'updated_at' => '2017-05-08 03:21:35',
            ),
            110 => 
            array (
                'desc' => NULL,
                'id' => 209,
                'key' => 'read_maillist',
                'order' => 0,
                'table_name' => 'maillist',
                'type' => 0,
                'updated_at' => '2017-05-08 03:21:35',
            ),
            111 => 
            array (
                'desc' => NULL,
                'id' => 210,
                'key' => 'edit_maillist',
                'order' => 0,
                'table_name' => 'maillist',
                'type' => 0,
                'updated_at' => '2017-05-08 03:21:35',
            ),
            112 => 
            array (
                'desc' => NULL,
                'id' => 211,
                'key' => 'add_maillist',
                'order' => 0,
                'table_name' => 'maillist',
                'type' => 0,
                'updated_at' => '2017-05-08 03:21:35',
            ),
            113 => 
            array (
                'desc' => NULL,
                'id' => 212,
                'key' => 'delete_maillist',
                'order' => 0,
                'table_name' => 'maillist',
                'type' => 0,
                'updated_at' => '2017-05-08 03:21:35',
            ),
            114 => 
            array (
                'desc' => NULL,
                'id' => 213,
                'key' => 'browse_affiliates',
                'order' => 0,
                'table_name' => 'affiliates',
                'type' => 0,
                'updated_at' => '2017-05-30 14:58:36',
            ),
            115 => 
            array (
                'desc' => NULL,
                'id' => 214,
                'key' => 'read_affiliates',
                'order' => 0,
                'table_name' => 'affiliates',
                'type' => 0,
                'updated_at' => '2017-05-30 14:58:36',
            ),
            116 => 
            array (
                'desc' => NULL,
                'id' => 215,
                'key' => 'edit_affiliates',
                'order' => 0,
                'table_name' => 'affiliates',
                'type' => 0,
                'updated_at' => '2017-05-30 14:58:36',
            ),
            117 => 
            array (
                'desc' => NULL,
                'id' => 216,
                'key' => 'add_affiliates',
                'order' => 0,
                'table_name' => 'affiliates',
                'type' => 0,
                'updated_at' => '2017-05-30 14:58:36',
            ),
            118 => 
            array (
                'desc' => NULL,
                'id' => 217,
                'key' => 'delete_affiliates',
                'order' => 0,
                'table_name' => 'affiliates',
                'type' => 0,
                'updated_at' => '2017-05-30 14:58:36',
            ),
            119 => 
            array (
                'desc' => NULL,
                'id' => 223,
                'key' => 'browse_coupons',
                'order' => 0,
                'table_name' => 'coupons',
                'type' => 0,
                'updated_at' => '2017-06-14 02:19:49',
            ),
            120 => 
            array (
                'desc' => NULL,
                'id' => 224,
                'key' => 'read_coupons',
                'order' => 0,
                'table_name' => 'coupons',
                'type' => 0,
                'updated_at' => '2017-06-14 02:19:49',
            ),
            121 => 
            array (
                'desc' => NULL,
                'id' => 225,
                'key' => 'edit_coupons',
                'order' => 0,
                'table_name' => 'coupons',
                'type' => 0,
                'updated_at' => '2017-06-14 02:19:49',
            ),
            122 => 
            array (
                'desc' => NULL,
                'id' => 226,
                'key' => 'add_coupons',
                'order' => 0,
                'table_name' => 'coupons',
                'type' => 0,
                'updated_at' => '2017-06-14 02:19:49',
            ),
            123 => 
            array (
                'desc' => NULL,
                'id' => 227,
                'key' => 'delete_coupons',
                'order' => 0,
                'table_name' => 'coupons',
                'type' => 0,
                'updated_at' => '2017-06-14 02:19:49',
            ),
            124 => 
            array (
                'desc' => NULL,
                'id' => 228,
                'key' => 'search_init_perday',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-08-07 12:42:44',
            ),
            125 => 
            array (
                'desc' => NULL,
                'id' => 229,
                'key' => 'search_limit_perday',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-08-07 12:42:44',
            ),
            126 => 
            array (
                'desc' => NULL,
                'id' => 230,
                'key' => 'search_where_perday',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-08-07 12:42:44',
            ),
            127 => 
            array (
                'desc' => NULL,
                'id' => 231,
                'key' => 'specific_adser_init_perday',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-08-07 12:42:44',
            ),
            128 => 
            array (
                'desc' => NULL,
                'id' => 232,
                'key' => 'specific_adser_limit_perday',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-08-07 12:42:44',
            ),
            129 => 
            array (
                'desc' => NULL,
                'id' => 233,
                'key' => 'specific_adser_where_perday',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-08-07 12:42:44',
            ),
            130 => 
            array (
                'desc' => NULL,
                'id' => 234,
                'key' => 'app_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-08-07 12:42:44',
            ),
            131 => 
            array (
                'desc' => NULL,
                'id' => 235,
                'key' => 'search_total_times',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-08-07 12:42:44',
            ),
            132 => 
            array (
                'desc' => NULL,
                'id' => 236,
                'key' => 'country_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-08-07 12:42:45',
            ),
            133 => 
            array (
                'desc' => NULL,
                'id' => 237,
                'key' => 'emarketing_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-08-07 12:42:45',
            ),
            134 => 
            array (
                'desc' => NULL,
                'id' => 238,
                'key' => 'tracking_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-08-07 12:42:45',
            ),
            135 => 
            array (
                'desc' => NULL,
                'id' => 239,
                'key' => 'affiliate_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-08-07 12:42:45',
            ),
            136 => 
            array (
                'desc' => NULL,
                'id' => 240,
                'key' => 'e_commerceList_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-08-07 12:42:45',
            ),
            137 => 
            array (
                'desc' => NULL,
                'id' => 241,
                'key' => 'advance_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-08-07 12:42:45',
            ),
            138 => 
            array (
                'desc' => NULL,
                'id' => 242,
                'key' => 'bookmark_init_perday',
                'order' => 0,
                'table_name' => 'bookmark',
                'type' => 0,
                'updated_at' => '2017-08-07 12:42:49',
            ),
            139 => 
            array (
                'desc' => NULL,
                'id' => 243,
                'key' => 'bookmark_limit_perday',
                'order' => 0,
                'table_name' => 'bookmark',
                'type' => 0,
                'updated_at' => '2017-08-07 12:42:49',
            ),
            140 => 
            array (
                'desc' => NULL,
                'id' => 244,
                'key' => 'e_commerce_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-08-07 13:54:43',
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
                'desc' => NULL,
                'id' => 246,
                'key' => 'advance_audience_search',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-08-14 02:37:21',
            ),
            143 => 
            array (
                'desc' => NULL,
                'id' => 247,
                'key' => 'audience_age_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-08-14 02:37:21',
            ),
            144 => 
            array (
                'desc' => NULL,
                'id' => 248,
                'key' => 'audience_gender_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-08-14 02:37:21',
            ),
            145 => 
            array (
                'desc' => NULL,
                'id' => 249,
                'key' => 'browse_refunds',
                'order' => 0,
                'table_name' => 'refunds',
                'type' => 0,
                'updated_at' => '2017-08-26 04:08:59',
            ),
            146 => 
            array (
                'desc' => NULL,
                'id' => 250,
                'key' => 'read_refunds',
                'order' => 0,
                'table_name' => 'refunds',
                'type' => 0,
                'updated_at' => '2017-08-26 04:08:59',
            ),
            147 => 
            array (
                'desc' => NULL,
                'id' => 251,
                'key' => 'edit_refunds',
                'order' => 0,
                'table_name' => 'refunds',
                'type' => 0,
                'updated_at' => '2017-08-26 04:08:59',
            ),
            148 => 
            array (
                'desc' => NULL,
                'id' => 252,
                'key' => 'add_refunds',
                'order' => 0,
                'table_name' => 'refunds',
                'type' => 0,
                'updated_at' => '2017-08-26 04:08:59',
            ),
            149 => 
            array (
                'desc' => NULL,
                'id' => 253,
                'key' => 'delete_refunds',
                'order' => 0,
                'table_name' => 'refunds',
                'type' => 0,
                'updated_at' => '2017-08-26 04:08:59',
            ),
            150 => 
            array (
                'desc' => NULL,
                'id' => 254,
                'key' => 'audience_interest_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-10-10 14:51:27',
            ),
            151 => 
            array (
                'desc' => NULL,
                'id' => 255,
                'key' => 'search_mode_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-10-10 14:51:27',
            ),
            152 => 
            array (
                'desc' => NULL,
                'id' => 256,
                'key' => 'first_time_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-10-10 14:51:27',
            ),
            153 => 
            array (
                'desc' => NULL,
                'id' => 257,
                'key' => 'last_time_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-11-16 02:01:13',
            ),
            154 => 
            array (
                'desc' => NULL,
                'id' => 258,
                'key' => 'rang_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-10-10 14:51:27',
            ),
            155 => 
            array (
                'desc' => NULL,
                'id' => 259,
                'key' => 'save_ad_count',
                'order' => 0,
                'table_name' => 'bookmark',
                'type' => 0,
                'updated_at' => '2017-10-10 14:51:31',
            ),
            156 => 
            array (
                'desc' => NULL,
                'id' => 260,
                'key' => 'search_limit_keys_perday',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-10-31 02:46:04',
            ),
            157 => 
            array (
                'desc' => NULL,
                'id' => 261,
                'key' => 'search_limit_without_keys_perday',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-10-31 02:46:04',
            ),
            158 => 
            array (
                'desc' => NULL,
                'id' => 262,
                'key' => 'search_without_key_total_perday',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-10-31 02:46:06',
            ),
            159 => 
            array (
                'desc' => NULL,
                'id' => 263,
                'key' => 'search_key_total_perday',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-10-31 02:46:06',
            ),
            160 => 
            array (
                'desc' => NULL,
                'id' => 264,
                'key' => 'hot_search_times_perday',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-10-31 02:46:06',
            ),
            161 => 
            array (
                'desc' => NULL,
                'id' => 265,
                'key' => 'specific_adser_times_perday',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-10-31 02:46:07',
            ),
            162 => 
            array (
                'desc' => NULL,
                'id' => 266,
                'key' => 'view_count_sort',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-11-01 15:23:14',
            ),
            163 => 
            array (
                'desc' => NULL,
                'id' => 267,
                'key' => 'default_filter',
                'order' => 0,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2017-11-06 15:08:20',
            ),
            164 => 
            array (
                'desc' => NULL,
                'id' => 268,
                'key' => 'browse_permissions',
                'order' => 0,
                'table_name' => 'permissions',
                'type' => 0,
                'updated_at' => '2017-11-16 01:57:32',
            ),
            165 => 
            array (
                'desc' => NULL,
                'id' => 269,
                'key' => 'read_permissions',
                'order' => 0,
                'table_name' => 'permissions',
                'type' => 0,
                'updated_at' => '2017-11-16 01:57:32',
            ),
            166 => 
            array (
                'desc' => NULL,
                'id' => 270,
                'key' => 'edit_permissions',
                'order' => 0,
                'table_name' => 'permissions',
                'type' => 0,
                'updated_at' => '2017-11-16 01:57:32',
            ),
            167 => 
            array (
                'desc' => NULL,
                'id' => 271,
                'key' => 'add_permissions',
                'order' => 0,
                'table_name' => 'permissions',
                'type' => 0,
                'updated_at' => '2017-11-16 01:57:32',
            ),
            168 => 
            array (
                'desc' => NULL,
                'id' => 272,
                'key' => 'delete_permissions',
                'order' => 0,
                'table_name' => 'permissions',
                'type' => 0,
                'updated_at' => '2017-11-16 01:57:32',
            ),
            169 => 
            array (
                'desc' => NULL,
                'id' => 273,
                'key' => 'adser_without_key_total_perday',
                'order' => 0,
                'table_name' => 'advertiser',
                'type' => 0,
                'updated_at' => '2017-12-02 10:04:37',
            ),
            170 => 
            array (
                'desc' => NULL,
                'id' => 274,
                'key' => 'adser_key_total_perday',
                'order' => 0,
                'table_name' => 'advertiser',
                'type' => 0,
                'updated_at' => '2017-12-02 10:04:37',
            ),
            171 => 
            array (
                'desc' => NULL,
                'id' => 275,
                'key' => 'adser_limit_keys_perday',
                'order' => 0,
                'table_name' => 'advertiser',
                'type' => 0,
                'updated_at' => '2017-12-02 10:04:37',
            ),
            172 => 
            array (
                'desc' => NULL,
                'id' => 276,
                'key' => 'adser_limit_without_keys_perday',
                'order' => 0,
                'table_name' => 'advertiser',
                'type' => 0,
                'updated_at' => '2017-12-02 10:04:37',
            ),
            173 => 
            array (
                'desc' => NULL,
                'id' => 277,
                'key' => 'adser_result_per_search',
                'order' => 0,
                'table_name' => 'advertiser',
                'type' => 0,
                'updated_at' => '2017-12-02 10:04:37',
            ),
            174 => 
            array (
                'desc' => NULL,
                'id' => 278,
                'key' => 'adser_init_perday',
                'order' => 0,
                'table_name' => 'advertiser',
                'type' => 0,
                'updated_at' => '2017-12-02 10:04:37',
            ),
            175 => 
            array (
                'desc' => NULL,
                'id' => 279,
                'key' => 'adser_analysis_perday',
                'order' => 0,
                'table_name' => 'advertiser',
                'type' => 0,
                'updated_at' => '2017-12-02 10:04:37',
            ),
            176 => 
            array (
                'desc' => NULL,
                'id' => 280,
                'key' => 'browse_plans',
                'order' => 0,
                'table_name' => 'plans',
                'type' => 0,
                'updated_at' => '2018-02-10 17:22:42',
            ),
            177 => 
            array (
                'desc' => NULL,
                'id' => 281,
                'key' => 'read_plans',
                'order' => 0,
                'table_name' => 'plans',
                'type' => 0,
                'updated_at' => '2018-02-10 17:22:42',
            ),
            178 => 
            array (
                'desc' => NULL,
                'id' => 282,
                'key' => 'edit_plans',
                'order' => 0,
                'table_name' => 'plans',
                'type' => 0,
                'updated_at' => '2018-02-10 17:22:42',
            ),
            179 => 
            array (
                'desc' => NULL,
                'id' => 283,
                'key' => 'add_plans',
                'order' => 0,
                'table_name' => 'plans',
                'type' => 0,
                'updated_at' => '2018-02-10 17:22:42',
            ),
            180 => 
            array (
                'desc' => NULL,
                'id' => 284,
                'key' => 'delete_plans',
                'order' => 0,
                'table_name' => 'plans',
                'type' => 0,
                'updated_at' => '2018-02-10 17:22:42',
            ),
            181 => 
            array (
                'desc' => NULL,
                'id' => 285,
                'key' => 'browse_gateway_configs',
                'order' => 0,
                'table_name' => 'gateway_configs',
                'type' => 0,
                'updated_at' => '2018-02-11 00:13:41',
            ),
            182 => 
            array (
                'desc' => NULL,
                'id' => 286,
                'key' => 'read_gateway_configs',
                'order' => 0,
                'table_name' => 'gateway_configs',
                'type' => 0,
                'updated_at' => '2018-02-11 00:13:41',
            ),
            183 => 
            array (
                'desc' => NULL,
                'id' => 287,
                'key' => 'edit_gateway_configs',
                'order' => 0,
                'table_name' => 'gateway_configs',
                'type' => 0,
                'updated_at' => '2018-02-11 00:13:41',
            ),
            184 => 
            array (
                'desc' => NULL,
                'id' => 288,
                'key' => 'add_gateway_configs',
                'order' => 0,
                'table_name' => 'gateway_configs',
                'type' => 0,
                'updated_at' => '2018-02-11 00:13:41',
            ),
            185 => 
            array (
                'desc' => NULL,
                'id' => 289,
                'key' => 'delete_gateway_configs',
                'order' => 0,
                'table_name' => 'gateway_configs',
                'type' => 0,
                'updated_at' => '2018-02-11 00:13:41',
            ),
            186 => 
            array (
                'desc' => '只显示最近天数的数据，0表示不限制',
                'id' => 290,
                'key' => 'search_filter_recent_days',
                'order' => 100,
                'table_name' => 'Advertisement',
                'type' => 0,
                'updated_at' => '2018-02-13 01:02:08',
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
                'desc' => '',
                'id' => 294,
                'key' => 'analysis_audience_list',
                'order' => 0,
                'table_name' => 'AdAnalysis',
                'type' => 0,
                'updated_at' => '2018-02-27 09:32:15',
            ),
            189 => 
            array (
                'desc' => '',
                'id' => 295,
                'key' => 'analysis_country_list',
                'order' => 0,
                'table_name' => 'AdAnalysis',
                'type' => 0,
                'updated_at' => '2018-02-27 09:32:26',
            ),
            190 => 
            array (
                'desc' => '',
                'id' => 296,
                'key' => 'analysis_demography_time',
                'order' => 0,
                'table_name' => 'AdAnalysis',
                'type' => 0,
                'updated_at' => '2018-02-27 09:33:05',
            ),
            191 => 
            array (
                'desc' => '',
                'id' => 297,
                'key' => 'analysis_countrymap_show',
                'order' => 0,
                'table_name' => 'AdAnalysis',
                'type' => 0,
                'updated_at' => '2018-02-27 09:33:18',
            ),
        ));
        
        
    }
}