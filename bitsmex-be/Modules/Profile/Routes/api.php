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
    Route::group(['prefix' => '/account', 'middleware' => ['jwt.auth']], function() {
        Route::get('/hide_balance', 'ProfileController@hideBalance');
        Route::group(['prefix' => '/profile'], function() {
            Route::post('/verifyPhone', 'ProfileController@postCerifyPhone');
            Route::post('/verifyPhoneCode', 'ProfileController@postCerifyPhoneCode');
            Route::post('/changePassword', 'ProfileController@changePassword');
            Route::post('/ChangeImage', 'ProfileController@ChangeImage');
            Route::get('/getKycDocument', 'ProfileController@getKycDocument');
            Route::post('/postKyc', 'ProfileController@postKyc');
            Route::post('/changeProfile', 'ProfileController@changeProfile');
            Route::post('/twofaSubmit', 'ProfileController@twofaSubmit');
            Route::post('/postUploadAvatar', 'ProfileController@postUploadAvatar');
        });


        Route::group(['prefix' => 'api-keys'], function() {
            Route::get('/', 'ProfileController@getAPIKeys');
            Route::post('/add', 'ProfileController@postAddAPIKeys');
            Route::post('/edit/{id}', 'ProfileController@postEditAPIKeys');
            Route::post('/delete/{id}', 'ProfileController@postDeleteAPIKeys');
        });
    });
});