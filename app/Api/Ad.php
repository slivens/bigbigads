<?php

namespace App\Api;

use GuzzleHttp\Client;

use Log;
use App\ActionLog;
use App\Jobs\LogAction;
use App\Services\AnonymousUser;
use Illuminate\Support\Facades\Auth;

class Ad
{
    static public function getAds($request, $params = [], $page = 1, $per_page = 10)
    {
        $where = [];
        if (isset($params['publisher'])) {
            $where[] = [
                'field' => 'adser_username',
                'value' => is_string($params['publisher'])
                    ? $params['publisher']
                    : join(',', is_array($params['publisher'])
                        ? $params['publisher']
                        : $params['publisher']->toArray()
                    )
            ];
        }

        if (isset($params['id'])) {
            $where[] = [
                'field' => 'ads_id',
                'value' => is_string($params['id'])
                    ? $params['id']
                    : join(',', is_array($params['id'])
                        ? $params['id']
                        : $params['id']->toArray()
                    )
            ];
        }

        if (isset($params['type'])) {
            $type = 'other';
            if ($params['type'] == 'image') $type = 'SingleImage';
            else if ($params['type'] == 'video') $type = 'SingleVideo';
            else if ($params['type'] == 'carousel') $type = 'Carousel';

            $where[] = ['field' => 'media_type', 'value' => $type];
        }

        $client = new Client();
        $result = $client->request('POST', env('AD_SEARCH_URL'), [
            'json' => [
                'search_result' => 'ads',
                'limit'         => [($page - 1) * $per_page, $per_page],
                'where'         => $where,
            ],
        ]);

        $result = json_decode($result->getBody(), true);

        dispatch(new LogAction(ActionLog::ACTION_MOBILE_ADSER_ADS_ANALYSIS, '请求参数', "mobile_adser_ads_analysis : " . '查看XXX类型的广告分析'.",cache_total_count: " . '总数', '用户id', $request->ip()));

        if (!array_key_exists('ads_info', $result)) return [];

        $data = [];
        foreach ($result['ads_info'] as $ad_info) {
            $ad = [];

            // 事件编号
            $ad['event_id'] = $ad_info['event_id'];

            // 类型
            $type = $ad_info['type'];
            if ($type == 'SingleImage') $ad['type'] = 'image';
            else if ($type == 'SingleVideo') $ad['type'] = 'video';
            else if ($type == 'Carousel') $ad['type'] = 'carousel';
            else $ad['type'] = 'other';

            // 喜爱数量
            if (array_key_exists('likes', $ad_info)) $ad['likes'] = $ad_info['likes'];

            // 分享数量
            if (array_key_exists('shares', $ad_info)) $ad['shares'] = $ad_info['shares'];

            // 评论数量
            if (array_key_exists('shares', $ad_info)) $ad['comments'] = $ad_info['comments'];

            // 链接
            if ($ad['type'] == 'carousel') $ad['link'] = json_decode($ad_info['link'], true);
            else $ad['link'] = $ad_info['link'];

            // 发布者
            $ad['publisher'] = [
                'name'        => $ad_info['adser_name'],
                'avatar'      => $ad_info['small_photo'],
                'facebook_id' => $ad_info['adser_username'],
            ];

            // 内容
            if (array_key_exists('message', $ad_info)) $ad['content']['message'] = $ad_info['message'];

            if ($ad['type'] == 'image' || $ad['type'] == 'other') {
                $ad['content']['media'] = $ad_info['local_picture'];
            } else if ($ad['type'] == 'carousel') {
                $pictures = json_decode($ad_info['local_picture'], true);
                $ad['content']['media'] = [];
                foreach ($pictures as $picture) {
                    $ad['content']['media'][] = $picture['source'];
                }
            } else if ($ad['type'] == 'video') {
                $local_picture = json_decode($ad_info['local_picture'], true);
                $ad['content']['media'] = [
                    'image' => $local_picture['source'],
                    'video' => $local_picture['video'],
                ];
            }

            if ($ad['type'] == 'image' || $ad['type'] == 'video') {
                $other = [];
                if ($ad_info['name']) $other['title'] = $ad_info['name'];
                if ($ad_info['description']) $other['description'] = $ad_info['description'];
                if ($ad_info['caption']) $other['domain'] = $ad_info['caption'];
                if (!empty($other)) $ad['content']['other'] = $other;
            }

            $data[$ad['event_id']] = $ad;
        }

        return $data;
    }

    static public function getAd($request, $id)
    {
        $ads = self::getAds($request, ['id' => $id], 1, 1);
        return reset($ads);
    }
}
