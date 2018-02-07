<?php


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

Route::get('/userinfo', 'UserController@logInfo');

Auth::routes();

Route::any('/forward/{action}', 'SearchController@search')->middleware('auth.freeze');

Route::get('/plans', 'SubscriptionController@plans');

Route::resource('/coupons', 'CouponController');//后面将改成统一由ReourceController+Hooks的方式控制

Route::group(['middleware'=>'auth'], function() {
    //Route::get('/pay', 'SubscriptionController@form');
    Route::post('/pay', 'SubscriptionController@pay');
    Route::get('/me/billings', 'SubscriptionController@billings');
    Route::post('/me/subscriptions/{id}/cancel', 'SubscriptionController@cancel');
	Route::get('/me/invoice/{invoice}', function (Request $request, $invoiceId) {
		return Auth::user()->downloadInvoice($invoiceId, [
			'vendor'  => 'xggg',
			'product' => 'Adminer',
		], storage_path('invoice'));
    });
    Route::get('/me/customize_invoice', 'UserController@getInvoiceCustomer');
    Route::post('/me/customize_invoice', 'UserController@setInvoiceCustomer');

    Route::get('/me/bookmark/default', 'BookmarkController@getDefault');
    Route::resource('/me/bookmarks', 'BookmarkController');
    Route::resource('/me/bookmark_items', 'BookmarkItemController');
    Route::post('/changepwd', 'UserController@changepwd');
    Route::put('/me/payments/{number}/refund_request', 'SubscriptionController@requestRefund');
    Route::patch('/me/profile', 'UserController@changeProfile');
    Route::post('/me/send_email', 'UserController@sendVerifyMailToSubEmail');
    Route::get('/me/affialites/{track}/payments', 'UserController@getUserListByAffiliateTrack');
    Route::post('/me/custom_option', 'UserController@updateCustomOption')->middleware('throttle:30,60');

    // TODO:下面2个应该移到secure_api
    Route::post('/service_term', 'UserController@updateServiceTerm');
    Route::post('/result_record', 'UserController@resultLogRecord');

});


Route::get('hotword', 'HotWordController@getHotWord');
Route::get('audience_interest', 'AudienceInterestController@getAudienceInterest');
Route::post('/subscriptions/{sid}/sync', 'SubscriptionController@sync');

Route::post('/quick_register', 'UserController@quickRegister');//快速注册表单提交位置
Route::get('/payment/{method}/prepare', 'SubscriptionController@prepareCheckout');

Route::post('/feedback/plan', 'FeedbackController@plan')->middleware('throttle:30,60');

Route::get('/advertisers', 'AdvertisersController@getPublishers');
Route::get('/advertisers/{facebookId}', 'AdvertisersController@getPublisherAnalysis');
Route::get('/advertisers/{facebookId}/{adRank}', 'AdvertisersController@getTopAds');


