<?php

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

Auth::routes();

Route::get('/login-from-platform', 'AuthController@loginFromPlatform');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/logout-from-platform', 'AuthController@logoutFromPlatform')->name("logout.platform");
    Route::get('call-log-identification/{phoneBillId}', 'HomeController@identifyCall')->name('call-log-identification');
    Route::get('/receipt-files/download', 'HomeController@downloadFile')->name('receipt.download');

    //Route::get('home/{callId}', 'HomeController@callDetails')->name('home');
});

Route::get('/', 'HomeController@index')->name('/');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/help-center', 'HelpController@index')->name('help-center');