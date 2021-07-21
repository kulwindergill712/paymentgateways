<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('flutterwave')->group(function () {
    Route::post('/charge', 'FlutterWaveController@generate_payment');
    Route::get('/success', 'FlutterWaveController@verify');
    Route::get('/verify', 'FlutterWaveController@verifytransaction');
    Route::get('/transaction_detail', 'FlutterWaveController@get_transaction_info');

});
Route::prefix('sms')->group(function () {
    Route::post('/send', 'SmsLeopardController@send_message');
    Route::post('/s1end', 'SmsLeopardController@s1end_message');

});
Route::post('/sra/send', 'SmsLeopardController@sendMessage');

Route::prefix('monri')->group(function () {
    Route::post('/charge', 'MonriController@generate_payment');
    Route::get('/callback', 'MonriController@callbackPage');
    Route::get('/verify/{token}', 'MonriController@verifytransaction');

});

Route::prefix('raz')->group(function () {
    Route::get('/charge', 'RazorpayController@charge');
    Route::post('/payment-complete', 'RazorpayController@Complete');

});
Route::prefix('raz2')->group(function () {
    Route::get('/charge', 'Razorpay2Controller@charge');
    Route::post('/payment-complete', 'Razorpay2Controller@Complete');

});
Route::prefix('brain')->group(function () {

    Route::get('/createtoken', 'BraintreeController@createtoken');
    Route::get('/charge', 'BraintreeController@charge');
    Route::get('/checkout', 'BraintreeController@checkout');

});

Route::prefix('rj')->group(function () {

    Route::get('/createtoken', 'RjbankController@token');
    Route::get('/success', 'RjbankController@success');
    Route::get('/confirm/{id}', 'RjbankController@confirm');
});

Route::prefix('dpo')->group(function () {

    Route::get('/createtoken', 'DPOController@token');
    Route::get('/success', 'DPOController@success');
    Route::get('/verify', 'DPOController@verify');

    Route::get('/confirm/{id}', 'DPOController@confirm');
});

Route::prefix('split')->group(function () {

    Route::post('/createtoken', 'HyperSplitController@token');
    Route::get('/createtoken1', 'HyperSplitController@new');
    Route::get('/success', 'HyperSplitController@success');
    Route::get('/confirm/{id}', 'HyperSplitController@confirm');
});

Route::get('/local', 'HyperSplitController@local');
Route::get('/locals', 'HyperSplitController@locals');
Route::get('c/hyperpay/callback', 'HyperSplitController@callback');
