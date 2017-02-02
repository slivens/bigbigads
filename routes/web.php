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
use Braintree\Plan;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index');


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

Route::get('/userinfo', function() {
    //返回登陆用户的session信息，包含
    //用户基本信息、权限信息
    $res = [];
    $user = Auth::user();
    if ($user) {
        $user->load('role', 'role.permissions', 'role.policies');
        $res['login'] = true;
        $res['user'] = $user;
        if ($user->hasBraintreeId()) {
            $res['subscription'] = $user->subscriptions->first();
        }
    } else {
        $res['login'] = false;
    }
    return json_encode($res, JSON_UNESCAPED_UNICODE);
});

Route::get('/plans', function() {
    $items = App\Role::with('permissions', 'policies')->where('id', '>', 2)->get();
    foreach ($items as $key=>$item) {
        $item->groupPermissions = $item->permissions->groupBy('table_name');
        $item->plan;
    }
    return $items;
});
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
Route::get('logout', 'Auth\LoginController@logout');

Route::resource('bookmark', 'BookmarkController');
Route::resource('BookmarkItem', 'BookmarkItemController');


Route::get('/tester', function() {
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
