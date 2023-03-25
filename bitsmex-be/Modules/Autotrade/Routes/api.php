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

Route::group(['middleware' => ['maintenance']], function() {
    Route::group(['prefix' => '/autotrade', 'middleware' => ['jwt.auth']], function() {
        Route::get('/overview', 'AutotradeController@overview');
        Route::get('/histories', 'AutotradeController@getHistoryPackage');
        Route::post('/buyPackage', 'AutotradeController@buyPackage');
        Route::post('/swap', 'AutotradeController@swap');
        Route::post('/borrow', 'AutotradeController@borrowMoney');
        Route::post('/activeBot', 'AutotradeController@activeBot');
        Route::post('/pay', 'AutotradeController@pay');
        Route::post('/activeBot', 'AutotradeController@activeBot');
        Route::post('/getCom', 'AutotradeController@postWithdrawCom');
    });
});