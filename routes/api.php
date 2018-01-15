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

Route::group(['middleware'=>'auth'], function() {
    //Route::get('/pay', 'SubscriptionController@form');
    Route::post('/pay', 'SubscriptionController@pay');
    Route::get('/me/billings', 'SubscriptionController@billings');
    Route::post('/subscription/{id}/cancel', 'SubscriptionController@cancel');
	Route::get('/invoice/{invoice}', function (Request $request, $invoiceId) {
		return Auth::user()->downloadInvoice($invoiceId, [
			'vendor'  => 'Bigbigads',
			'product' => 'Bigbigads',
		], storage_path('invoice'));
    });
    Route::post('/changepwd', 'UserController@changepwd');
    Route::put('/payments/{number}/refund_request', 'SubscriptionController@requestRefund');
    Route::get('/me/customize_invoice', 'UserController@getInvoiceCustomer');
    Route::post('/me/customize_invoice', 'UserController@setInvoiceCustomer');
    Route::get('/invoices/{invoice_id}', 'InvoiceController@downloadInvoice');
    Route::patch('/me/profile', 'UserController@changeProfile');
    Route::post('/me/send-email', 'UserController@sendVerifyMailToSubEmail');
    Route::get('/bookmark/default', 'BookmarkController@getDefault');
    Route::post('/service_term', 'UserController@updateServiceTerm');
    Route::post('/result_record', 'UserController@resultLogRecord');
    Route::get('/affialites/{track}/payments', 'UserController@getUserListByAffiliateTrack');
});
