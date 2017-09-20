<?php

namespace App\Api;

use GuzzleHttp\Client;
use App\ActionLog;
use Log;
use App\Jobs\LogAction;
use App\Api\Curl;
use App\Services\AnonymousUser;
use Illuminate\Support\Facades\Auth;

class AdserAnalysis
{
    /*
     * 获取某个特定广告主的全部分析信息
     */
    static public function getAdserAnalysis($request, $facebook_id)
    {
        $user = Auth::check() ? Auth::user() : AnonymousUser::user($request);
        $params = array(
            'search_result' =>  'mobile_adser',
            'sort'          =>  [
                    'field' =>  'ads_number' ,
                    'order' =>  1
            ],
            'limit'         =>  [0, 1],
            'adser_detail'  =>  1,
            'where'         =>  [
                [
                    'field' =>  'adser_username',
                    'value' =>  $facebook_id,
                ]
            ]
        );
        
        $result = Curl::request($params, env('MOBILE_ADSER_SEARCH'));
        
        if (!$result) return ['error' => 'this pubulisher is no data', 'reason' => $result];          

        if (!array_key_exists('adser_info', $result)) return false;
        // var_dump($user);
        // var_dump($result['total_adser_count']);
        // die();
        dispatch(new LogAction(ActionLog::ACTION_MOBILE_ADSER_ANALYSIS, json_encode($params), "mobile_adser_analysis :" . '初始化次数'.",total_adser_count: " . $result['total_adser_count'], $user->id, $request->ip()));
        $audience = json_decode($result['adser_info'][0]['audience'], true);
        $audienceArray = [];
        $index = 0;
        $download = false;

        if ($audience && array_key_exists('interests', $audience)) {
            // 数据超过20个不显示，加上标示用于前台展示
            //return count($audience['interests']);
           foreach ($audience['interests'] as $key => $value) {
                $audienceArray[$index] = array('interests' => $key, 'count' => $value);
                $index++;
                if ($index > 8) {
                    $audienceArray[$index]['tooMoreInterests'] = true;
                    $download = true;
                    break;
                }
            }
            if (count($audience['interests']) < 10) $audienceArray[count($audience['interests'])+1]['download'] = true;
            $audienceArray = array_chunk($audienceArray, 5, true);
            $result['adser_info'][0]['interests'] = $audienceArray; 
        }
        
        if ($audience && array_key_exists('addr', $audience)) {
            if (count($audience['addr']) > 10) {
                // 数据超过20个不显示，加上标示用于前台展示
                $countrysArray = array_slice($audience['addr'], 0, 9);
                $countrysArray[20]['tooMoreCountry'] = true;
                $countrys = array_chunk($countrysArray, 5, true);
            } else {
                $audience['addr'][count($audience['addr'])+1]['download'] = true;
                $countrys = array_chunk($audience['addr'], 5, true);
            }
            $result['adser_info'][0]['countrys'] = $countrys;
        }
        $result['adser_info'][0]['name'] = $result['adser_info'][0]['adser_name'];
        $result['adser_info'][0]['avatar'] = 'http://192.168.10.174:88'.$result['adser_info'][0]['large_photo'];
        return $result['adser_info'];
    }

