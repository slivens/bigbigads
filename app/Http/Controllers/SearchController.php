<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use TCG\Voyager\Models\Permission;
use App\Services\AnonymousUser;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Jobs\LogAction;
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
            dispatch(new LogAction($name, $params, "{$name}:"  . ($usage[2] + 1), $user->id, $req->ip()));
            $user->setCache($name, $params);
            Log::debug("statics:" . $data);
        }
    }


    /**
     * 攻击检测：目前检测非法请求参数和请求速率，检测到异常就记录到ActionLog
	 * 假设：正常用户平均搜索一次加上查看也要5秒，一小时3600秒，720次。
     * 累计次数，当当前时间与上个时间都在在(n - 1, n]小时，就累加；当前时间与上个时间不在同一个区间，重新计数。当计数超过720次时，记录到ACTION_LOG。
     * @remark 对于攻击的检测，如果没有相应的单元测试手段，将很难覆盖测试
     */
    protected function checkAttack($req, $user)
    {
        //速率的计算
        $key = 'attack_' . $req->ip();
        $def = ['last' => Carbon::now()->toDateTimeString() , 'count' => 0];
        $static = Cache::get($key, $def);
        $last = new Carbon($static['last']);
        $now = Carbon::now();
        /* Log::debug("{$now->hour} and {$last->toDateTimeString()}"); */
        if ($now->hour == $last->hour && $now->diffInHours($last, true) == 0) {
            $static['count']++;
        } else {
            $static['count'] = 1;
        }
        $static['last'] = $now->toDateTimeString();
        Cache::put($key, $static, 1440);
        //每小时不应超过720（以整个小时作为样本)
        if ($static['count'] >= 720) {
            if ($static['count'] == 720) {
                Log::debug("{$req->ip()} may attach the server");
                dispatch(new LogAction("ATTACK_IP_RATE", json_encode($req->all()), json_encode($static), $user->id, $req->ip()));
            }
            return false;
        }

        //参数的判断，过于严格，开发阶段增加额外参数将导致较大的人力沟通，不利于开发，暂不实施
        /* $allowed = ['action', 'is_stat', 'is_why_all', 'keys', 'limit', 'search_result', 'sort', 'topN', 'where']; */
       
        return true;
    }

    /**
     * 广告搜索前先检查参数是否有对应权限，对于无权限的将该参数清空或者通过throw抛出错误;
     * @warning 需要特别注意，参数的'field'与权限值通常不相等。
     */
    protected function checkBeforeAdSearch($user, $params)
    {       
            $wheres = $params['where'];
            if(!Auth::check()) {
                //throw new \Exception("no permission of search", -1);
                if ((array_key_exists('action', $params)) && ($params['action'] != 'analysis')) {
                    $params['search_result'] = 'cache_ads';
                    $params['where'] = [];
                    $params['keys'] = [];
                }        
                return $params;
            }else if(Auth::check() && ($user->hasRole('Free') || $user->hasRole('Standard'))) {
                if ((array_key_exists('keys', $params) && (count($params['keys']) > 0) || count($wheres) > 0)) {
                    $params['search_result'] = 'ads';
                    //免费用户限制在两个月前的时间内的数据，设置role = free 是为了让数据端识别并在一个请求内进行两次搜索，第一次是正常的搜索流程，第二次是获取全部的广告总数，
                    //在一次请求内给出两个总数结果，total_count和all_total_count
                    $freeEndDate = Carbon::now()->subMonths(2)->format("Y-m-d");
                    if ($user->hasRole('Free')) {
                        foreach($params['where'] as $key => $obj) {
                            if ($obj['field'] == "time" && $obj['role'] == 'free' && ($obj['min'] != '2016-01-01' || $obj['max'] != $freeEndDate)) {
                                $obj['min'] = '2016-01-01';
                                $obj['max'] = $freeEndDate;
                            }
                        }
                    }
                    
                }else {
                    $params['search_result'] = 'cache_ads';
                }          
            }
            foreach($params['where'] as $key => $obj) {         
                    if ($obj['field'] == "duration_days" && !$user->can('duration_filter')) {
                        throw new \Exception("no permission of filter", -4001);
                    }
                    if ($obj['field'] == "see_times" && !$user->can('see_times_filter')) {
                        throw new \Exception("no permission of filter", -4001);
                    }
                    if ($obj['field'] == "likes" && !$user->can('advance_likes_filter')) {
                        throw new \Exception("no permission of filter", -4001);
                    }
                    if ($obj['field'] == "shares" && !$user->can('advance_shares_filter')) {
                        throw new \Exception("no permission of filter", -4001);
                    }
                    if ($obj['field'] == "comments" && !$user->can('advance_comments_filter')) {
                        throw new \Exception("no permission of filter", -4001);
                    }
                    if ($obj['field'] == "views" && !$user->can('advance_video_views_filter')) {
                        throw new \Exception("no permission of filter", -4001);
                    }
                    if ($obj['field'] == "engagements" && !$user->can('advance_engagement_filter')) {
                        throw new \Exception("no permission of filter", -4001);
                    }
                    /*if (Auth::check() && $user->hasRole('Free') && $obj['field'] == "time") {
                        $freeEndDate = Carbon::now()->modify('-60 days');
                        $min = Carbon::parse($obj['min']);
                        $max = Carbon::parse($obj['max']);
                        if ($min->gt($freeEndDate)) {
                            throw new \Exception("illegalTime", -4002);
                        }
                        if ($max->gt($freeEndDate)) {
                            $obj['max'] = $freeEndDate->format("Y-m-d");
                        }
                    }*/
                    if ($obj['field'] == "watermark_md5" && !$user->can('analysis_similar')) {
                        $params['where'][$key]['field'] = "";
                        $params['where'][$key]['value'] = "";
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
        if(!$user->can('adser_search')){
            throw new \Exception("no permission of adser_search", -4101);
        }
        
        return $params;
    }

    /**
     * TODO:待补充和调用 
     */
    protected function checkAfterAdserSearch($user, $data)
    {   
        if(!$user->can('adser_search')){
            $data['adser'] = null;
            $date['count'] = 0;
            $data['total_ads_count'] = 0;
            $data['is_end'] = true;
            $data['total_adser_count'] = 0;
        }
        return $data;
    }

    protected function checkAfterAdTrends($user, $data)
    {   
        if (!isset($data['info']))
            return $data;
        if (!$user->can('analysis_trend')){
            $data['info'] = null;
        }
        return $data;
    }

    //暂时能想到的策略是使用用户权限来记录写入log的用户行为，
    //将来可能会对where, limit, init的每日统计做出逻辑处理
    protected function checkAndUpdateUsagePerday($user, $logAction)
    {
        $logActionUsage = $user->getUsage($logAction);
        if (!$logActionUsage) {
            return $this->responseError("no search permission");
        }            
        if (count($logActionUsage) < 4) {
                $carbon = Carbon::now();
            } else {
                //如果已经初始化过，就直接读取；为什么会有两种写法？这是由于从数据库反序列化后的格式跟缓存中的格式不一样导致的。
                if ($logActionUsage[3] instanceof Carbon)
                    $carbon = new Carbon($logActionUsage[3]->date, $logActionUsage[3]->timezone);
                else
                    $carbon = new Carbon($logActionUsage[3]['date'], $logActionUsage[3]['timezone']);
            } 
            if (!$carbon->isToday()) {
                $logActionUsage[2] = 0;
            }
            $user->updateUsage($logAction, $logActionUsage[2] + 1, Carbon::now());
            return $logActionUsage[2] + 1;
    }

    /*
        1.用户包括进入搜索页和下拉滚动条的请求都记录，remark: limit:num
        2.用户空词加上过滤条件时做记录，remark: 搜索结果总数,where
        3.用户填上搜索词搜索做记录，remark：搜索次数，搜索结果总数
        注：这只是初步版本，明后两天会统一给出规范的记录情况和remark 格式规范

        将修改为
        记录类型                记录说明与remark格式
        1.页面初始化记录，      记录初始化次数，limit=0，where和keys为空；remark格式为：search_int_perday:num,cache_total_count
        2.搜索条件变化-where    记录空词条件下过滤条件变化，where不为空，keys为空；remark格式为：search_where_change_perday:num,total_count,all_total_count  
        3.搜索条件变化-key      记录搜索词+任意过滤条件，remark格式为：remark:search_times_perday:num,total_count,all_total_count
        4.滚动条下拉发起请求    记录下拉发起请求，keys和where与上次搜索相同；remark格式:remark:search_limit_change_perday:num

    */
    public function search(Request $req, $action) {
        $json_data = json_encode($req->except(['action']));
        $remoteurl = "";
        $isLogSearchTimes = false;
        $isWhereChange = false;
        $user = $this->user();
        $isGetAdAnalysis = false;
        if (!(Auth::check())) {
            //匿名用户只有adsearch动作，其它动作一律不允许
            if ($action == 'adsearch') {

                if (count($req->where) > 0 || count($req->keys) > 0) {
                    //防止用户未登录直接使用url构造url参数来获取数据
                    //区分出获取广告分析的请求
                    foreach($req->where as $key => $obj) {         
                        if ($obj['field'] == "ads_id") {
                            $isGetAdAnalysis = true;
                        }
                    }
                    if(!$isGetAdAnalysis){
                        return $this->responseError("You should sign in", -4199);
                    }
                }
                if(false === (($req->except(['action'])['limit'][0] % 10 === 0) && ($req->except(['action'])['limit'][0] < 300) && (intval($req->except(['action'])['limit'][1]) === 10)))
                    return ;//TODO:应该抛出错误，返回空白会导致维护困难
            }else {
                return ;
            }   
        }
        if (!$this->checkAttack($req, $user)) {
            return $this->responseError("We detect your ip has abandom behavior", -5000);
        }
        if ($action == 'adsearch') {
            //检查权限（应该是根据GET的动作参数判断，否则客户端会出现一种情况，当查看收藏时，也会触发搜索资源统计)
            $act = $req->only('action');
            try {
                $json_data = json_encode($this->checkBeforeAdSearch($user, $req->except(['action'])));
            } catch(\Exception $e) {
                return $this->responseError($e->getMessage(),$e->getCode());
            }
            if (in_array($act["action"], ['search'])) {
                $lastParams = $user->getCache('adsearch.params');
                $lastParamsArray = json_decode($lastParams, true);
                //参数有变化，开始做搜索次数的判定
                if ($lastParams != $json_data) {
                    $usage = $user->getUsage('search_times_perday');
                    if (!$usage) {
                        return $this->responseError("no search permission");
                    }
                    if ($lastParamsArray['where'] != $req->where && count($req->where) > 0) {
                        $isWhereChange = true;
                    }    
                    //有搜索或者过滤条件
                    //if (count($req->keys) > 0 || count($req->where) > 0) {
                    if(count($req->keys) > 0){
                        //正常搜索才变更每日搜索次数，如果是过滤则不更新每日搜索次数
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
                            return $this->responseError("you reached search times today, default result will show", -4100);
                        Log::debug("adsearch " . $json_data . json_encode($usage));
                        $user->updateUsage('search_times_perday', $usage[2] + 1, Carbon::now());
                        $isLogSearchTimes = true;
                        $searchTimesPerday = $usage[2] + 1;
                        //dispatch(new LogAction("search_times_perday", $json_data, "search_times_perday:"  . ($usage[2] + 1), $user->id, $req->ip()));
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

            $remoteurl = config('services.bigbigads.ad_search_url');
        } else if ($action == "adserSearch") {
            //广告主分析
            try {
                $json_data = json_encode($this->checkBeforeAdserSearch($user, $req->except(['action'])));
            } catch(\Exception $e) {
                return $this->responseError($e->getMessage(),$e->getCode());
            }
            try {
                $this->updateUsage($req, "adser_search_times_perday", $json_data);
            } catch(\Exception $e) {
                if ($e->getCode() == -1)
                    return response(["code"=>-1, "desc"=>"no permission"], 422);
                else if ($e->getCode() == -2)
                    return response(["code"=>-1, "desc"=>"you reached the limit of ad analysis today"], 422);
            }
            $remoteurl = config('services.bigbigads.adser_search_url');
        } else if ($action == "adserAnalysis") {
            //curl_setopt($ch, CURLOPT_URL, 'http://121.41.107.126:8080/adser_analysis');
            $remoteurl = config('services.bigbigads.adser_analysis_url');

        } else if ($action == "trends") {
            //获取广告趋势
            $user = Auth::user();
            if(!$user->can('analysis_trend')) return response(["code"=>-1, "desc"=>"you no permission"], 422);
            $remoteurl = config('services.bigbigads.trends_url');
        } else {
            return response(["code"=>-1, "desc"=>"unsupported action"], 422);
        }
        $t1 = microtime(true);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remoteurl);
        /* curl_setopt($ch, CURLOPT_POST, TRUE); */
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HEADER, 1);//获取头信息
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json_data))
        );
        /* curl_setopt($ch, CURLOPT_TIMEOUT, 1); */ 
        $response = curl_exec($ch);
		if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == '200') {
			$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$header = substr($response, 0, $headerSize);
			$result = substr($response, $headerSize);
        } else {
            //curl如果失败就直接返回错误了，这是个良性错误，当作成功处理，前端遇到此错误的策略应该是
            //先重试，再提示
            Log::info("curl failed:" . json_encode($response));
            return $this->responseError("server is busy, please refresh again", -4202);
        }
        curl_close($ch);
        $t2 = microtime(true);
        /* Log::debug("time cost:" . round($t2 - $t1, 3)); */
        //执行时间超过0.5S的添加到日志中
        if (($t2 - $t1) > 0.5) {
            Log::warning("<{$user->name}, {$user->email}> params:$json_data, time cost:" . round($t2 - $t1, 3));
            if (isset($header))
                Log::info($header);
        }
        $result = trim($result);
        $resultJson = json_decode($result, true);
        if (array_key_exists('error', $resultJson)) {
            return $this->responseError("Your search term is not legal", -4200);
        }
        if ($action == 'adsearch') {
            if (Auth::check()) {
                $json = json_decode($result, true);
                if ($user->hasRole('Free') && array_key_exists("all_total_count", $json)) {
                    $searchResult = "total_count: " . $json['total_count'] . " ,all_total_count: " . $json['all_total_count'];
                }else {
                    $searchResult = "total_count: " . $json['total_count'];
                }

                if (intval($req->limit[0]) == 0 && !$isWhereChange && !$isLogSearchTimes) {
                    $searchInitPerday = $this->checkAndUpdateUsagePerday($user, 'search_init_perday');
                    dispatch(new LogAction("SEARCH_INIT_PERDAY", $json_data, "search_init_perday : " . $searchInitPerday.",cache_total_count: " . $json['total_count'], $user->id, $req->ip()));
                }
                //测试时发现$req->limit对比$lastParams的limit一直是相同的，很奇怪暂时使用这个intval($req->limit[0]) >= 0做判断
                if (intval($req->limit[0]) > 0 && !$isWhereChange && !$isLogSearchTimes) {
                    //所有的下拉请求都要记录,区分出是否有where和key变化，否则会同时一个动作记录多条
                    $searchLimitPerday = $this->checkAndUpdateUsagePerday($user, 'search_limit_perday');
                    dispatch(new LogAction("SEARCH_LIMIT_PERDAY", $json_data, "search_limit_perday: " . $searchLimitPerday, $user->id, $req->ip()));
                }
                //用户不带搜索词使用过滤时记录
                if ($isWhereChange && !$isLogSearchTimes) {
                    $searchWherePerday = $this->checkAndUpdateUsagePerday($user, 'search_where_perday');
                    dispatch(new LogAction("SEARCH_WHERE_CHANGE_PERDAY", $json_data, "search_where_change_perday: " . $searchWherePerday . "," . $searchResult, $user->id, $req->ip()));
                }
                if ($isLogSearchTimes) {
                    dispatch(new LogAction("SEARCH_TIMES_PERDAY", $json_data, "search_times_perday: " . $searchTimesPerday . "," .$searchResult , $user->id, $req->ip()));
                }
            }
        }
        if (Auth::check()) {
            //检查是否有该用户收藏
            for($i = 0; $i < 1; $i++) {//小技巧
                if ($action == 'adsearch') {
                    $json = json_decode($result, true);
                    if (!isset($json['ads_info'])) {
                        break;
                    }
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
                //cache_ads接口返回时带有NUL不可见字符，会导致json解析错误
                $result = $this->checkAfterAdSearch($user, json_decode($result, true));
            } catch (\Exception $e) {
                return $this->responseError($e->getMessage(),$e->getCode());
            }
        } else if ($action == 'adserSearch') {
            //TODO
            try {
                $result = $this->checkAfterAdserSearch($user, json_decode($result, true));
            } catch (\Exception $e) {
                return $this->responseError($e->getMessage(),$e->getCode());
            }
        } else if ($action == 'trends') {
            try {
                $result = $this->checkAfterAdTrends($user, json_decode($result, true));
            } catch (\Exception $e) {
                return $this->responseError($e->getMessage(),$e->getCode());
            }
        }
        return $result;
    }
}
