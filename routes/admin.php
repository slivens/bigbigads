<?php

Voyager::routes();

Route::get('/refunds/{id}/accept', 'Admin\RefundController@acceptRefund')->name('refund_accept');

Route::get('/refunds/{id}/reject', 'Admin\RefundController@rejectRefund')->name('refund_reject');

Route::get('/permission_map', 'Admin\RoleController@showPermissionMap')->name('permission_map');
Route::post('/permission_map', 'Admin\RoleController@storePermissionMap')->name('store_permission_map');

Route::post('/permission/cache/generate', 'Admin\RoleController@generatePermissionCache')->name('generate_permission_cache');
