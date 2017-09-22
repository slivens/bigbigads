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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


Route::group(['middleware' => ['auth:api']], function () { 
    Route::get('publishers', 'Api\PublisherController@search')->middleware('cors');

    // 获取单个的发布者信息
    Route::get('publisher/{facebook_id}', 'Api\PublisherController@getPublisher')->middleware('cors');

    // 获取发布者的分析数据
    Route::get('adserAnalysis/{facebook_id}', 'Api\AdserAnalysisController@show')->middleware('cors');

    // 获取特定发布者的Top前20广告, 以top_pression share_rate like_rate comment_rate
    Route::get('topAds/{facebook_id}/{rate_type}', 'Api\AdserAnalysisController@getTopAds')->middleware('cors');

    // 获取广告详情
    Route::get('ads/{event_id}', 'Api\AdController@getAd')->middleware('cors');
});