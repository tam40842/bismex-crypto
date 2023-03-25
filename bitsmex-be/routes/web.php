<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

include('admin.php');
Route::get('/{any}', function() {
    if(!Auth::check()) {
        return view('index');
    }
    return redirect('/admin');
})->where('any', '^(?!api|bot).*$');