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
    Route::group(['prefix' => '/auth'], function () {
        Route::post('remember/{remember_token}', 'AuthController@postLoginRemember');
        Route::get('/ResetDemo', 'AuthController@getResetDemo');
        Route::post('register', 'AuthController@register');
        Route::get('verify_email', 'AuthController@verify_email');
        Route::post('postSendMail', 'AuthController@postSendMail');
        Route::post('login', 'AuthController@login');
        Route::post('authenticated', 'AuthController@authenticated');
        Route::post('logout', 'AuthController@logout');
        Route::post('/resetEmail', 'AuthController@sendMail');
        Route::post('/resetPassword', 'AuthController@postReset');
        Route::post('/refresh', 'AuthController@refresh');
    });
    
    Route::group(['middleware' => ['jwt.auth']], function() {
        Route::group(['prefix' => '/profile'], function() {
            // Route::post('/', 'ProfileController@postProfile');
            // Route::get('/imageavatar', 'ProfileController@getAvatar');
            // Route::post('uploadAvatar', 'ProfileController@uploadAvatar');
    
            Route::group(['prefix' => '/user'], function() {
                Route::get('/', 'AuthController@user');
                // Route::post('/change_play_mode', 'ProfileController@ChangePlayMode');
                // Route::get('generate_2fa', 'ProfileController@Generate2FA');
                // Route::post('enable_2fa', 'ProfileController@Enable2FA');
                // Route::post('disable_2fa', 'ProfileController@Disable2FA');
            });
        });
    });
});