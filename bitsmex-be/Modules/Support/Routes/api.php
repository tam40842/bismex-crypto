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

Route::group(['prefix' => '/support', 'middleware' => ['jwt.auth', 'maintenance']], function() {
    Route::get('/tickets', 'SupportController@getTickets');
    Route::post('/create', 'SupportController@createTicket');
    Route::group(['prefix' => '/ticket'], function() {
        Route::get('/{ticketid}', 'SupportController@getDetail');
        Route::post('/reply', 'SupportController@Reply');
    });
});