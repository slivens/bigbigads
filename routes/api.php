<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['auth']], function () {
    // 获取登录用户信息
    Route::get('users/current', 'Api\UserController@getCurrentUser');

    // 获取广告主列表
    Route::get('owners', 'Api\OwnerController@getOwners');

    // 获取广告主信息
    Route::get('owners/{facebook_id}', 'Api\OwnerController@getOwner');

    // 获取广告详情
    Route::get('ads/{event_id}', 'Api\AdController@getAd');
});