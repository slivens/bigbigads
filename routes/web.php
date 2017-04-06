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


Route::get('/', function (Request $request) {
    $recents = Post::orderBy('created_at', 'desc')->take(5)->get();
    return view('index')->with('recents', $recents);
});

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
Route::get('/registerVerify', 'UserController@registerVerify');
Route::get('/noverify', 'UserController@noverify');
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

Route::any('/forward/{action}', 'SearchController@search');

Route::any('/onPayWebhooks', 'SubscriptionController@onPayWebhooks');

