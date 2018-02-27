<?php

use Illuminate\Database\Seeder;

class PoliciesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('policies')->delete();
        
        \DB::table('policies')->insert(array (
            0 => 
            array (
                'created_at' => '2017-01-28 15:29:38',
                'id' => 2,
                'key' => 'online_users',
                'type' => 4,
                'updated_at' => '2017-01-28 15:29:38',
            ),
            1 => 
            array (
                'created_at' => '2017-01-28 15:29:38',
                'id' => 3,
                'key' => 'duration',
                'type' => 4,
                'updated_at' => '2017-01-28 15:29:38',
            ),
            2 => 
            array (
                'created_at' => '2017-01-28 15:29:38',
                'id' => 4,
                'key' => 'ad_date',
                'type' => 4,
                'updated_at' => '2017-01-28 15:29:38',
            ),
            3 => 
            array (
                'created_at' => '2017-01-28 15:29:38',
                'id' => 5,
                'key' => 'ad_update',
                'type' => 4,
                'updated_at' => '2017-01-28 15:29:38',
            ),
            4 => 
            array (
                'created_at' => '2017-01-28 15:29:38',
                'id' => 6,
                'key' => 'platform',
                'type' => 4,
                'updated_at' => '2017-01-28 15:29:38',
            ),
            5 => 
            array (
                'created_at' => '2017-01-29 05:24:29',
                'id' => 7,
                'key' => 'search_times_perday',
                'type' => 2,
                'updated_at' => '2017-01-29 05:24:29',
            ),
            6 => 
            array (
                'created_at' => '2017-01-29 05:24:29',
                'id' => 8,
                'key' => 'result_per_search',
                'type' => 4,
                'updated_at' => '2017-01-29 05:24:29',
            ),
            7 => 
            array (
                'created_at' => '2017-01-29 05:43:45',
                'id' => 9,
                'key' => 'image_download',
                'type' => 2,
                'updated_at' => '2017-01-29 05:43:45',
            ),
            8 => 
            array (
                'created_at' => '2017-01-29 05:43:45',
                'id' => 10,
                'key' => 'video_download',
                'type' => 2,
                'updated_at' => '2017-01-29 05:43:45',
            ),
            9 => 
            array (
                'created_at' => '2017-01-29 05:43:45',
                'id' => 11,
                'key' => 'HD_video_download',
                'type' => 2,
                'updated_at' => '2017-01-29 05:43:45',
            ),
            10 => 
            array (
                'created_at' => '2017-01-29 05:43:45',
                'id' => 12,
                'key' => 'Export',
                'type' => 2,
                'updated_at' => '2017-01-29 05:43:45',
            ),
            11 => 
            array (
                'created_at' => '2017-01-29 05:50:32',
                'id' => 13,
                'key' => 'ranking',
                'type' => 4,
                'updated_at' => '2017-01-29 05:50:32',
            ),
            12 => 
            array (
                'created_at' => '2017-01-29 05:50:32',
                'id' => 14,
                'key' => 'ranking_export',
                'type' => 4,
                'updated_at' => '2017-01-29 05:50:32',
            ),
            13 => 
            array (
                'created_at' => '2017-01-29 05:55:39',
                'id' => 15,
                'key' => 'bookmark_list',
                'type' => 0,
                'updated_at' => '2017-01-29 05:55:39',
            ),
            14 => 
            array (
                'created_at' => '2017-01-29 05:55:40',
                'id' => 16,
                'key' => 'save_ad_count',
                'type' => 0,
                'updated_at' => '2017-01-29 05:55:40',
            ),
            15 => 
            array (
                'created_at' => '2017-01-29 05:55:40',
                'id' => 17,
                'key' => 'save_adser_count',
                'type' => 0,
                'updated_at' => '2017-01-29 05:55:40',
            ),
            16 => 
            array (
                'created_at' => '2017-01-29 05:58:37',
                'id' => 18,
                'key' => 'monitor_ad_keyword',
                'type' => 0,
                'updated_at' => '2017-01-29 05:58:37',
            ),
            17 => 
            array (
                'created_at' => '2017-01-29 05:58:37',
                'id' => 19,
                'key' => 'monitor_advertiser',
                'type' => 0,
                'updated_at' => '2017-01-29 05:58:37',
            ),
            18 => 
            array (
                'created_at' => '2017-03-06 09:05:38',
                'id' => 20,
                'key' => 'save_count',
                'type' => 0,
                'updated_at' => '2017-03-06 09:05:38',
            ),
            19 => 
            array (
                'created_at' => '2017-03-07 15:05:05',
                'id' => 21,
                'key' => 'keyword_times_perday',
                'type' => 2,
                'updated_at' => '2017-03-07 15:05:05',
            ),
            20 => 
            array (
                'created_at' => '2017-03-07 15:05:05',
                'id' => 22,
                'key' => 'ad_analysis_times_perday',
                'type' => 2,
                'updated_at' => '2017-03-07 15:05:05',
            ),
            21 => 
            array (
                'created_at' => '2017-03-07 21:05:24',
                'id' => 23,
                'key' => 'adser_search_times_perday',
                'type' => 2,
                'updated_at' => '2017-03-07 21:05:24',
            ),
            22 => 
            array (
                'created_at' => '2017-08-07 20:42:45',
                'id' => 24,
                'key' => 'search_init_perday',
                'type' => 2,
                'updated_at' => '2017-08-07 20:42:45',
            ),
            23 => 
            array (
                'created_at' => '2017-08-07 20:42:46',
                'id' => 25,
                'key' => 'search_limit_perday',
                'type' => 2,
                'updated_at' => '2017-08-07 20:42:46',
            ),
            24 => 
            array (
                'created_at' => '2017-08-07 20:42:46',
                'id' => 26,
                'key' => 'search_where_perday',
                'type' => 2,
                'updated_at' => '2017-08-07 20:42:46',
            ),
            25 => 
            array (
                'created_at' => '2017-08-07 20:42:46',
                'id' => 27,
                'key' => 'specific_adser_init_perday',
                'type' => 2,
                'updated_at' => '2017-08-07 20:42:46',
            ),
            26 => 
            array (
                'created_at' => '2017-08-07 20:42:46',
                'id' => 28,
                'key' => 'specific_adser_limit_perday',
                'type' => 2,
                'updated_at' => '2017-08-07 20:42:46',
            ),
            27 => 
            array (
                'created_at' => '2017-08-07 20:42:46',
                'id' => 29,
                'key' => 'specific_adser_where_perday',
                'type' => 2,
                'updated_at' => '2017-08-07 20:42:46',
            ),
            28 => 
            array (
                'created_at' => '2017-08-07 20:42:46',
                'id' => 30,
                'key' => 'search_total_times',
                'type' => 0,
                'updated_at' => '2017-08-07 20:42:46',
            ),
            29 => 
            array (
                'created_at' => '2017-08-07 20:42:50',
                'id' => 31,
                'key' => 'bookmark_init_perday',
                'type' => 2,
                'updated_at' => '2017-08-07 20:42:50',
            ),
            30 => 
            array (
                'created_at' => '2017-08-07 20:42:50',
                'id' => 32,
                'key' => 'bookmark_limit_perday',
                'type' => 2,
                'updated_at' => '2017-08-07 20:42:50',
            ),
            31 => 
            array (
                'created_at' => '2017-10-31 10:46:07',
                'id' => 34,
                'key' => 'search_limit_keys_perday',
                'type' => 2,
                'updated_at' => '2017-10-31 10:46:07',
            ),
            32 => 
            array (
                'created_at' => '2017-10-31 10:46:07',
                'id' => 35,
                'key' => 'search_limit_without_keys_perday',
                'type' => 2,
                'updated_at' => '2017-10-31 10:46:07',
            ),
            33 => 
            array (
                'created_at' => '2017-10-31 10:46:08',
                'id' => 36,
                'key' => 'search_without_key_total_perday',
                'type' => 2,
                'updated_at' => '2017-10-31 10:46:08',
            ),
            34 => 
            array (
                'created_at' => '2017-10-31 10:46:08',
                'id' => 37,
                'key' => 'search_key_total_perday',
                'type' => 2,
                'updated_at' => '2017-10-31 10:46:08',
            ),
            35 => 
            array (
                'created_at' => '2017-10-31 10:46:08',
                'id' => 38,
                'key' => 'hot_search_times_perday',
                'type' => 2,
                'updated_at' => '2017-10-31 10:46:08',
            ),
            36 => 
            array (
                'created_at' => '2017-10-31 10:46:08',
                'id' => 39,
                'key' => 'specific_adser_times_perday',
                'type' => 2,
                'updated_at' => '2017-10-31 10:46:08',
            ),
            37 => 
            array (
                'created_at' => '2017-12-02 18:04:37',
                'id' => 40,
                'key' => 'adser_without_key_total_perday',
                'type' => 2,
                'updated_at' => '2017-12-02 18:04:37',
            ),
            38 => 
            array (
                'created_at' => '2017-12-02 18:04:38',
                'id' => 41,
                'key' => 'adser_key_total_perday',
                'type' => 2,
                'updated_at' => '2017-12-02 18:04:38',
            ),
            39 => 
            array (
                'created_at' => '2017-12-02 18:04:38',
                'id' => 42,
                'key' => 'adser_limit_keys_perday',
                'type' => 2,
                'updated_at' => '2017-12-02 18:04:38',
            ),
            40 => 
            array (
                'created_at' => '2017-12-02 18:04:38',
                'id' => 43,
                'key' => 'adser_limit_without_keys_perday',
                'type' => 2,
                'updated_at' => '2017-12-02 18:04:38',
            ),
            41 => 
            array (
                'created_at' => '2017-12-02 18:04:38',
                'id' => 44,
                'key' => 'adser_result_per_search',
                'type' => 2,
                'updated_at' => '2017-12-02 18:04:38',
            ),
            42 => 
            array (
                'created_at' => '2017-12-02 18:04:38',
                'id' => 45,
                'key' => 'adser_init_perday',
                'type' => 2,
                'updated_at' => '2017-12-02 18:04:38',
            ),
            43 => 
            array (
                'created_at' => '2017-12-02 18:04:38',
                'id' => 46,
                'key' => 'adser_analysis_perday',
                'type' => 2,
                'updated_at' => '2017-12-02 18:04:38',
            ),
            44 => 
            array (
                'created_at' => '2018-02-13 00:58:54',
                'id' => 47,
                'key' => 'search_filter_recent_days',
                'type' => 4,
                'updated_at' => '2018-02-13 01:00:09',
            ),
            45 => 
            array (
                'created_at' => '2018-02-13 01:54:02',
                'id' => 48,
                'key' => 'session_limit',
                'type' => 4,
                'updated_at' => '2018-02-13 01:54:02',
            ),
        ));
        
        
    }
}