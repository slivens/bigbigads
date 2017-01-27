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
    if (Auth::user()) {
        $res['login'] = true;
        $res['user'] = Auth::user();
    } else {
        $res['login'] = false;
    }
    return json_encode($res, JSON_UNESCAPED_UNICODE);
});

Route::get('logout', 'Auth\LoginController@logout');

Route::resource('bookmark', 'BookmarkController');
Route::resource('BookmarkItem', 'BookmarkItemController');
