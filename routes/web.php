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

Route::get('/', function (Request $request) {
    /*$url = '/app/';
    if (track($request)) {
        $url .= "?track={$request->track}";
    }
    return redirect($url);*/
    //需求改动，/跳转至index页面
    $recents = Post::orderBy('created_at', 'desc')->take(5)->get();
    return view('index')->with('recents', $recents);
})->middleware('track');

Route::get('/message', 'Controller@messageView');

Auth::routes();
Route::get('/forget', function() {
    return view('auth.login');
});

Route::get('/socialite/{name}', 'UserController@socialiteRedirect');
Route::get('/socialite/{name}/callback', 'UserController@socialiteCallback');
Route::get('/socialite/{name}/bind', 'UserController@bindForm');
Route::post('/socialite/{name}/bind', 'UserController@bind')->name('socialiteBindPost');

/**
 * 前台主页
 */
Route::get('/home', function (Request $request) {
    $recents = Post::orderBy('created_at', 'desc')->take(5)->get();
    return view('index')->with('recents', $recents);
})->middleware('track');

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

Route::get('/pricing', function () {
    return view('pricing');
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

Route::get('/userinfo', 'UserController@logInfo');
Route::get('/registerVerify', 'UserController@registerVerify');
Route::get('/sendVerifyMail', 'UserController@sendVerifyMail');

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
Route::resource('/rest/coupon', 'CouponController');//后面将改成统一由ReourceController+Hooks的方式控制
Route::any('/forward/{action}', 'SearchController@search');

Route::any('/onPayWebhooks', 'SubscriptionController@onPayWebhooks');

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

Route::get('/mobile', function (Request $request) {
    //手机端的跳转也支持track统计
    return view('mobile');
})->middleware('track');

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

Route::get('hotWord', 'HotWordController@getHotWord');