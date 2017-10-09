<?php

namespace App\Api;

use GuzzleHttp\Client;

class Owner
{
    static public function getOwners($params = [], $page = 1, $per_page = 10)
    {
        $where = [];
        $keys  = [];

        if (isset($params['id'])) {
            $where[] = [
                'field' => 'adser_username',
                'value' => is_string($params['id'])
                    ? $params['id']
                    : join(',', is_array($params['id'])
                        ? $params['id']
                        : $params['id']->toArray()
                    )
            ];
        }

        if (isset($params['keywords'])) {
            $keys[] = [
                'string' => $params['keywords'],
                'search_fields' => 'adser_name,adser_username,username',
            ];
        }

        if (isset($params['keywords'])) {
            $keys[] = [
                'string' => $params['keywords'],
                'search_fields' => 'adser_name,adser_username,username',
            ];
        }

        $client = new Client();
        $result = $client->request('POST', env('MOBILE_PUBLISHERS_SEARCH_API'), [
            'json' => [
                'search_result' => 'mobile_adser',
                'limit'         => [($page - 1) * $per_page, $per_page],
                'where'         => $where,
                'keys'          => $keys,
                'sort'          => [
                    'field' => 'ads_number',
                    'order' => 1
                ],
                'select'        => 'adser_username,large_photo,ads_number,adser_name',
            ],
        ]);

        $result = json_decode($result->getBody(), true);

        if (!array_key_exists('adser_info', $result)) return [
            'data'  => [],
            'last'  => 0,
            'total' => 0,
        ];

        $data = [];
        foreach ($result['adser_info'] as $adser_info) {
            $publisher = [];

            $publisher['facebook_id'] = $adser_info['adser_username'];
            $publisher['name']        = $adser_info['adser_name'];
            $publisher['avatar']      = env('MOBILE_IMAGE_URL') . $adser_info['large_photo'];
            $publisher['ads_total']   = $adser_info['ads_number'];

            $data[$publisher['facebook_id']] = $publisher;
        }

        return [
            'data'  => $data,
            'last'  => ceil($result['total_adser_count'] / $per_page),
            'total' => $result['total_adser_count'],
        ];
    }

    static public function getOwner($id)
    {
        $result = self::getOwners(['id' => $id], 1, 1);
        if ($result) {
            return reset($result['data']);
        } else {
            return false;
        }
    }

    static public function getOwnerAnalysis($id)
    {
        $client = new Client();
        $result = $client->request('POST', env('MOBILE_PUBLISHERS_SEARCH_API'), [
            'json' => [
                'search_result' => 'mobile_adser',
                'limit'         => [0, 1],
                'select'        => 'sum_impression,avg_frequency,avg_like_rate,avg_share_rate,avg_comment_rate,audience',
                'where'         => [
                    [
                        'field' => 'adser_username',
                        'value' => $id,
                    ]
                ],
            ],
        ]);

        $result = json_decode($result->getBody(), true);
        if (!array_key_exists('adser_info', $result)) return;

        $analysis = reset($result['adser_info']);
        $audience = json_decode($analysis['audience'], true);

        $result = $analysis;
        $result['audience'] = [];

        // 用户性别分布
        if (array_key_exists('gender', $audience)) {
            $result['audience']['gender'] = [
                'male'   => $audience['gender'][0],
                'female' => $audience['gender'][1],
            ];
        }

        // 用户兴趣分布
        if (array_key_exists('interests', $audience)) {
            foreach ($audience['interests'] as $keyword => $amount) {
                $result['audience']['interests'][] = [
                    'keyword' => $keyword,
                    'amount'  => $amount,
                ];
            }
        }

        // 用户年龄分布
        if (array_key_exists('age', $audience)) {
            $result['audience']['age'] = [];
            foreach ($audience['age'] as $key => $value) {
                switch ($key) {
                    case 0:
                        $range = [18, 24];
                        break;
                    case 1:
                        $range = [25, 34];
                        break;
                    case 2:
                        $range = [35, 44];
                        break;
                    case 3:
                        $range = [45, 54];
                        break;
                    case 4:
                        $range = [55, 64];
                        break;
                    case 5:
                        $range = [65, 0];
                        break;
                }

                $result['audience']['age'][] = [
                    'range'  => [
                        'min' => $range[0],
                        'max' => $range[1],
                    ],
                    'amount' => [
                        'male'   => $value[0],
                        'female' => $value[1],
                    ],
                ];
            }
        }

        // 用户国家分布
        if (array_key_exists('addr', $audience)) {
            $result['audience']['country'] = [];
            foreach ($audience['addr'] as $key => $value) {
                $result['audience']['country'][] = [
                    'code'   => strtoupper($value['country']),
                    'amount' => $value['value'],
                ];
            }
        }

        return $result;
    }
}
