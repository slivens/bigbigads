<?php

namespace App\Api;

use GuzzleHttp\Client;

class Ad
{
    static public function getAds($params = [], $page = 1, $per_page = 10)
    {
        $mobile_media_url = env('MOBILE_IMAGE_URL');
        $body = [];
        $body['search_result'] = 'ads';
        $body['limit']         = [($page - 1) * $per_page, $per_page];
        $body['where']         = [];
        $body['select']        = 'link,caption,description,type,shares,views,likes,comments,message,adser_name,event_id,adser_username,local_picture,small_photo,name,share_rate,shares_per30d,like_rate,likes_per30d,comment_rate,comments_per_30d,total_impression';

        if (isset($params['owner'])) {
            $body['where'][] = [
                'field' => 'adser_username',
                'value' => is_string($params['owner'])
                    ? $params['owner']
                    : join(',', is_array($params['owner'])
                        ? $params['owner']
                        : $params['owner']->toArray()
                    )
            ];
        }

        if (isset($params['id'])) {
            $body['where'][] = [
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

            $body['where'][] = ['field' => 'media_type', 'value' => $type];
        }

        if (isset($params['type'])) {
            $type = 'other';
            if ($params['type'] == 'image') $type = 'SingleImage';
            else if ($params['type'] == 'video') $type = 'SingleVideo';
            else if ($params['type'] == 'carousel') $type = 'Carousel';

            $body['where'][] = ['field' => 'media_type', 'value' => $type];
        }

        if (isset($params['order'])) {
            $body['sort'] = [
                'field' => $params['order'][0],
                'order' => $params['order'][1]
            ];
        }

        $client = new Client();
        $result = $client->request('POST', env('MOBILE_ADS_SEARCH_API'), [
            'json' => $body,
        ]);

        $result = json_decode($result->getBody(), true);

        if (!array_key_exists('ads_info', $result)) return [
            'data'  => [],
            'last'  => 0,
            'total' => 0,
        ];

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

            if (array_key_exists('share_rate', $ad_info)) $ad['share_rate'] = $ad_info['share_rate'];
            if (array_key_exists('like_rate', $ad_info)) $ad['like_rate'] = $ad_info['like_rate'];
            if (array_key_exists('comment_rate', $ad_info)) $ad['comment_rate'] = $ad_info['comment_rate'];
            if (array_key_exists('total_impression', $ad_info)) $ad['total_impression'] = $ad_info['total_impression'];
            if (array_key_exists('shares_per_30d', $ad_info)) $ad['shares_per_30d'] = $ad_info['shares_per_30d'];
            if (array_key_exists('likes_per_30d', $ad_info)) $ad['likes_per_30d'] = $ad_info['likes_per_30d'];
            if (array_key_exists('comment_per_30d', $ad_info)) $ad['comment_per_30d'] = $ad_info['comment_per_30d'];

            // 链接
            if ($ad['type'] == 'carousel') $ad['link'] = json_decode($ad_info['link'], true);
            else $ad['link'] = $ad_info['link'];

            // 发布者
            $ad['owner'] = [
                'name'        => $ad_info['adser_name'],
                'avatar'      => $mobile_media_url . $ad_info['small_photo'],
                'facebook_id' => $ad_info['adser_username'],
            ];

            // 内容
            if (array_key_exists('message', $ad_info)) $ad['content']['message'] = $ad_info['message'];

            if ($ad['type'] == 'image') {
                $ad['content']['media'] = $mobile_media_url . $ad_info['local_picture'];
            } else if ($ad['type'] == 'carousel') {
                $pictures = json_decode($ad_info['local_picture'], true);
                $ad['content']['media'] = [];
                foreach ($pictures as $picture) {
                    $ad['content']['media'][] = $mobile_media_url . $picture['source'];
                }
            } else if ($ad['type'] == 'video') {
                $local_picture = json_decode($ad_info['local_picture'], true);
                $ad['content']['media'] = [
                    'image' => $mobile_media_url . $local_picture['source'],
                    'video' => $mobile_media_url . $local_picture['video'],
                ];
            } else if ($ad['type'] == 'other') {
                $local_picture = json_decode($ad_info['local_picture'], true);

                if (is_array($local_picture)) {
                    $ad['type'] = 'carousel';
                    $ad['content']['media'] = array_map(function ($picture) use ($mobile_media_url) {
                        if (is_array($picture)) return $mobile_media_url . $picture['source'];
                        return $mobile_media_url . $picture;
                    }, $local_picture);
                } else {
                    $ad['type'] = 'image';
                    $ad['content']['media'] = $mobile_media_url . $ad_info['local_picture'];
                }
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

        return [
            'data'  => $data,
            'last'  => ceil($result['total_count'] / $per_page),
            'total' => $result['total_count'],
        ];
    }

    static public function getAd($id)
    {
        $ads = self::getAds(['id' => $id], 1, 1);
        if ($ads) {
            return reset($ads['data']);
        } else {
            return false;
        }
    }
}
