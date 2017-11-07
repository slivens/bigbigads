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
use TCG\Voyager\Models\Post;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegisterVerify;
use TCG\Voyager\Models\Setting;
use Illuminate\Support\Facades\Auth;

// 前台主页
Route::get('/', 'HomeController@index')->middleware('track');
Route::get('/home', 'HomeController@index')->middleware('track');

// 用于前台主页动态获取广告总数
Route::get('/get_total_count', 'HomeController@getTotalCount');

Route::get('/message', 'Controller@messageView');

Auth::routes();
Route::get('/forget', function() {
    return view('auth.login');
});

Route::get('/socialite/{name}', 'UserController@socialiteRedirect');
Route::get('/socialite/{name}/callback', 'UserController@socialiteCallback')->middleware('auth.freeze:true');
Route::get('/socialite/{name}/bind', 'UserController@bindForm');
Route::post('/socialite/{name}/bind', 'UserController@bind')->name('socialiteBindPost');

Route::get('/blog', function () {

    /* dd( Route::current()); */
    //TODO:现在只做一次性加载，排版最终定版以及前端改用vue,webpack整理后再做完整修改
    $posts = Post::orderBy('created_at', 'desc')->paginate(100);
    return view('blog_list')->with("posts", $posts);
});
Route::get('/post/{id}', function($id) {

    /* dd( Route::current()); */
    $post = Post::find($id);
    $recents = Post::orderBy('created_at', 'desc')->take(5)->get();
    return view('post')->with('post', $post)->with('recents', $recents);
});
Route::get('/product', function () {
    return view('product');
});

Route::get('/plan', function () {
    //暂定登录后再次点击sign up后跳转至app/plans
    if (Auth::check()) {
        return redirect('/app/plans');
    } else {
        return view('plan');
    }
});

Route::get('/about', function () {
    return view('about');
});
Route::get('/terms_service',function(){
    return view('auth/terms_service');
});
Route::get('/privacy_policy',function(){
    return view('auth/privacy_policy');
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    Route::get('/refunds/{id}/accept', 'Admin\RefundController@acceptRefund')->name('refund_accept');
    Route::get('/refunds/{id}/reject', 'Admin\RefundController@rejectRefund')->name('refund_reject');
});

Route::get('/ranking', function(Request $req) {
    $maxCount = 100;//根据权限去判断
    $user = Auth::user();
    if($user->can('ranking')) {
        if (isset($req->category)) {
            $items = App\CategoryTopAdvertiser::where('page_category', $req->category)->take($maxCount)->get();
        } else {
            $items = App\TopAdvertiser::take($maxCount)->get();
        }
        return json_encode($items, JSON_UNESCAPED_UNICODE);
    }else {
        return response(["code"=>-4201, "desc"=>"no permission of ranking"], 422);
    }
});
// 由于移动端访问过于频繁，暂时更改路由名称并
Route::get('/userinfo', 'UserController@logInfo');
Route::get('/registerVerify', 'UserController@registerVerify');
Route::get('/sendVerifyMail', 'UserController@sendVerifyMail');

Route::get('/plans', 'SubscriptionController@plans');

Route::group(['middleware'=>'auth'], function() {
    //Route::get('/pay', 'SubscriptionController@form');
    Route::post('/pay', 'SubscriptionController@pay');
    Route::get('/billings', 'SubscriptionController@billings');
    Route::post('/subscription/{id}/cancel', 'SubscriptionController@cancel');
	Route::get('/invoice/{invoice}', function (Request $request, $invoiceId) {
		return Auth::user()->downloadInvoice($invoiceId, [
			'vendor'  => 'Bigbigads',
			'product' => 'Bigbigads',
		], storage_path('invoice'));
    });
    Route::post('changepwd', 'UserController@changepwd');
    Route::put('/payments/{number}/refund_request', 'SubscriptionController@requestRefund');
    // Route::get('/invoices/{invoice_id}/status', 'InvoiceController@getGenerateStatus');
    Route::get('/invoices/{invoice_id}', 'InvoiceController@downloadInvoice');
    Route::post('users/changeProfile/{type}/{param}', 'UserController@changeProfile');
});

//pay页面需要支持不登录可访问
Route::get('/pay', 'SubscriptionController@form');

Route::get('/onPay', 'SubscriptionController@onPay');

Route::get('logout', 'Auth\LoginController@logout');

Route::resource('bookmark', 'BookmarkController');
Route::resource('BookmarkItem', 'BookmarkItemController');
Route::resource('/rest/coupon', 'CouponController');//后面将改成统一由ReourceController+Hooks的方式控制
Route::any('/forward/{action}', 'SearchController@search')->middleware('auth.freeze');

Route::post('/onPayWebhooks', 'SubscriptionController@onPayWebhooks');



//邮件营销 
Route::get('/edm', 'EDMController@index');
Route::post('/edm/send', 'EDMController@send');
Route::get('/edm/unsubscribe', 'EDMController@unsubscribe');

Route::post('/mailgun/webhook', 'MailgunWebhookController@onWebhook');
Route::get('/image', function(Request $request) {
    $src = asset($request->src);
    $width = max(8, intval($request->width));
    try {
        $img = Image::make(file_get_contents($src));
    } catch (\Exception $e) {
        return "image not found:$src";
    }
    $img->widen($width);
    $response = Response::make($img->encode('jpg', 75));
    $response->header('Content-Type', 'image/jpg');
    return $response;
});

/*移动端登录提示页面*/
Route::get('/mobile_maintain', function () {
    return view('mobile_maintain');
});

Route::get('/config', function() {
    return Setting::select('key', 'value')->get()->groupBy('key');
});

/*
    统计app的track
*/
Route::post('/trackState', function (Request $request) {
})->middleware('track');

// Wordpress
Route::get('/wordpress/get_track_id', 'WordpressController@getTrackId');
Route::get('/wordpress/track_notice', 'WordpressController@trackNotice')->middleware('track');

Route::get('/welcome', function () {
    return view('welcome');
});
Route::get('/extension', function () {
    return view('extension');
})->middleware('track');
Route::get('/methodology', function () {
    return view('methodology');
});

Route::post('/subscriptions/{sid}/sync', 'SubscriptionController@sync');
Route::get('hotword', 'HotWordController@getHotWord');
Route::get('audience-interest', 'AudienceInterestController@getAudienceInterest');

Route::post('/quick_register', 'UserController@quickRegister');//快速注册表单提交位置
Route::get('/payment/{method}/prepare', 'SubscriptionController@prepareCheckout');
Route::any('/payment/paypal/done', 'SubscriptionController@onPaypalDone')->name('paypal_done');
Route::any('/payment/stripe/done', 'SubscriptionController@onStripeDone')->name('stripe_done');

/*
Route::get('/faker', function(Request $request) {
    if ($request->key != 'liuwencan')
        return "wrong key";
    $user = App\User::where('email', $request->email)->first();
    if (!$user)
        return "no such user";
    Auth::login($user);
    return "done";
});*/
/* Route::any('/payment/stripe', function() { */
/*     return view('subscriptions.stripe')->with('key', env('STRIPE_PUBLISHABLE_KEY')); */
/* }); */
Route::get('/record-continue', 'UserController@recordContinue');
Route::post('/filter-record', 'UserController@filterLogRecord');

// 以后会在新增新的反馈收集，就统一处理反馈的控制器及其具体的反馈收集项
Route::post('/feedback/plan', 'FeedbackController@plan')->middleware('throttle:30,60');
