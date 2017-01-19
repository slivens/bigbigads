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

Route::any('/forward/{action}', function(Request $req, $action) {
    $json_data = json_encode($req->all());
    $ch = curl_init();
    if ($action == 'adsearch') {
        curl_setopt($ch, CURLOPT_URL, 'http://121.41.107.126:8080/search');
    } else if ($action == "adserSearch") {
        curl_setopt($ch, CURLOPT_URL, 'http://121.41.107.126:8080/adser_search');
    } else if ($action == "adserAnalysis") {
        //curl_setopt($ch, CURLOPT_URL, 'http://121.41.107.126:8080/adser_analysis');
        curl_setopt($ch, CURLOPT_URL, 'http://103.71.178.49:5000/adser_analysis');
    } else {
        return '{"status":-1}';
    }
	/* curl_setopt($ch, CURLOPT_POST, TRUE); */
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Content-Length: ' . strlen($json_data))
	);
	/* curl_setopt($ch, CURLOPT_TIMEOUT, 1); */ 
	$result = curl_exec($ch);
    curl_close($ch);
    return;
  //  echo $res;
});
