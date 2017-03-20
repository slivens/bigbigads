<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
 */
use Illuminate\Http\Request;
use TCG\Voyager\Models\Permission;
use App\Services\AnonymousUser;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\ActionLog;
use App\Role;
use App\Plan;
/**
 * @$req Reqeust 
 * @$name 权限名称
 * @$params 搜索参数
 */
function updateUsage($req, $name, &$params)
{
    if (Auth::check()) {
        $user = Auth::user();
    } else {
        $user = AnonymousUser::user($req);
    }
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

Route::get('/', function () {
    return view('index');
});

Route::get('/product', function () {
    return view('product');
});

Route::get('/pricing', function () {
    return view('pricing');
});

Route::get('/about', function () {
    return view('about');
});

Auth::routes();

Route::get('/home', function() {
    return redirect('/app');
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

Route::get('/ranking', function(Request $req) {
    $maxCount = 100;//根据权限去判断

    if (isset($req->category)) {
        $items = App\CategoryTopAdvertiser::where('page_category', $req->category)->take($maxCount)->get();
    } else {
        $items = App\TopAdvertiser::take($maxCount)->get();
    }
    return json_encode($items, JSON_UNESCAPED_UNICODE);
});

Route::get('/userinfo', 'UserController@logInfo');

Route::get('/plans', 'SubscriptionController@plans');

Route::group(['middleware'=>'auth'], function() {
    Route::get('/pay', 'SubscriptionController@form');
    Route::post('/pay', 'SubscriptionController@pay');
    Route::get('/billings', 'SubscriptionController@billings');
	Route::get('/invoice/{invoice}', function (Request $request, $invoiceId) {
		return Auth::user()->downloadInvoice($invoiceId, [
			'vendor'  => 'Bigbigads',
			'product' => 'Bigbigads',
		], storage_path('invoice'));
    });

    Route::post('changepwd', 'UserController@changepwd');

});

Route::get('/onPay', 'SubscriptionController@onPay');

Route::get('logout', 'Auth\LoginController@logout');

Route::resource('bookmark', 'BookmarkController');
Route::resource('BookmarkItem', 'BookmarkItemController');

Route::any('/forward/{action}', function(Request $req, $action) {
    $json_data = json_encode($req->except(['action']));
    $remoteurl = "";
    if ($action == 'adsearch') {
        //检查权限（应该是根据GET的动作参数判断，否则客户端会出现一种情况，当查看收藏时，也会触发搜索资源统计)
        $act = $req->only('action');
        if (in_array($act["action"], ['search'])) {
            if (Auth::check()) {
                $user = Auth::user();
            } else {
                $user = AnonymousUser::user($req);
            }
            $lastParams = $user->getCache('adsearch.params');
            //参数有变化，开始做搜索次数的判定
            if ($lastParams != $json_data) {
                $usage = $user->getUsage('search_times_perday');
                if (!$usage) {
                    return response(["code"=>-1, "desc"=> "no search permission"], 422);
                }
                //有搜索或者过滤条件
                if (count($req->keys) > 0 || count($req->where) > 0) {
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
                        return response(["code"=>-1, "desc"=> "you reached search times today, default result will show"], 422);
                    Log::debug("adsearch " . $json_data . json_encode($usage));
                    $user->updateUsage('search_times_perday', $usage[2] + 1, Carbon::now());
                    ActionLog::log("search_times_perday", $json_data, "search_times_perday:"  . ($usage[2] + 1));
                }
                $user->setCache('adsearch.params', $json_data);
            }
        } else if ($act["action"] == "statics") {
            try {
                updateUsage($req, "keyword_times_perday", $json_data);
            } catch(\Exception $e) {
                if ($e->getCode() == -1)
                    return response(["code"=>-1, "desc"=>"no permission"], 422);
                else if ($e->getCode() == -2)
                    return response(["code"=>-1, "desc"=>"you reached the limit of statics today"], 422);
            }
        } else if ($act["action"] == "analysis") {
            try {
                updateUsage($req, "ad_analysis_times_perday", $json_data);
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
            updateUsage($req, "adser_search_times_perday", $json_data);
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
        $user = Auth::user();
        for($i=0;$i<1;$i++) {
            //检查是否有该用户收藏
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
                $result = json_encode($json);
            }
        }
    }
    return $result;
});

//测试，正式发布后删除
Route::get('/tester', function() {
    $role = App\Role::where('name', 'Pro')->first();
    $user = Auth::user();
    $user->initUsageByRole($role);
    $user->save();
    /* Auth::user()->incUsage("image_download"); */
    //Auth::user()->save();
    dd(Auth::user()->usage);
    /* $role = App\Role::where('name', 'Standard')->first(); */
    /* dd($role->groupedPolicies()); */
    $fields = ["id", "billingPeriodStartDate", "billingPeriodEndDate", "currentBillingCycle", "planId", "price", "status"];
    $user = Auth::user();
    $res = [];
    foreach($user->subscriptions as $item) {
        $subscription = \Braintree\Subscription::find($item->braintree_id);
        $resitem = [];
        foreach($fields as $item2) {
            $resitem[$item2] = $subscription->$item2;
        }
        array_push($res,  $subscription);
    }
   // $invoices = $user->invoices();
    //dd($invoices);
    dd($res);
});

/* Route::get('/showPlans', 'SubscriptionController@showPlans'); */
Route::any('/onPayWebhook', 'SubscriptionController@onPayWebhook');
/* Route::any('/createPayment', 'PaymentController@createPayment'); */


Route::get('/t2', function() {

    /* $service = new \App\Services\PaypalService(); */
    /* $name = "I-23KWN02LTR7P"; */
    /* /1* $service->suspendSubscription($name); *1/ */
    /* $subscription = $service->subscription($name); */
    /* return $subscription; */
    /* $detail = $subscription->getAgreementDetails(); */
    /* echo "payment:" . $detail->getLastPaymentDate(); */
    /* echo " next:" . $detail->getNextBillingDate() . "<br/>"; */
    /* $t = $service->transactions($name); */
    /* foreach ($t as $item) { */
    /*     echo "{$item->getTransactionId()} , {$item->getTransactionType()} "; */
    /*     $amount = $item->getAmount(); */
    /*     if ($amount) { */
    /*         echo "value: {$amount->getValue()}"; */
    /*     } */
    /*     $carbon = new Carbon($item->getTimeStamp(), $item->getTimeZone()); */
    /*     $carbon->tz = Carbon::now()->tz; */
    /*     echo " happend: " . $carbon ; */
    /*     echo " timezone:" . $item->getTimeZone(); */
    /*     echo " status: " . $item->getStatus(); */
    /*     echo "<br/>"; */
    /* } */
});
Route::get('/paytest', function() {
    /* $plan = Plan::where('name', 'standard')->first(); */
    /* $service = new \App\Services\PaypalService; */
    /* return $service->createPayment($plan); */
});
