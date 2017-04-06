<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use TCG\Voyager\Models\Permission;
use App\Services\AnonymousUser;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\ActionLog;
use App\Role;
use App\Plan;
use Log;

class SearchController extends Controller
{
    /**
     * @$req Reqeust 
     * @$name 权限名称
     * @$params 搜索参数
     */
    protected function updateUsage($req, $name, &$params)
    {
        $user = $this->user();
        $lastParams = $user->getCache($name);
        if ($lastParams != $params) {
            //usage的格式请参考Role::groupedPolicies的说明
            $usage = $user->getUsage($name);
            if (!$usage) {
                throw new \Exception("no permission", -1);
            }
            //如果没有时间信息就以当前时间作为时间信息
            if (count($usage) < 4) {
                $carbon = Carbon::now();
            } else {
                if ($usage[3] instanceof Carbon)
                    $carbon = new Carbon($usage[3]->date, $usage[3]->timezone);
                else
                    $carbon = new Carbon($usage[3]['date'], $usage[3]['timezone']);
            }
            if (!$carbon->isToday()) {
                $usage[2] = 0;
            }
            if ($usage[2] >= intval($usage[1]))
                throw new \Exception("you reached the limit", -2);
            $user->updateUsage($name, $usage[2] + 1, Carbon::now());
            ActionLog::log($name, $params, "{$name}:"  . ($usage[2] + 1));
            $user->setCache($name, $params);
            Log::debug("statics:" . $data);
        }
    }

    /**
     * 广告搜索前先检查参数是否有对应权限，对于无权限的将该参数清空或者通过throw抛出错误;
     * @warning 需要特别注意，参数的'field'与权限值通常不相等。
     */
    protected function checkBeforeAdSearch($user, $params)
    {
        $wheres = $params['where'];
        foreach($wheres as $key => $obj) {
            if ($obj['field'] == "duration_days" && !$user->can('duration_filter')) {
                throw new \Exception("no permission of duration_filter", -1);
            }
        }
        return $params;
    }

    /**
     * 广告搜索数据返回前先做权限检查，将无权限获取的数据清空
     * 如果想返回错误，通过throw抛异常
     */
    protected function checkAfterAdSearch($user, $data)
    {
        if (!isset($data['ads_info']))
            return $data;
        $adsInfo = &$data['ads_info'];
        if (!$user->can('audience_search')) {
            foreach ($adsInfo  as $key => $info) {
                if (isset($info['whyseeads_all'])) {
                    $adsInfo[$key]['whyseeads_all'] = "";    
                }
            }
        }
        return $data;
    }

    /**
     * TODO:待补充和调用 
     */
    protected function checkBeforeAdserSearch($user, $params)
    {
    }

    /**
     * TODO:待补充和调用 
     */
    protected function checkAfterAdserSearch($user, $data)
    {
        return $data;
    }