    /*
     * 返回特定广告按share、like、comment、total_impression排序的前
     * 20个广告数据，返回 event_id和各自的分类排序的增长率   
     */
    static public function getAdserAdByDesc($request, $facebook_id, $desc)
    {   
        $user = Auth::check() ? Auth::user() : AnonymousUser::user($request);
        $select  = '';
        switch ($desc) {
            case 'share_rate':
                $select = 'event_id,shares,share_rate,shares_per_30d,last_see,small_photo,type,local_picture';
                break;
            case 'like_rate':
                $select = 'event_id,likes,like_rate,likes_per_30d,last_see,small_photo,type,local_picture';
                break;
            case 'comment_rate':
                $select = 'event_id,comments,comment_rate,comments_per_30d,last_see,small_photo,type,local_picture';
                break;
            case 'total_impression':
                $select = 'event_id,total_impression,small_photo,type,local_picture';
                break;
            default:
                break;
        }
        $params = array(
            'search_result' => 'ads',
            'sort'          => [
                'field'     => $desc,
                'order'     => 1
            ],
            'where'         => [
                [
                    'field' => 'adser_username',
                    'value' => $facebook_id,
                    'ads_type'  => 'timeline'
                ]
            ],
            'limit'         => [0, 10],
            "select"        => $select
        );
    
        $result = Curl::request($params, env('AD_SEARCH_URL'));
        // var_dump($result);
        // die();
        switch ($desc) {
            case 'share_rate':
                dispatch(new LogAction(ActionLog::ACTION_MOBILE_ADSER_SHARE_TOPADS, json_encode($params), "mobile_adser_share_topads:" . '获取share次数' . ",total_ad_count:" . $result['count'], $user->id, $request->ip()));
                break;
            case 'like_rate':
                dispatch(new LogAction(ActionLog::ACTION_MOBILE_ADSER_LIKE_TOPADS, json_encode($params), "mobile_adser_like_topads:" . '获取like次数' . ",total_ad_count:" . $result['count'], $user->id, $request->ip()));
                break;
            case 'comment_rate':
                dispatch(new LogAction(ActionLog::ACTION_MOBILE_ADSER_COMMENT_TOPADS, json_encode($params), "mobile_adser_comment_topads:" . '获取comment次数' . ",total_ad_count:" . $result['count'], $user->id, $request->ip()));
                break;
            case 'total_impression':
                dispatch(new LogAction(ActionLog::ACTION_MOBILE_ADSER_IMPRESSIOM_TOPADS, json_encode($params), "mobile_adser_impression_topads:" . '获取impression次数' . ",total_ad_count:" . $result['count'], $user->id, $request->ip()));
                break;
            default:
                break;
        }
        if (!array_key_exists('ads_info', $result)) return false;

        $isFullData = count($result['ads_info']) === 10 ? true : false;
        // 前端需求：当广告个数不足10个时,需要提示下载插件
        //          当广告个数超过10个时,需要提示用户登录到桌面端查看更多的数据
        if ($isFullData) {
            $result['ads_info'][9] = [];
            $result['ads_info'][9] = ['fullDataFlag' => true];
        } else {
            $result['ads_info'][count($result['ads_info'])+1] = ['download' => true]; 
        }
        $data = [];
        //return $result['ads_info'];
        foreach ($result['ads_info'] as $key => $value) {
            if (array_key_exists('type', $value)) {
                if ($value['type'] == 'SingleImage') {
                    $result['ads_info'][$key]['image'] = 'http://192.168.10.174:88' . self::switchToMobileImage($value['local_picture']);
                } else if ($value['type'] == 'SingleVideo') {
                    $local_picture = json_decode($value['local_picture'], true);
                    $result['ads_info'][$key]['image'] = 'http://192.168.10.174:88' . self::switchToMobileImage($local_picture['source']);
                } else if ($value['type'] == 'Carousel') {
                    $local_picture = json_decode($value['local_picture'], true);
                    if (array_key_exists('source', $local_picture[0])) {
                        $result['ads_info'][$key]['image'] = 'http://192.168.10.174:88' . self::switchToMobileImage($local_picture[0]['source']);
                    }
                } else if ($value['type'] == 'Canvas'){
                    $local_picture = json_decode($value['local_picture'], true);
                    if (!is_array($local_picture[0])) {
                        //多此判断的原因是数据格式不一致，会出现不带source的数据，导致出错
                        $result['ads_info'][$key]['image'] = 'http://192.168.10.174:88' . self::switchToMobileImage($local_picture[0]);
                    } else {
                        $result['ads_info'][$key]['image'] = 'http://192.168.10.174:88' . self::switchToMobileImage($local_picture[0]['source']);
                    }
                }
            }
        }
        // return $result['ads_info'];
        $topAds = array_chunk($result['ads_info'], 5, true);

        $data['ads'] = $topAds;

        return $data;
    }

    static public function switchToMobileImage ($url) 
    {  
        if ($url) {
           return str_replace('image', 'mobile_phone_image', $url);
        }
    }

}
