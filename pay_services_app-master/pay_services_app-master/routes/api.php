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


Route::group(['prefix' => 'v1'], function () {
  # Authentication not required in 2 calls
  Route::post('/authenticate', 'api\v1\LoginController@authenticate');
  Route::post('/authenticate-vendor', 'api\v1\LoginController@authenticateWithPassword');
  Route::post('/send-otp', 'api\v1\OTPController@sendOTP')->name('api.otp');
  Route::post('/users', 'api\v1\UserController@create');
  Route::post('/reset-password', 'api\v1\UserController@resetPassword')->name('api.password');

  # Authentication required in below calls
  Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/services', 'api\v1\ServiceController@index');
    Route::post('/orders', 'api\v1\OrderController@create');

    Route::post('/user-addresses', 'api\v1\UserController@add_user_address');
    Route::get('/user-addresses', 'api\v1\UserController@user_addresses');
    Route::delete('/user-addresses/{id}', 'api\v1\UserAddressController@delete');

    Route::get('/orders', 'api\v1\OrderController@index');
    Route::get('/orders/{id}', 'api\v1\OrderController@getOrderDetail');
    Route::get('/home-widgets-services', 'api\v1\HomeWidgetController@index');
    Route::post('/home-widgets-requests', 'api\v1\HomeWidgetController@widget_request');
    Route::put('/cancel-order/{order_id}', 'api\v1\OrderController@cancelOrder');
    
    Route::put('/rate-vendor/{order_id}', 'api\v1\OrderController@saveRatingReview');
    
    Route::post('/payment-request-hash', 'api\v1\PaymentController@createPaymentRequestHash');
    Route::post('/update-user-token', 'api\v1\DeviceController@updateUserDeviceToken');
    Route::put('/accept-order/{order_id}', 'api\v1\OrderController@acceptOrder');
    Route::get('/current-user', 'api\v1\UserController@currentUser');
    Route::put('/update-profile', 'api\v1\UserController@updateUser');
    Route::put('/complete-order/{id}', 'api\v1\OrderController@completeOrder');
    Route::post('/payments', 'api\v1\PaymentController@initPayment');
    Route::post('/process-online-payment', 'api\v1\PaymentController@processOnlinePayment');
    Route::post('/process-cash-payment', 'api\v1\PaymentController@processCashPayment');
    Route::post('/update-vendor-payment', 'api\v1\UserController@updateVendorPayment');
    Route::get('/payments', 'api\v1\OrderController@index');

    Route::get('/vendor-services', 'api\v1\ServiceController@servicesForVendor');
    Route::put('/update-order-comment/{id}', 'api\v1\OrderController@updateOrderComment');
    Route::get('/account-statements', 'api\v1\VendorPaymentController@getPaymentsHistory');
  });
});