    public function search(Request $req, $action) {
        $json_data = json_encode($req->except(['action']));
        $remoteurl = "";
        $user = $this->user();
        if (!(Auth::check())) {
            //匿名用户只有adsearch动作，其它动作一律不允许
            if ($action == 'adsearch') {
                if(false===(($req->except(['action'])['limit'][0]==0)&&($req->except(['action'])['limit'][1]==10)))
                    return ;
            }else {
                return ;
            }   
        }
        if ($action == 'adsearch') {
            //检查权限（应该是根据GET的动作参数判断，否则客户端会出现一种情况，当查看收藏时，也会触发搜索资源统计)
            $act = $req->only('action');
            try {
                $json_data = json_encode($this->checkBeforeAdSearch($user, $req->except(['action'])));
            } catch(\Exception $e) {
                return $this->responseError($e->getMessage());
            }
            if (in_array($act["action"], ['search'])) {
                $lastParams = $user->getCache('adsearch.params');
                //参数有变化，开始做搜索次数的判定
                if ($lastParams != $json_data) {
                    $usage = $user->getUsage('search_times_perday');
                    if (!$usage) {
                        return $this->responseError("no search permission");
                    }
                    //有搜索或者过滤条件
                    if (count($req->keys) > 0 || count($req->where) > 0) {
                        //额外信息是由用户自己写入的，初始化时并不存在，当不存在时需要自己初始化。
                        if (count($usage) < 4) {
                            $carbon = Carbon::now();
                        } else {
							//如果已经初始化过，就直接读取；为什么会有两种写法？这是由于从数据库反序列化后的格式跟缓存中的格式不一样导致的。
                            if ($usage[3] instanceof Carbon)
                                $carbon = new Carbon($usage[3]->date, $usage[3]->timezone);
                            else
                                $carbon = new Carbon($usage[3]['date'], $usage[3]['timezone']);
                        }
                        if (!$carbon->isToday()) {
                            $usage[2] = 0;
                        }
                        if ($usage[2] >= intval($usage[1]))
                            return response(["code"=>-1, "desc"=> "you reached search times today, default result will show"], 422);
                        Log::debug("adsearch " . $json_data . json_encode($usage));
                        $user->updateUsage('search_times_perday', $usage[2] + 1, Carbon::now());
                        ActionLog::log("search_times_perday", $json_data, "search_times_perday:"  . ($usage[2] + 1));
                    }
                    $user->setCache('adsearch.params', $json_data);
                }
            } else if ($act["action"] == "statics") {
                try {
                    $this->updateUsage($req, "keyword_times_perday", $json_data);
                } catch(\Exception $e) {
                    if ($e->getCode() == -1)
                        return response(["code"=>-1, "desc"=>"no permission"], 422);
                    else if ($e->getCode() == -2)
                        return response(["code"=>-1, "desc"=>"you reached the limit of statics today"], 422);
                }
            } else if ($act["action"] == "analysis") {
                try {
                    $this->updateUsage($req, "ad_analysis_times_perday", $json_data);
                } catch(\Exception $e) {
                    if ($e->getCode() == -1)
                        return response(["code"=>-1, "desc"=>"no permission"], 422);
                    else if ($e->getCode() == -2)
                        return response(["code"=>-1, "desc"=>"you reached the limit of ad analysis today"], 422);
                }
            }

            $remoteurl = 'http://121.41.107.126:8080/search';
        } else if ($action == "adserSearch") {
            //广告主分析
            try {
                $this->updateUsage($req, "adser_search_times_perday", $json_data);
            } catch(\Exception $e) {
                if ($e->getCode() == -1)
                    return response(["code"=>-1, "desc"=>"no permission"], 422);
                else if ($e->getCode() == -2)
                    return response(["code"=>-1, "desc"=>"you reached the limit of ad analysis today"], 422);
            }
            $remoteurl = 'http://121.41.107.126:8080/adser_search';
        } else if ($action == "adserAnalysis") {
            //curl_setopt($ch, CURLOPT_URL, 'http://121.41.107.126:8080/adser_analysis');
            $remoteurl = 'http://xgrit.xicp.net:5000/adser_analysis';

        } else if ($action == "trends") {
            //获取广告趋势
            $user = Auth::user();
            if(!$user->can('analysis_trend')) return response(["code"=>-1, "desc"=>"you no permission"], 422);
            $remoteurl = 'http://xgrit.xicp.net:5000/adsid_trend';
        } else {
            return response(["code"=>-1, "desc"=>"unsupported action"], 422);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remoteurl);
        /* curl_setopt($ch, CURLOPT_POST, TRUE); */
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json_data))
        );
        /* curl_setopt($ch, CURLOPT_TIMEOUT, 1); */ 
        $result = curl_exec($ch);
        curl_close($ch);
        if (Auth::check()) {
            //检查是否有该用户收藏
            for($i = 0; $i < 1; $i++) {//小技巧
                if ($action == 'adsearch' && $act['action'] == 'search') {
                    $json = json_decode($result, true);
                    if (!isset($json['ads_info'])) 
                        break;
                    foreach($json['ads_info'] as $key => $item) {
                        if ($user->bookmarkItems()->where('type', 0)->where('ident', $item['event_id'])->count()) {
                            /* Log::debug($item['event_id'] . ' is in bookmark'); */
                            $json['ads_info'][$key]['hasBookmark'] = true;
                        }
                    }
                    $result = json_encode($json);
                } else if ($action == 'adserSearch') {
                    $json = json_decode($result, true);
                    if (!isset($json['adser'])) 
                        break;
                    foreach($json['adser'] as $key => $item) {
                        if ($user->bookmarkItems()->where('type', 1)->where('ident', $item['adser_username'])->count()) {
                            $json['adser'][$key]['hasBookmark'] = true;
                        }
                    }
                    //$result = json_encode($json);
                }
            }
        }
        //对返回的数据做权限检查，没有权限的数据部分要清空
        if ($action == 'adsearch') {
            try {
                $result = $this->checkAfterAdSearch($user, json_decode($result, true));
            } catch (\Exception $e) {
                return $this->responseError($e->getMessage());
            }
        } else if ($action == 'adserSearch') {
            //TODO
        } else if ($action == 'trends') {
            //TODO
        }
        return $result;
    }
}