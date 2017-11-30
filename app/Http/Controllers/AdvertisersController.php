<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Jobs\LogAction;
use App\ActionLog;
use Log;
use App\Jobs\LogAbnormalAction;
use App\Api\Adser as ApiAdser;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;

class AdvertisersController extends Controller
{
    /**
     *  获取一组广告主
     */
    private function apiPublishers($request, $params)
    {
        $where = [];
        $keys  = [];
        $page  = $params['page'];
        $limit = $params['limit'];

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

        /**
         * search_result判断
         */ 
        $searchResult = $this->getSearchResultParams($params);

        $json = [
            'search_result' => $searchResult,
            'limit'         => [($page - 1) * $limit, $limit],
            'where'         => $where,
            'keys'          => $keys,
            'sort'          => [
                'field' => 'ads_number',
                'order' => 1
            ],
            'select'        => 'adser_username,large_photo,ads_number,adser_name,page_verified',
        ];
        $result = $client->request('POST', env('MOBILE_ADSER_SEARCH'), [
            'json' => $json
        ]);

        $result = json_decode($result->getBody(), true);

        $subAction = $this->getUserAction($params);

        if (!array_key_exists('adser_info', $result)) return [
            'data'  => [],
            'page'  => 0,
            'pages' => 0,
            'limit' => 0,
            'next'  => 0,
            'prev'  => 0,
        ];
        $this->logActionAndUpgradeUsage($subAction, $result['total_adser_count'], $json, $request);

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

    /**
     *  搜索单个广告主
     */
    private function apiPublisher($id)
    {
        $json = [
            'search_result' => 'mobile_adser',
            'sort'          => [
                'field' => 'ads_number',
                'order' => 1
            ],
            'limit'         => [0, 1],
            'adser_detail'  => 1,
            'where'         => [[
                'field'     =>  'adser_username',
                'value'     =>  $id,
            ]],
        ];

        $client = new Client();
        
        $result = $client->request('POST', env('MOBILE_ADSER_SEARCH'), [
            'json' => [
                'search_result' => 'mobile_adser',
                'sort'          => [
                    'field' => 'ads_number',
                    'order' => 1
                ],
                'limit'         => [0, 1],
                'adser_detail'  => 1,
                'where'         => [[
                    'field'     =>  'adser_username',
                    'value'     =>  $id,
                ]],
            ]
        ]);

        $result = json_decode($result->getBody(), true);

        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     *  得到排序后的广告主名下Top广告
     */
    static public function getTopAds($id, $descMode)
    {
        $select = '';
        switch($descMode) {
            case 'like_rate':
                $select = 'event_id,likes_per_30d,likes,last_see,like_rate,local_picture';
                break;
            case 'comment_rate':
                $select = 'event_id,comment_per_30d,comment,last_see,comment_rate,local_picture';
                break;
            case 'share_rate':
                $select = 'event_id,share_per_30d,share,last_see,share_rate,local_picture';
                break;
            case 'total_impression':
                $select = 'event_id,likes_per_30d,likes,last_see,like_rate,local_picture';
                break;
            default: break;
        }
        $client = new Client();
        
        $json = [
            'search_result' => 'ads',
            'sort'          => [
                'field' => $descMode,
                'order' => 1
            ],
            'limit'         => [0, 10],
            'adser_detail'  => 1,
            'where'         => [[
                'field'     =>  'adser_username',
                'value'     =>  $id,
            ]],
            'select'        => $select,
        ];

        $result = $client->request('POST', env('AD_SEARCH_URL'), [
            'json' => [
                'search_result' => 'ads',
                'sort'          => [
                    'field' => $descMode,
                    'order' => 1
                ],
                'limit'         => [0, 10],
                'adser_detail'  => 1,
                'where'         => [[
                    'field'     =>  'adser_username',
                    'value'     =>  $id,
                ]],
                'select'        => $select,
            ]
        ]);
        $result = json_decode($result->getBody(), true);

        return $result;
    }

    /**
     * 检查并更新权限
     * Todo 与 SearchController 有重复代码应该抽出
     */
    protected function checkAndUpdateUsagePerday($user, $logAction)
    {
        $logActionUsage = $user->getUsage($logAction);
        if (!$logActionUsage) {
            return $this->responseError("no search permission");
        }

        if (count($logActionUsage) < 4) {
            $carbon = Carbon::now();
        } else {
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

    protected function throttleByIdent($ident, $user)
    {
        //速率的计算
        $key = 'attack_' . $ident;
        $def = ['last' => Carbon::now()->toDateTimeString() , 'count' => 0];
        $cache = Cache::get($key, $def);
        $last = new Carbon($cache['last']);
        $now = Carbon::now();

        if ($now->hour == $last->hour && $now->diffInHours($last, true) == 0) {
            $cache['count']++;
        } else {
            $cache['count'] = 1;
        }

        $cache['last'] = $now->toDateTimeString();
        Cache::put($key, $cache, 1440);

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
     * 检测访问速率
     * Todo 与 SearchController 有重复代码应该抽出
     */
    protected function checkAttack($req, $user)
    {
        if (!$this->throttleByIdent($req->ip(), $user))
            return false;
        if ($this->isAnonymous())
            return true;
        return $this->throttleByIdent($user->email, $user);
    }

    /**
     * 广告搜索资源轮询, 当某一个资源消耗尽, 其余资源全部不可使用
     * Todo 与 SearchController 有重复代码应该抽出
     */
    protected function checkIsRestrictGetAdResource($request, $user, $jsonData)
    {
        $searchPolicyArray = [
            'specific_adser_times_perday'       => ActionLog::ACTION_SEARCH_RESTRICT_PERDAY_ADSER,
            'search_key_total_perday'           => ActionLog::ACTION_SEARCH_KEY_RESTRICT,
            'search_without_key_total_perday'   => ActionLog::ACTION_SEARCH_WITHOUT_KEY_RESTRICT,
            'search_times_perday'               => ActionLog::ACTION_SEARCH_TIMES_PERDAY_RESTRICT,
            'ad_analysis_times_perday'          => ActionLog::ACTION_AD_ANALYSIS_TIMES_PERDAY_RESTRICT,
            'adser_without_key_total_perday'    => ActionLog::ACTION_ADSER_SEARCH_WITHOUT_KEY_RESTRICT,
            'adser_key_total_perday'            => ActionLog::ACTION_ADSER_SEARCH_KEY_RESTRICT,
            'adser_search_times_perday'         => ActionLog::ACTION_ADSER_SEARCH_TIMES_PERDAY_RESTRICT,
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
                dispatch(new LogAction($value, $jsonData, $key . ': RESTRICT', $user->id, $request->ip()));
                throw new \Exception("you reached search times today, default result will show", -4100);
            }
        }
    }

    /**
     * 解析广告主搜索时用户提交的参数得到用户的页面操作行为
     */
    protected function getUserAction($params)
    {
        $user = Auth::user();
        $lastParams = $user->getCache('adserSearch.params');
        $lastParamsArr = json_decode($lastParams, true);
        $subAction = '';

        // 与上次缓存的参数一致，说明是刷新页面操作
        if ($lastParamsArr == $params) {
            return $subAction;
        }
        // 空词且page 为 1 说明是初始化页面，多次刷新仅记录第一次  
        if (!$params['keywords'] && $params['page'] == 1) {
            $subAction = 'init';
        }
        // 与上次缓存的参数比较仅page不同，说明是下拉操作，此处区分空词与非空词下拉
        if ($params['keywords'] && $params['keywords'] == $lastParamsArr['keywords'] && $params['page'] != $lastParamsArr['page'] && $params['page'] != 1) {
            $subAction = 'keyScroll';
        } else if (!$params['keywords'] && $params['keywords'] == $lastParamsArr['keywords'] && $params['page'] != $lastParamsArr['page'] && $params['page'] != 1) {
            $subAction = 'scroll';
        }
        // 与上次缓存的参数比较仅keywords不同，且keywords存在，则用户操作为搜索
        if ($params['keywords'] && $params['keywords'] != $lastParamsArr['keywords'] && $params['page'] == 1) {
            $subAction = 'search';
        }

        return $subAction;
    }

    /**
     * 用户行为log记录并且更新资源使用情况
     */
    protected function logActionAndUpgradeUsage($subAction, $resultTotal, $params, $request)
    {
        $user = Auth::user();
        $searchResult = $resultTotal;
        $jsonData = json_encode($params);
        $resultPerSearchUsage = $user->getUsage('adser_result_per_search');
        $adserSearchTimesPerday = $user->getUsage('adser_search_times_perday');
        switch ($subAction) {
            case 'init': {
                //页面初始化，应该重置adser_result_per_search 已使用的次数
                $user->updateUsage('adser_result_per_search', 10, Carbon::now());
                $adserInitPerday = $this->checkAndUpdateUsagePerday($user, 'adser_init_perday');
                dispatch(new LogAction(ActionLog::ACTION_ADSER_INIT_PERDAY, $jsonData, "adser_init_perday : " . $adserInitPerday.",cache_total_count: " . $searchResult, $user->id, $request->ip()));
                break;
            }
            case 'keyScroll': {
                if (intval($resultPerSearchUsage[2]) < intval($resultPerSearchUsage[1])) {
                    $user->updateUsage('adser_result_per_search', $resultPerSearchUsage[2] + 10, Carbon::now());
                } else {
                    Log::warning("{$req->ip()} : <{$user->name}, {$user->email}> Illegal request limit: {$params['limit']}");
                    throw new \Exception("beyond result limit", -4400);
                }
                $adserLimitKeysPerday = $this->checkAndUpdateUsagePerday($user, 'adser_limit_keys_perday');
                dispatch(new LogAction(ActionLog::ACTION_ADSER_LIMIT_KEYS_PERDAY, $jsonData, "adser_limit_keys_perday: " . $adserLimitKeysPerday, $user->id, $request->ip()));
                break;
            }
            case 'scroll': {
                //某个请求的获取到的最大结果总数不能超过权限设置的总数，否则抛出异常，防止postman获取超出权限的数据
                if (intval($resultPerSearchUsage[2]) < intval($resultPerSearchUsage[1])) {
                    $user->updateUsage('adser_result_per_search', $resultPerSearchUsage[2] + 10, Carbon::now());
                } else {
                    Log::warning("{$request->ip()} : <{$user->name}, {$user->email}> Illegal request limit: {$jsonData}");
                    throw new \Exception("beyond result limit", -4400);
                }
                $adserLimitWithoutKeysPerday = $this->checkAndUpdateUsagePerday($user, 'adser_limit_without_keys_perday');
                dispatch(new LogAction(ActionLog::ACTION_ADSER_LIMIT_WITHOUT_KEYS_PERDAY, $jsonData, "adser_limit_without_keys_perday: " . $adserLimitWithoutKeysPerday, $user->id, $request->ip()));
                break;
            }
            case 'search': {
                //搜索条件改变，应该重置adser_result_per_search 已使用的次数
                if (intval($adserSearchTimesPerday[2]) < intval($adserSearchTimesPerday[1])) {
                    $adserSearchTimesPerday = $this->checkAndUpdateUsagePerday($user, 'adser_search_times_perday');
                    dispatch(new LogAction(ActionLog::ACTION_ADSER_SEARCH_TIMES_PERDAY, $jsonData, "adser_search_times_perday: " . $adserSearchTimesPerday . ", adser_total_count: " .$searchResult , $user->id, $request->ip()));
                } else {
                    throw new \Exception("you reached search times today, default result will show", -4100);
                }
                $user->updateUsage('adser_result_per_search', 10, Carbon::now());
                break;
            }
        }
    }

    /**
     * 判断用户提交参数是否合法
     */
    protected function checkParamsIsLegal($req, $params)
    {
        $user = $this->user;
        $isLegal = true;
        $resultPerSearch = $user->getUsage('adser_result_per_search');
        $limit = ($params['page'] - 1) * $params['limit'];

        if ($params['limit'] % 10 != 0 || $limit >= $resultPerSearch[1]) {
            dispatch(new LogAbnormalAction('', $req, 'Illegal limit params', $user->id, $req->ip()));
            $isLegal = false;
        }

        return $isLegal; 
    }

    /**
     * 判断search result的参数
     * 用户无过滤条件和空词请求,则访问cache文件
     */
    private function getSearchResultParams($params)
    {
        $searchResult = 'cache_adser';
        if ($params['keywords']) {
            $searchResult = 'mobile_adser';
        }
        return $searchResult;
    }

    public function getPublishers(Request $request)
    {
        /**
         * 用户登录检查
         * 访问速率检查
         */
        $user = Auth::user();
        if (!$user) return $this->responseError("You should sign in", -4199);
        if (!$this->checkAttack($request, $user)) {
            return $this->responseError("We detect your ip has abandom behavior", -5000);
        }

        $keywords = $request->input('keywords');
        $page = $request->input('page') ? $request->input('page') : 1;
        $limit = $request->input('limit') ? $request->input('limit') : 10;
        $params = [];
        $params['keywords'] = $keywords;
        $params['page']     = $page;
        $params['limit']    = $limit;

        try {
            /**
             * 更新请求广告主资源次数
             * 只区分空词和非空词情况
             */
            if ($params['keywords']) {
                $this->checkAndUpdateUsagePerday($user, 'adser_key_total_perday');
            } else {
                $this->checkAndUpdateUsagePerday($user, 'adser_without_key_total_perday');
            }

            /**
             * 轮询全部广告资源使用情况
             */
            $this->checkIsRestrictGetAdResource($request, $user, json_encode($params));

            /**
             * 获取广告主结果
             */
            $result = $this->apiPublishers($request, $params);

            /**
             * 缓存用户提交的搜索参数
             */
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(),$e->getCode());
        }
        $user->setCache('adserSearch.params', json_encode($params));
        
        return response()->json($result, 200);
    }

    public function getPublisherAnalysis(Request $request, $facebookId)
    {
        $user = Auth::user();
        if (!$user) return $this->responseError("You should sign in", -4199);
        if (!$this->checkAttack($request, $user)) {
            return $this->responseError("We detect your ip has abandom behavior", -5000);
        }
        //$this->checkIsRestrictGetAdResource($request, $user, $jsonData);

        $result = $this->apiPublisher($facebookId);
        
        // $this->checkAndUpdateUsagePerday($user, 'search_key_total_perday');
        return response()->json($result, 200);
    }
}
