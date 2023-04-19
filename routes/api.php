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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['return-json', 'auth:api'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'Api', 'middleware' => 'return-json'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('/login', 'AuthController@login');
        Route::post('/login-token', 'AuthController@loginWithToken');
    });

    //NOTE: WRITE YOUR API ROUTES HERE

    Route::group(['prefix' => 'phonebilling', 'middleware' => ['auth:api']], function () {  
        Route::post('/import-phone-bill-template', 'PhoneBillController@importWithTemplate');
        Route::post('/dispatch-email', 'PhoneBillController@sendEmail');
        Route::post('/update-phone-bill-extensions', 'PhoneBillController@updatePhoneExtensions');
        Route::post('/update-phone-bill', 'PhoneBillController@updatePhoneBillData');
        Route::post('/complete-call-log-identification', 'PhoneBillController@callLogIdentificationCompleted');
    
        Route::post('/update-phone-bill-reviewed', 'PhoneBillController@updatePhoneBillDataReviewed');
        Route::get('/list-current-phone-bill', 'PhoneBillController@getCurrentUserPhoneBill');
        Route::get('/list-all-phone-bill', 'PhoneBillController@getAllUserPhoneBill');
        Route::get('/phone-bill-details/{id}', 'PhoneBillController@getPhoneBillDetails');
        Route::get('/single-identified-phone-bill-details/{id}', 'PhoneBillController@getSingleIdentifiedPhoneBillDetails');
        Route::get('/list-all-identified-phone-bill', 'PhoneBillController@getAllIdentifiedPhoneBill');
        Route::get('/call-log-for', 'PhoneBillController@getUserWhoseCallLogIs');
        Route::get('/phone-bill-extensions-details/{id}', 'PhoneBillController@phoneBillExtensionsDetails');
        Route::get('/user-details/{id}', 'PhoneBillController@userDetails');
        Route::post('/upload-receipt', 'PhoneBillController@uploadPayment');
        Route::post('/upload-receipt-two', 'PhoneBillController@uploadPaymentTwo');
        Route::get('/list-all-phone-bills-for-reconciliation', 'PhoneBillController@getPhoneBillBSC');
        Route::get('/timeline/{id}', 'PhoneBillController@getCallLogTimeline');
        Route::post('/update-payroll-reconcilled', 'PhoneBillController@updatePayrollReconcilled');
        Route::post('/update-payment-receipt-reconcilled', 'PhoneBillController@updatePaymentReceiptReconcilled');
        Route::get('/file', 'PhoneBillController@getPhoneBillFile');
        Route::get('/phone-bill-status/{billownerid?}', 'PhoneBillController@getUserPhoneBillStatus');
        Route::get('/download-receipt', 'PhoneBillController@downloadUploadedReceipt');
    });
});
