<?php

namespace App\Api;

use GuzzleHttp\Client;

class Adser
{
    static public function getPublishers($params = [], $page = 1, $limit = 10)
    {

        $where = [];
        $keys  = [];

        if (isset($params['id'])) {
            $where[] = [
                'field' => 'adser_username',
                'value' => $params['id']
            ];
        }

        if (isset($params['keywords'])) {
            $keys[] = [
                'string'        => $params['keywords'],
                'search_fields' => 'adser_name,adser_username,username',
            ];
        }

        $client = new Client();

        $result = $client->request('POST', env('MOBILE_ADSER_SEARCH'), [
            'json' => [
                'search_result' => 'mobile_adser',
                'limit'         => [($page - 1) * $limit, $limit],
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
            'page'  => 0,
            'pages' => 0,
            'limit' => 0,
            'next'  => 0,
            'prev'  => 0,
        ];

        $pagination = [];
        $pagination['pages'] = ceil($result['total_adser_count'] / $limit);
        $pagination['page']  = $page;
        $pagination['limit'] = $limit;
        $pagination['total'] = $result['total_adser_count'];    
        $pagination['next']  = $page + 1;
        $pagination['prev']  = $page > 1 ? ($page - 1) : null;

        return [
            'data'          => $result['adser_info'],
            'pagination'    => $pagination,
        ];
    }

    static public function getPublisher($id)
    {
        $result = self::getPublishers(['id' => $id], 1, 1);
        if ($result) {
            return reset($result['data']);
        } else {
            return false;
        }
    }
}