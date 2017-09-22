<?php

namespace App\Api;

use GuzzleHttp\Client;
use App\Api\Curl;
use Log;
use App\ActionLog;
use App\Jobs\LogAction;
use App\Services\AnonymousUser;
use Illuminate\Support\Facades\Auth;

class Publisher
{
    // 获取全部的广告主信息

    static public function getPublishers($request, $params = [], $page = 1, $perPage = 10)
    {
        $where = [];
        $keys  = [];
        $user = Auth::check() ? Auth::user() : AnonymousUser::user($request);
        if (isset($params['id'])) {
            // 当存id为数组时为搜索多个广告主
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

        $params = array(
            'search_result' =>  'mobile_adser',
            'limit'         =>  [($page - 1) * $perPage, $perPage],
            'where'         =>  $where,
            'keys'          =>  $keys,
            'sort'          =>  [
                'field'     =>  'ads_number',
                'order'     =>  1
            ],
            'select'        =>  'adser_username,large_photo,ads_number,adser_name',
        );
        $jsonData = json_encode($params);
        $result = Curl::request($params, env('MOBILE_ADSER_SEARCH'));

        if (!array_key_exists('adser_info', $result)) return [
            'data'  => [],
            'last'  => 0,
            'total' => 0,
            'isEnd' => true
        ];

        $data = [];
        foreach ($result['adser_info'] as $adser_info) {
            $publisher = [];
            // $publisher为返回的广告主信息,目前只返回每个广告信息的如下字段,前端需要其他字段在此数组扩充
            $publisher['facebook_id'] = $adser_info['adser_username'];
            $publisher['name']        = $adser_info['adser_name'];
            $publisher['avatar']      = env('MOBILE_IMAGE_URL').$adser_info['large_photo'];
            $publisher['ads_total']   = $adser_info['ads_number'];

            $data[$publisher['facebook_id']] = $publisher;
        }
        $lastParams = $user->getCache('adser.params');
        $lastParamsArray = json_decode($lastParams, true);

        if ($lastParamsArray != $params && $perPage != 1) {
            if (!$params['keys'][0]['string'] && $params['limit'][0] == 0)
                dispatch(new LogAction(ActionLog::ACTION_MOBILE_ADSER_INIT, json_encode($params), "mobile_adser_init:" . '初始化次数' . ",total_adser_count: " . $result['total_adser_count'], $user->id, $request->ip()));
            if ($params['keys'][0]['string'] && $params['keys'] && $params['keys'] != $lastParamsArray['keys']) {
                dispatch(new LogAction(ActionLog::ACTION_MOBILE_ADSER_SEARCH, json_encode($params), "mobile_adser_search:" . '搜索次数' . ",total_adser_count: " . $result['total_adser_count'], $user->id, $request->ip()));
            }
            if ($params['keys'] == $lastParamsArray['keys'] && $params['limit'] != $lastParamsArray['limit']) {
                dispatch(new LogAction(ActionLog::ACTION_MOBILE_ADSER_LIMIT, json_encode($params), "mobile_adser_limit:" . '下拉次数' . ",total_adser_count: " . $result['total_adser_count'], $user->id, $request->ip()));
            }
        }
        $user->setCache('adser.params', $jsonData);
        return [
            'data'  => $data,
            'last'  => ceil($result['total_adser_count'] / $perPage),
            'total' => $result['total_adser_count'],        
            'isEnd' => $result['is_end']                            //广告是否结束标示
        ];
    }

    // 获取单个广告主信息,明确知道广告主id
    static public function getPublisher($request, $id)
    {
        $result = self::getPublishers($request, ['id' => $id], 1, 1);
        if ($result) {
            // $data[0] = $result['data'];
            // return $data[0];
            return reset($result['data']);
        } else {
            return false;
        }
    }
}
