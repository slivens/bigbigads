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
use App\ActionLog;
use Log;
use App\HotWord;
use App\Jobs\LogAbnormalAction;
use App\ServiceTerm;
use App\Exceptions\GenericException;

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
     * @param string $ident 根据标识监测一定时间段的访问次数，$ident为IP地址则根据IP限制，为email则根据该email限制
     * @param App\User $user 
     */
    protected function throttleByIdent($ident, $user)
    {
        //速率的计算
        $key = 'attack_' . $ident;
        $def = ['last' => Carbon::now()->toDateTimeString() , 'count' => 0];
        $cache = Cache::get($key, $def);
        $last = new Carbon($cache['last']);
        $now = Carbon::now();
        /* Log::debug("{$now->hour} and {$last->toDateTimeString()}"); */
        if ($now->hour == $last->hour && $now->diffInHours($last, true) == 0) {
            $cache['count']++;
        } else {
            $cache['count'] = 1;
        }
        $cache['last'] = $now->toDateTimeString();
        Cache::put($key, $cache, 1440);
        //每小时不应超过720（以整个小时作为样本)
        if ($cache['count'] >= 720) {
            if ($cache['count'] == 720) {
                Log::debug("{$ident} may attack the server");
                dispatch(new LogAction(ActionLog::ACTION_ATTACK_RATE, json_encode(request()->all()), $ident . ':' . json_encode($cache), $user->id, request()->ip()));
            }
            return false;
        }
        return true;
    }

    /**
     * 攻击检测：目前检测非法请求参数和请求速率，检测到异常就记录到ActionLog
     * 假设：正常用户平均搜索一次加上查看也要5秒，一小时3600秒，720次。
     * 累计次数，当当前时间与上个时间都在在(n - 1, n]小时，就累加；当前时间与上个时间不在同一个区间，重新计数。当计数超过720次时，记录到ACTION_LOG。
     * @remark 对于攻击的检测，如果没有相应的单元测试手段，将很难覆盖测试
     */
    protected function checkAttack($req, $user)
    {
        if (!$this->throttleByIdent($req->ip(), $user))
            return false;
        if ($this->isAnonymous())
            return true;
        return $this->throttleByIdent($user->email, $user);
        //速率的计算
        /* $key = 'attack_' . $req->ip(); */
        /* $def = ['last' => Carbon::now()->toDateTimeString() , 'count' => 0]; */
        /* $static = Cache::get($key, $def); */
        /* $last = new Carbon($static['last']); */
        /* $now = Carbon::now(); */
        /* /1* Log::debug("{$now->hour} and {$last->toDateTimeString()}"); *1/ */
        /* if ($now->hour == $last->hour && $now->diffInHours($last, true) == 0) { */
        /*     $static['count']++; */
        /* } else { */
        /*     $static['count'] = 1; */
        /* } */
        /* $static['last'] = $now->toDateTimeString(); */
        /* Cache::put($key, $static, 1440); */
        /* //每小时不应超过720（以整个小时作为样本) */
        /* if ($static['count'] >= 720) { */
        /*     if ($static['count'] == 720) { */
        /*         Log::debug("{$req->ip()} may attack the server"); */
        /*         dispatch(new LogAction(ActionLog::ACTION_ATTACK_IP_RATE, json_encode($req->all()), json_encode($static), $user->id, $req->ip())); */
        /*     } */
        /*     return false; */
        /* } */

        /* //参数的判断，过于严格，开发阶段增加额外参数将导致较大的人力沟通，不利于开发，暂不实施 */
        /* /1* $allowed = ['action', 'is_stat', 'is_why_all', 'keys', 'limit', 'search_result', 'sort', 'topN', 'where']; *1/ */
       
        /* return true; */
    }

    /**
     * 广告搜索前先检查参数是否有对应权限，对于无权限的将该参数清空或者通过throw抛出错误;
     * @warning 需要特别注意，参数的'field'与权限值通常不相等。
     */
    protected function checkBeforeAdSearch($user, $params, $action, $req)
    {       
            $wheres = $params['where'];
            $resultPerSearch = $user->getUsage('result_per_search');
            $isCanSort = true; 
            $adsTypePermissions = ['timeline' => 'timeline_filter', 'rightcolumn' => 'rightcolumn_filter', 'phone' => 'phone_filter', 'suggested app' => 'app_filter'];
            
            if (!array_key_exists('limit', $params)) {
                dispatch(new LogAbnormalAction('', json_encode($params), 'lack of limit params', $user->id, $req->ip()));
                throw new \Exception("Illegal limit params", -4300);
            }
            if (($params['limit'][0] % 10 != 0) || ($params['limit'][0] >= $resultPerSearch[1])) {
                Log::warning("<{$user->name}, {$user->email}> request legal limit params : {$params['limit'][0]}");
                throw new \Exception("Illegal limit params", -4300);
            }
            if(!Auth::check()) {
                //throw new \Exception("no permission of search", -1);
                /*
                    排除analysis的原因是未登录用户是使用cache_ads的接口，无任何的过滤功能，
                    会造成未登录的时候详情页打开都是显示第一个广告的Bug。
                */
                if ($action['action'] && $action['action'] != 'analysis') {
                    $params['search_result'] = 'cache_ads';
                    $params['where'] = [];
                    $params['keys'] = [];
                    $params['sort']['field'] = 'default';
                }        
                return $params;
            }else if(Auth::check() && ($user->hasRole('Free') || $user->hasRole('Standard') || $user->hasRole('Lite'))) {
                if (array_key_exists('keys', $params) && (count($params['keys']) > 0) || count($wheres) > 0 || (array_key_exists('sort', $params) && $params['sort']['field'] != 'default')) {
                    $params['search_result'] = 'ads';
                    $isHasTime = false;
                    //新增free用户在总搜索次数在没有超过10次(暂定)的情况下，结合voyager setting
                    //来控制是否强制在三个月内的数据
                    $isLimitGetAllAds = true;
                    $searchTotalTimes = $user->getUsage('search_total_times');
                    if ($searchTotalTimes[2] < 10 && \Voyager::setting('free_role_get_all_ads') == "true") {
                        $isLimitGetAllAds = false;
                    }
                    //免费用户限制在三个月前的时间内的数据，设置role = free 是为了让数据端识别并在一个请求内进行两次搜索，第一次是正常的搜索流程，第二次是获取全部的广告总数，
                    //在一次请求内给出两个总数结果，total_count和all_total_count
                    $freeEndDate = Carbon::now()->subMonths(3)->format("Y-m-d");
                    $LiteEndDate = Carbon::now()->subWeeks(2)->format("Y-m-d");
                    //后台限制没有使用postman的接口测试工具做测试无效，深刻教训：以后的后台测试会以postman测试为准，使用dd打印会漏情况和测试无效。
                    //发现会对获取广告收藏和广告分析页拦截，需要根据action来区分,已开放的功能内，只有search和adser有次限制
                    //分为两种情况：1.修改time的值
                    //              2.直接删除where的time选项
                    if ($action['action'] == 'search' || $action['action'] == 'adser') {
                        if ($user->hasRole('Free')) {
                            foreach($params['where'] as $key => $obj) {
                                if (array_key_exists('field', $obj) && $obj['field'] === 'time' && array_key_exists('min', $obj)) {
                                    $isHasTime = true;
                                    if ($isLimitGetAllAds) {
                                        // 解决bug当用户使用advance过滤时同样有min,max键值出现时被错误覆盖,
                                        if ($obj['min'] != '2016-01-01' || $obj['max'] != $freeEndDate) {
                                            $params['where'][$key]['min'] = '2016-01-01';
                                            $params['where'][$key]['max'] = $freeEndDate;
                                        }
                                    }
                                }
                            }
                            if (!$isHasTime) {
                                throw new \Exception("illegal time", -4198);
                            }
                        }
                        if ($user->hasRole('Lite')) {
                            foreach($params['where'] as $key => $obj) {
                                if (array_key_exists('field', $obj) && $obj['field'] === 'time' && array_key_exists('min', $obj)) {
                                    $isHasTime = true;
                                    if ($isLimitGetAllAds) {
                                        // 解决bug当用户使用advance过滤时同样有min,max键值出现时被错误覆盖,
                                        if ($obj['min'] != '2016-01-01' || $obj['max'] != $LiteEndDate) {
                                            $params['where'][$key]['min'] = '2016-01-01';
                                            $params['where'][$key]['max'] = $LiteEndDate;
                                        }  
                                    }        
                                }
                            }
                            if (!$isHasTime) {
                                throw new \Exception("illegal time", -4198);
                            }
                        }
                    }
                } else {
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
                    // todo 新增功能不做优化, 减小审核难度，下次另外提交优化
                    if ($obj['field'] == "tracking" && !$user->can('tracking_filter')) {
                        throw new \Exception("no permission of filter", -4001);
                    }
                    if ($obj['field'] == "affiliate" && !$user->can('affiliate_filter')) {
                        throw new \Exception("no permission of filter", -4001);
                    }
                    if ($obj['field'] == "e_commerce" && !$user->can('e_commerce_filter')) {
                        throw new \Exception("no permission of filter", -4001);
                    }
                    if ($obj['field'] == "state" && !$user->can('country_filter')) {
                        throw new \Exception("no permission of filter", -4001);
                    }
                    if ($obj['field'] == "objective" && !$user->can('objective_filter')) {
                        throw new \Exception("no permission of filter", -4001);
                    }
                    if ($obj['field'] == "audience_age" && !$user->can('audience_age_filter')) {
                        throw new \Exception("no permission of filter", -4001);
                    }
                    if ($obj['field'] == "audience_gender" && !$user->can('audience_gender_filter')) {
                        throw new \Exception("no permission of filter", -4001);
                    }
                    if ($obj['field'] == "media_type" && !$user->can('format_filter')) {
                        throw new \Exception("no permission of filter", -4001);
                    }
                    if ($obj['field'] == "buttondesc" && !$user->can('call_action_filter')) {
                        throw new \Exception("no permission of filter", -4001);
                    }
                    if ($obj['field'] == "ad_lang" && !$user->can('lang_filter')) {
                        throw new \Exception("no permission of filter", -4001);
                    }
                    if ($obj['field'] == "audience_interest" && !$user->can('audience_interest_filter')) {
                        throw new \Exception("no permission of filter", -4001);
                    }
                    if ($obj['field'] == "watermark_md5" && !$user->can('analysis_similar')) {
                        $params['where'][$key]['field'] = "";
                        $params['where'][$key]['value'] = "";
                    }
                    //拦截postman发起无权限ads type过滤的请求
                    if ($obj['field'] == "ads_type") {
                        $ads_type = $obj['value'];
                        if (!$user->can($adsTypePermissions[$ads_type])) {
                            throw new \Exception("no permission of ads type", -4003);
                        }  
                    }
                    $params['where'][$key] = $this->AutoComplementedAdvanceFilter($key, $obj, $user, $params['where'][$key]);
            }
            //使用数组来处理过滤参数和权限名称不一致的情况比使用switch更优雅。
            $sortPermissions = ['last_view_date' => 'date_sort', 'duration_days' => 'duration_sort', 'engagements' => 'engagements_sort', 'views' => 'views_sort', 'engagements_per_7d' => 'engagement_inc_sort',
                                'views_per_7d' => 'views_inc_sort', 'likes' => 'likes_sort', 'shares' => 'shares_sort', 'comments' => 'comment_sort', 'likes_per_7d' => 'likes_inc_sort', 'shares_per_7d' => 'shares_inc_sort',
                                'comments_per_7d' => 'comments_inc_sort', 'view_count' => 'view_count_sort', 'default' => 'default_filter'];
            $key = $sortPermissions[$params['sort']['field']];
            if ($key && !$user->can($key)) {
                throw new \Exception("no permission of sort", -4002);
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

    protected function checkIsHotWord($key) {
        if ($key[0]['string']) {
            $hotword = HotWord::where("keyword", $key[0]['string'])->first();
            if ($hotword instanceof HotWord) {
                return true;
            } else {
                return false;
            }
        } 
        return false;
    }

    /**
     * 新增需求:
     * 统计全部的空词请求    空词 + 任何条件过滤 + 下拉
     * 统计全部的非空词请求 非空词 + 任何条件过滤 + 下拉
     */
    protected function updateAdSearchResource($req, $user)
    {
        if (count($req->keys) > 0 && $req->keys[0]['string']) {
            $this->checkAndUpdateUsagePerday($user, 'search_key_total_perday');
            $searchKeyTotalPerday = $user->getUsage('search_key_total_perday');
            if ($searchKeyTotalPerday[2] >= intval($searchKeyTotalPerday[1])) {
                throw new \Exception("you reached search times today, default result will show", -4100);
            }
        } else {
            $this->checkAndUpdateUsagePerday($user, 'search_without_key_total_perday');
            $searchWithoutKeyTotalPerday = $user->getUsage('search_without_key_total_perday');
            if ($searchWithoutKeyTotalPerday[2] >= intval($searchWithoutKeyTotalPerday[1])) {
                throw new \Exception("you reached search times today, default result will show", -4100);
            }
        }
    }

    /**
     * 新增需求：
     * 统计特别广告主下的请求 任何条件过滤 + 下拉
     */
    protected function updateSpecificAdserResource($user)
    {
        $this->checkAndUpdateUsagePerday($user, 'specific_adser_times_perday');
        $specificAdserTimesPerday = $user->getUsage('specific_adser_times_perday');
        if ($specificAdserTimesPerday[2] >= intval($specificAdserTimesPerday[1])) {
            throw new \Exception("you reached search times today, default result will show", -4100);
        }
    }

    /**
     * 轮询所有搜索资源使用情况，某一资源超过限制全部搜索资源该日禁用
     */
    protected function checkIsRestrictGetAdResource($req, $user, $jsonData)
    {
        $searchPolicyArray = [
            'specific_adser_times_perday'       => ActionLog::ACTION_SEARCH_RESTRICT_PERDAY_ADSER,
            'search_key_total_perday'           => ActionLog::ACTION_SEARCH_KEY_RESTRICT,
            'search_without_key_total_perday'   => ActionLog::ACTION_SEARCH_WITHOUT_KEY_RESTRICT,
            'search_times_perday'               => ActionLog::ACTION_SEARCH_TIMES_PERDAY_RESTRICT,
            'ad_analysis_times_perday'          => ActionLog::ACTION_AD_ANALYSIS_TIMES_PERDAY_RESTRICT
        ];
        foreach ($searchPolicyArray as $key => $value) {
            $usage = $user->getUsage($key);
            if (count($usage) < 4) {
                $carbon = Carbon::now();
            } else {
                if ($usage[3] instanceof Carbon)
                    $carbon = new Carbon($usage[3]->date, $usage[3]->timezone);
                else
                    $carbon = new Carbon($usage[3]['date'], $usage[3]['timezone']);
            }
            if ($usage[2] >= intval($usage[1]) && $carbon->isToday()) {
                dispatch(new LogAction($value, $jsonData, $key . ': RESTRICT', $user->id, $req->ip()));
                throw new \Exception("you reached search times today, default result will show", -4100);
            }
        }
    }

    /*
    *   用户服务条款检查
    *   当用户上次确认版本低于现在的服务条款版本, 限制使用搜索, 点击确认后恢复使用
    *   ServiceTerm与\Voyager::setting的值是一个低频变化的内容。 但是在每次搜索流程上都会查询一遍，每次都增加几十MS的响应时间是不必要的。
    *   在该函数下面，将上面的说明添加进注释
    *   TODO:应该在后续优化为从缓存中读取
    *   TODO:出于调试目的，暂时屏蔽
    */
    public function checkServiceTermsVersion()
    {
        /* $user = Auth::user(); */
        /* if (!$user) return; */
        /* $serviceTerm = ServiceTerm::where('user_id', $user->id)->first(); */
        /* if (!$serviceTerm || $serviceTerm->version != intval(\Voyager::setting('service_terms_version'))) { */
        /*     throw new \Exception(trans('messages.service_term'), GenericException::ERROR_CODE_SHOULD_AGREE_TERM); */
        /* } */
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
    /*
        free用户新增的all_total_count字段,要求过滤或者带有搜索词的请求前端必须带上time过滤,
        限制上在两个月前的时间,否则为非法搜索.
    */

    /**
     * 免费用户邮箱有效性检查, 检查未通过则限制获取广告数据
     * 目前只检查2017-7-31 23:59:59之前的用户
     */
    protected function checkEmailIsEffective($req, $user)
    {
        $emailVerification = \Voyager::setting('check_email_validity');
        $checkTime = Carbon::create(2017, 7, 31, 23, 59, 59);

        // 检查用户邮箱有效性voyager后端控制开关; 0 为关闭邮箱有效性检查
        if (!$emailVerification) return;
        
        // 检查的用户对象为 2017-07-31号之前注册的免费账号
        if ($user instanceof AnonymousUser) return;
        if (!$user->isFree()) return;

        if ($user->isFree() && $user->created_at->gt($checkTime)) return;

        // 新增is_check字段，标记强制免费用户提供一个有效的邮箱是否验证
        // 强制所有用户提供一个有效的邮箱
        // TODO:出于调试目的，暂时屏蔽
        
        /* if ($user->is_check == 0) { */
        /*     dispatch(new LogAction(ActionLog::ACTION_CHECK_EMAIL_EFFECTIVE, '', trans('messages.effective_email'), $user->id, $req->ip())); */
        /*     throw new \Exception("you must effective email", -4999); */
        /* } */
    }

    /**
     * 支持用户engagement 选项只填入最大值或者最小值，自动填充缺失项
     * 目前max默认设置为10亿,min默认设置为0
     */
    public function AutoComplementedAdvanceFilter($key, $obj, $user, $params) {
        $advanceFilter = [
            'likes'         => 'advance_likes_filter',
            'shares'        => 'advance_shares_filter',
            'comments'      => 'advance_comments_filter',
            'views'         => 'advance_video_views_filter',
            'engagements'   => 'advance_engagement_filter',
        ];
        foreach($advanceFilter as $item => $permission) {
            if ($obj['field'] == $item && !$user->can($permission)) {
                throw new \Exception("no permission of filter", -4001);
            } else if ($obj['field'] == $item && $user->can($permission)) {
                if (!$obj['min'] && $obj['max']) $params['min'] = 0;
                if ($obj['min'] && !$obj['max']) $params['max'] = 100000000;
            }
        }
        return $params;
    }

    public function search(Request $req, $action) {
        $reqParams = $req->except(['action']);
        $jsonData = json_encode($reqParams);
        $remoteurl = "";
        //$isLogSearchTimes = false;
        $isWhereChange = false;
        $user = $this->user();
        $isGetAdAnalysis = false;
        $subAction = '';
        $isHotWord = false;
        if (!(Auth::check())) {
            //匿名用户只有adsearch动作，其它动作一律不允许
            if ($action == 'adsearch') {
                try {
                    if (count($req->where) > 0 || count($req->keys) > 0) {
                        //防止用户未登录直接使用url构造url参数来获取数据
                        //区分出获取广告分析的请求
                        foreach($req->where as $key => $obj) {
                            if ($obj['field'] == "ads_id") {
                                $isGetAdAnalysis = true;
                            }
                        }
                        if(!$isGetAdAnalysis){
                            return response()->fail(GenericException::ERROR_CODE_SHOULD_SIGNIN, "You should sign in");
                        }
                    }
                    if(false === (($reqParams['limit'][0] % 10 === 0) && ($reqParams['limit'][0] < 300) && (intval($reqParams['limit'][1]) === 10)))
                        return ;//TODO:应该抛出错误，返回空白会导致维护困难
                } catch (\Exception $e) {
                    //记录匿名用户伪造url参数的情况
                    Log::warning("{$req->ip()} : <{$user->name}, {$user->email}> Anonymous user illegal request params: $jsonData");
                    dispatch(new LogAbnormalAction($e->getMessage(), $jsonData, 'Anonymous user illegal request', $user->id, $req->ip()));
                }
            }else {
                return ;
            }   
        }
        if (!$this->checkAttack($req, $user)) {
            return $this->responseError("We detect your ip has abandom behavior", -5000);
        }
        try {
            $this->checkServiceTermsVersion();
            $this->checkEmailIsEffective($req, $user);
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(),$e->getCode());
        }
        if ($action == 'adsearch') {
            //检查权限（应该是根据GET的动作参数判断，否则客户端会出现一种情况，当查看收藏时，也会触发搜索资源统计)
            $act = $req->only('action');
            if (!$act["action"]) {
                Log::warning("{$req->ip()} : <{$user->name}, {$user->email}> illegal search request");
                return $this->responseError("Illegal search request", -6000);
            }
            try {
                $jsonData = json_encode($this->checkBeforeAdSearch($user, $reqParams, $act, $req));
            } catch(\Exception $e) {
                return $this->responseError($e->getMessage(),$e->getCode());
            }
            if (in_array($act["action"], ['search'])) {
                $lastParams = $user->getCache('adsearch.params');
                $lastParamsArray = json_decode($lastParams, true);
                //参数有变化，开始做搜索次数的判定
                if ($lastParams != $jsonData) {
                    $usage = $user->getUsage('search_times_perday');
                    if (!$usage) {
                        return $this->responseError("no search permission");
                    }
                    if ($lastParamsArray['where'] != $req->where && count($req->where) > 0) {
                        $isWhereChange = true;
                    }    
                    //有搜索或者过滤条件
                    //if (count($req->keys) > 0 || count($req->where) > 0) {
                    /*
                     * 需要加上limit判断,区分出非空词下的下拉请求
                     * 搜索统计为：非空词条件下任何过滤条件的首次请求
                     */
                    if (count($req->keys) > 0 && $req->keys[0]['string'] && $req->limit['0'] === 0) {
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
                        Log::debug("adsearch " . $jsonData . json_encode($usage));
                        $user->updateUsage('search_times_perday', $usage[2] + 1, Carbon::now());
                        //$isLogSearchTimes = true;
                        $searchTimesPerday = $usage[2] + 1;
                        $subAction = 'search';
                        //dispatch(new LogAction("search_times_perday", $json_data, "search_times_perday:"  . ($usage[2] + 1), $user->id, $req->ip()));
                    }

                    //search 页面初始化                   
                    if (count($req->keys) === 0 && count($req->where) === 0 && $req->limit['0'] === 0 && $req->sort['field'] === 'default') {
                        $subAction = 'init';
                    }
                    //search 页面下拉滚动条
                    if ($req->limit['0'] != 0 && $req->limit['0'] != $lastParamsArray['limit']['0']) {
                        $subAction = 'scroll';
                    } else {
                        //search 页面使用过滤,由于search mode后参数情况不一样，是添加在了keys里面，所以有两种情况
                        //1.keys长度不为0，但是string为0，where长度大于等于0，说明是仅使用了search mode过滤或者包含search mode的组合过滤。
                        //2.keys长度为0，where长度不为0，说明是使用了除search mode以外的过滤。
                        if (count($req->keys) > 0 && array_key_exists('string', $req->keys[0]) && !$req->keys[0]['string'] && count($req->where) >= 0 || ($lastParamsArray['sort'] != $req->sort && $req->sort['field'] != 'default')) {
                            $subAction = 'where';
                        } else if (count($req->keys) === 0 && count($req->where) > 0 || ($lastParamsArray['sort'] != $req->sort && $req->sort['field'] != 'default')) {
                            $subAction = 'where';
                        }
                    }
                    $user->setCache('adsearch.params', $jsonData);
                }
            } else if (in_array($act["action"], ['adser'])) {
                $lastParams = $user->getCache('adser.params');
                $lastParamsArray = json_decode($lastParams, true);
                if ($lastParams != $jsonData) {
                    //需要另外判断免费用户，每次过滤都会带有ads_id和time
                    if ($user->hasRole('Free')) {
                        //特定adser 页面初始化
                        if (count($req->keys) === 0 && count($req->where) === 2 && $req->limit['0'] === 0 && $req->sort['field'] == 'default') {
                            $subAction = 'init';
                        }
                        if ($req->limit['0'] != 0 && $req->limit['0'] != $lastParamsArray['limit']['0']) {
                            $subAction = 'scroll';
                        } else {
                            //特定adser 页面使用过滤,由于search mode后参数情况不一样，是添加在了keys里面，所以有两种情况
                            //1.keys长度不为0，但是string为0，where长度大于等于2，说明是仅使用了search mode过滤或者包含search mode的组合过滤。
                            //2.keys长度为0，where长度不为0，说明是使用了除search mode以外的过滤。
                            if (count($req->keys) > 0 && array_key_exists('string', $req->keys[0]) && !$req->keys[0]['string'] && count($req->where) >= 2) {
                                $subAction = 'where';
                            } else if (count($req->keys) === 0 && count($req->where) > 2) {
                                $subAction = 'where';
                            }
                        }
                    } else {
                        if (count($req->keys) === 0 && count($req->where) === 1 && $req->limit['0'] === 0 && $req->sort['field'] === 'default') {
                            $subAction = 'init';
                        }
                        if ($req->limit['0'] != 0 && $req->limit['0'] != $lastParamsArray['limit']['0']) {
                            $subAction = 'scroll';
                        } else {
                            //特定adser 页面使用过滤,由于search mode后参数情况不一样，是添加在了keys里面，所以有两种情况
                            //1.keys长度不为0，但是string为0，where长度大于等于1，说明是使用了search mode组合的过滤。
                            //2.keys长度为0，where长度不为0，说明是使用了除search mode以外的过滤。
                            if (count($req->keys) > 0 && array_key_exists('string', $req->keys[0]) && !$req->keys[0]['string'] && count($req->where) >= 1 || ($lastParamsArray['sort'] != $req->sort && $req->sort['field'] != 'default')) {
                                $subAction = 'where';
                            } else if (count($req->keys) === 0 && count($req->where) > 1 || ($lastParamsArray['sort'] != $req->sort && $req->sort['field'] != 'default')) {
                                $subAction = 'where';
                            }
                        }
                    } 
                    $user->setCache('adser.params', $jsonData);                   
                }
            } else if ($act["action"] == "statics") {
                try {
                    $this->updateUsage($req, "keyword_times_perday", $jsonData);
                } catch(\Exception $e) {
                    if ($e->getCode() == -1)
                        return response(["code"=>-1, "desc"=>"no permission"], 422);
                    else if ($e->getCode() == -2)
                        return response(["code"=>-1, "desc"=>"you reached the limit of statics today"], 422);
                }
            } else if ($act["action"] == "analysis") {
                try {
                    $this->updateUsage($req, "ad_analysis_times_perday", $jsonData);
                } catch(\Exception $e) {
                    if ($e->getCode() == -1)
                        return response(["code"=>-1, "desc"=>"no permission"], 422);
                    else if ($e->getCode() == -2)
                        return response(["code"=>-1, "desc"=>"you reached the limit of ad analysis today"], 422);
                }
            } else if ($act["action"] == "bookmark") {
                if (count($req->keys) === 0 && count($req->where) === 1 && $req->limit['0'] === 0) {
                    $subAction = 'init';
                }
                if ($req->limit['0'] != 0 && $req->limit['0'] > 0) {
                    $subAction = 'scroll';
                }
            }
            $remoteurl = config('services.bigbigads.ad_search_url');
        } else if ($action == "adserSearch") {
            //广告主分析
            try {
                $jsonData = json_encode($this->checkBeforeAdserSearch($user, $reqParams));
            } catch(\Exception $e) {
                return $this->responseError($e->getMessage(),$e->getCode());
            }
            try {
                $this->updateUsage($req, "adser_search_times_perday", $jsonData);
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
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HEADER, 1);//获取头信息
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData))
        );
        /* curl_setopt($ch, CURLOPT_TIMEOUT, 1); */ 
        $response = curl_exec($ch);
        try {
            if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == '200' || curl_getinfo($ch, CURLINFO_HTTP_CODE) == '201') {
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
                Log::info("<{$user->name}, {$user->email}> params:$jsonData, time cost:" . round($t2 - $t1, 3));
                if (isset($header))
                    Log::info($header);
            }

            $result = trim($result);
            $resultJson = json_decode($result, true);
            if (!$resultJson) {
                dispatch(new LogAbnormalAction($result, $jsonData, 'Server no response', $user->id, $req->ip()));
                return $this->responseError("server is busy, please refresh again", -4202);
            }
            if (array_key_exists('error', $resultJson)) {
                Log::warning('search fail:', ['result' => $resultJson]);
                return $this->responseError("Your search term is not legal", -4200);
            }
        } catch (Exception $e) {
            //记录下当搜索结果发生错误时用户的请求参数
            Log::warning("<{$user->name}, {$user->email}> something error in search result. params:$jsonData");
            dispatch(new LogAbnormalAction($e->getMessage(), $jsonData, 'something error happend in search', $user->id, $req->ip()));
        }
        
        if ($action == 'adsearch') {
            if (Auth::check()) {
                $act = $req->only('action');
                $resultPerSearchUsage = $user->getUsage('result_per_search');
                $searchTotalTimes = $user->getUsage('search_total_times');
                $hotSearchTimesPerday = $user->getUsage('hot_search_times_perday');
                $json = json_decode($result, true);
                if ($user->hasRole('Free') && array_key_exists("all_total_count", $json)) {
                    $searchResult = "total_count: " . $json['total_count'] . " ,all_total_count: " . $json['all_total_count'];
                }else {
                    $searchResult = "total_count: " . $json['total_count'];
                }
                if (in_array($act["action"], ['search'])) {
                    try {
                        $this->checkIsRestrictGetAdResource($req, $user, $jsonData);
                        $this->updateAdSearchResource($req, $user);
                    } catch(\Exception $e) {
                        return $this->responseError($e->getMessage(),$e->getCode());
                    }
                    switch ($subAction) {
                        case 'init': {
                            //页面初始化，应该重置result_per_search 已使用的次数
                            $user->updateUsage('result_per_search', 10, Carbon::now());
                            $searchInitPerday = $this->checkAndUpdateUsagePerday($user, 'search_init_perday');
                            dispatch(new LogAction(ActionLog::ACTION_SEARCH_INIT_PERDAY, $jsonData, "search_init_perday : " . $searchInitPerday.",cache_total_count: " . $json['total_count'], $user->id, $req->ip()));
                            break;
                        }               
                        case 'where': {
                            //搜索条件改变，应该重置result_per_search 已使用的次数
                            $user->updateUsage('result_per_search', 10, Carbon::now());
                            $searchWherePerday = $this->checkAndUpdateUsagePerday($user, 'search_where_perday');
                            dispatch(new LogAction(ActionLog::ACTION_SEARCH_WHERE_CHANGE_PERDAY, $jsonData, "search_where_change_perday: " . $searchWherePerday . "," . $searchResult, $user->id, $req->ip()));
                            break;
                        }
                        case 'scroll': {
                            //某个请求的获取到的最大结果总数不能超过权限设置的总数，否则抛出异常，防止postman获取超出权限的数据
                            if (intval($resultPerSearchUsage[2]) < intval($resultPerSearchUsage[1])) {
                                $user->updateUsage('result_per_search', $resultPerSearchUsage[2] + 10, Carbon::now());
                            } else {
                                Log::warning("{$req->ip()} : <{$user->name}, {$user->email}> Illegal request limit: {$req->limit['0']}");
                                return $this->responseError("beyond result limit", -4400);
                            }
                            if (count($req->keys) > 0 && $req->keys[0]['string']) {
                                $searchLimitkeysPerday = $this->checkAndUpdateUsagePerday($user, 'search_limit_keys_perday');
                                dispatch(new LogAction(ActionLog::ACTION_SEARCH_LIMIT_KEYS_PERDAY, $jsonData, "search_limit_keys_perday: " . $searchLimitkeysPerday, $user->id, $req->ip()));
                            } else {
                                $searchLimitWithoutKeysPerday = $this->checkAndUpdateUsagePerday($user, 'search_limit_without_keys_perday');
                                dispatch(new LogAction(ActionLog::ACTION_SEARCH_LIMIT_WITHOUT_KEYS_PERDAY, $jsonData, "search_limit_without_keys_perday: " . $searchLimitWithoutKeysPerday, $user->id, $req->ip()));
                            }
                            break;
                        }
                        case 'search': {
                            //搜索条件改变，应该重置result_per_search 已使用的次数
                            $user->updateUsage('result_per_search', 10, Carbon::now());
                            //log区分用户是否使用热词
                            //使用搜索参数进行区分会造成刷新标记丢失或者标记永久带上的问题
                            $isHotWord = $this->checkIsHotWord($req->keys);
                            if (count($req->keys) > 0 && $isHotWord) {
                                $hotSearchUsage = $hotSearchTimesPerday[2] + 1;
                                $user->updateUsage('hot_search_times_perday', $hotSearchUsage, Carbon::now());
                                dispatch(new LogAction(ActionLog::ACTION_HOT_SEARCH_TIMES_PERDAY, $jsonData, "hot_search_times_perday: " . $hotSearchUsage . "," .$searchResult , $user->id, $req->ip()));
                            } else {
                                //统计用户的总搜索次数,
                                //新增需求 -> 热词搜索不纳入用户搜索总数统计中
                                $user->updateUsage('search_total_times', $searchTotalTimes[2] + 1, Carbon::now());
                                dispatch(new LogAction(ActionLog::ACTION_SEARCH_TIMES_PERDAY, $jsonData, "search_times_perday: " . $searchTimesPerday . "," .$searchResult , $user->id, $req->ip()));
                            }
                            //dispatch(new LogAction("SEARCH_TIMES_PERDAY", $json_data, "search_times_perday: " . $searchTimesPerday . "," .$searchResult , $user->id, $req->ip()));
                            break;
                        }
                        default:
                            break;
                    }
                }
                if (in_array($act["action"], ['adser'])) {
                    try {
                        $this->checkIsRestrictGetAdResource($req, $user, $jsonData);
                        $this->updateSpecificAdserResource($user);  
                    } catch(\Exception $e) {
                        return $this->responseError($e->getMessage(),$e->getCode());
                    }
                    switch ($subAction) {
                        case 'init': {
                            $user->updateUsage('result_per_search', 10, Carbon::now());
                            $searchInitPerdayAdser = $this->checkAndUpdateUsagePerday($user, 'specific_adser_init_perday');
                            dispatch(new LogAction(ActionLog::ACTION_SEARCH_INIT_PERDAY_ADSER, $jsonData, "search_init_perday_adser : " . $searchInitPerdayAdser.",cache_total_count: " . $json['total_count'], $user->id, $req->ip()));
                            break;
                        }               
                        case 'where': {
                            //搜索条件改变，应该重置result_per_search 已使用的次数
                            $user->updateUsage('result_per_search', 10, Carbon::now());
                            $searchWherePerdayAdser = $this->checkAndUpdateUsagePerday($user, 'specific_adser_where_perday');
                            dispatch(new LogAction(ActionLog::ACTION_SEARCH_WHERE_CHANGE_PERDAY_ADSER, $jsonData, "search_where_change_perday_adser: " . $searchWherePerdayAdser . "," . $searchResult, $user->id, $req->ip()));
                            break;
                        }
                        case 'scroll': {
                            if (intval($resultPerSearchUsage[2]) < intval($resultPerSearchUsage[1])) {
                                $user->updateUsage('result_per_search', $resultPerSearchUsage[2] + 10, Carbon::now());
                            } else {
                                Log::warning("{$req->ip()} : <{$user->name}, {$user->email}> Illegal request limit: {$req->limit['0']}");
                                return $this->responseError("beyond result limit", -4400);
                            }
                            $searchLimitPerdayAdser = $this->checkAndUpdateUsagePerday($user, 'specific_adser_limit_perday');
                            dispatch(new LogAction(ActionLog::ACTION_SEARCH_LIMIT_PERDAY_ADSER, $jsonData, "search_limit_perday_adser: " . $searchLimitPerdayAdser, $user->id, $req->ip()));
                            break;
                        }
                        default:
                            break;
                    }
                }
                if (in_array($act["action"], ['bookmark'])) {
                    switch ($subAction) {
                        case 'init': {
                            $searchInitPerdayBookmark = $this->checkAndUpdateUsagePerday($user, 'bookmark_init_perday');
                            dispatch(new LogAction(ActionLog::ACTION_SEARCH_INIT_PERDAY_BOOKMARK, $jsonData, "search_init_perday_bookmark : " . $searchInitPerdayBookmark, $user->id, $req->ip()));
                            break;
                        }               
                        case 'scroll': {
                            $searchLimitPerdayBookmark = $this->checkAndUpdateUsagePerday($user, 'bookmark_limit_perday');
                            dispatch(new LogAction(ActionLog::ACTION_SEARCH_LIMIT_PERDAY_BOOKMARK, $jsonData, "search_limit_perday_bookmark: " . $searchLimitPerdayBookmark, $user->id, $req->ip()));
                            break;
                        }
                        default:
                            break;
                    }
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
