<?php

Voyager::routes();
Route::get('/refunds/{id}/accept', 'Admin\RefundController@acceptRefund')->name('refund_accept');
Route::get('/refunds/{id}/reject', 'Admin\RefundController@rejectRefund')->name('refund_reject');
