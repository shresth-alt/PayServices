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

Route::redirect('/', '/admin');

Route::group(['prefix' => 'admin'], function() {
  Route::get('/', 'LoginController@login')->name('login');
  Route::post('/authenticate', 'LoginController@authenticate')->name('authenticate');
  Route::get('/forgot-password', 'LoginController@forgotPassword')->name('forgot.password');

  Route::group(['middleware' => ['auth']], function() {
    Route::get('/dashboard', 'AdminController@dashboard')->name('dashboard');
    Route::get('/edit/customer', 'UserController@editcustomer')->name('editcustomer');
    /** Service routes */
    Route::get('/services', 'ServiceController@show')->name('services.list')->middleware('cloudinary.init');
    Route::get('/services/edit/{id}', 'ServiceController@edit')->name('service.edit')->middleware('cloudinary.init');
    Route::get('/services/add', 'ServiceController@add')->name('service.add');
    Route::get('/services/delete/{id}', 'ServiceController@delete')->name('service.delete');
    Route::post('/services', 'ServiceController@addService')->name('service.post')->middleware('cloudinary.init');
    Route::put('/services/{id}', 'ServiceController@updateService')->name('service.update')->middleware('cloudinary.init');
    /** users routes */
    Route::get('/users', 'UserController@list')->name('users');
    Route::get('/users/delete/{id}', 'UserController@delete')->name('user.delete');
    Route::get('/users/add', 'UserController@addUser')->name('user.add');
    Route::post('/users', 'UserController@saveUser')->name('user.post');
    Route::get('/users/{id}/edit', 'UserController@edit')->name('user.edit');
    Route::put('/users/{id}', 'UserController@update')->name('user.update');
    Route::get('/users/service-list/{id}', 'UserController@service')->name('user.service');
    Route::post('/users/{id}/document-update', 'UserController@updateDocuments')->name('user.document');
    /** Order routes */
    Route::get('/orders', 'OrderController@list')->name('orders.list');
    Route::get('/orders/edit/{id}', 'OrderController@edit')->name('order.edit');
    Route::put('/orders/{id}', 'OrderController@update')->name('order.update');
    Route::get('/orders/item-details/{id}', 'OrderController@orderDetails')->name('orders.order');
    Route::get('/order/delete/{id}', 'OrderController@delete')->name('order.delete');

    /** Accounts routes */
    Route::get('/accounts', 'OrderController@accounts')->name('order.accounts');
    /**export as excel */
    Route::get('/export/{type}', 'OrderController@export')->name('order.export');
    /** Home widget routes */
    Route::get('/widgets', 'HomeWidgetController@list')->name('widgets.list')->middleware('cloudinary.init');
    Route::get('/widgets/requests', 'HomeWidgetController@widget_requests')->name('widgets.requests');
    Route::get('/widgets/requests/delete/{id}', 'HomeWidgetController@widget_request_delete')->name('widgets.request.delete');
    Route::get('/widgets/add', 'HomeWidgetController@add')->name('widget.add');
    Route::get('/widgets/edit/{id}', 'HomeWidgetController@edit')->name('widget.edit')->middleware('cloudinary.init');
    Route::post('/widgets', 'HomeWidgetController@store')->name('widget.post')->middleware('cloudinary.init');
    Route::get('/widgets/delete/{id}', 'HomeWidgetController@delete')->name('widget.delete');
    Route::put('/widgets/{id}', 'HomeWidgetController@update')->name('widget.update')->middleware('cloudinary.init');
    /** Generate invoice */
    Route::get('/generate-invoice/{id}', 'InvoiceController@createInvoice')->name('save.invoice');
    Route::get('/unauthorized-access', 'UnauthorizedAccessController@index')->name('unauthorized');
    Route::get('/logout', 'LoginController@logout')->name('logout');
  });
  Route::get('/showvendors/{id}', 'api\v1\OrderController@sendNotificationsToAllVendors')->name('notifications');
});
Route::get('/test-noti', 'api\v1\OrderController@testNotification');
Route::get('/do-test', 'TestController@index');
/**Send Email */
Route::get('/send-invoice/{id}', 'InvoiceController@sendInvoice');
Route::get('/invoice_no', 'TestController@invoice_no');