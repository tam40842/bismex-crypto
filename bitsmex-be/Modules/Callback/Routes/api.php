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

Route::group(['prefix' => '/callback'], function() {
    // Route::post('/coinbase', 'CallbackController@coinbase')->name('api.coinbase');
    // Route::match(['get', 'post'], '/coinpayments', 'CallbackController@coinpayments')->name('api.coinpayments');
    // Route::match(['get', 'post'], '/vndc', 'CallbackController@vndc')->name('api.vndc');
    Route::match(['get', 'post'], '/', 'CallbackController@bep20')->name('api.bep20');
});