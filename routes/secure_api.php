<?php

Route::post('/register_verify', 'UserController@registerVerify');
// TODO:payum会检查return url与预设是否一致，通过nuxt访问这里会导致不一致，因此该路由实际没生效
Route::any('/payment/paypal/done', 'SubscriptionController@onPaypalDone');
