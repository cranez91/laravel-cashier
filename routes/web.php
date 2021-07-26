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

Route::get('/', function () {
    return view('welcome');
});

Route::post('/payment', 'App\Http\Controllers\SuscripcionController@payment');
Route::post('/create-checkout-session', 'App\Http\Controllers\SuscripcionController@createCheckoutSession');
Route::get('/billing-portal', function (Request $request) {
    return $request->user()->redirectToBillingPortal();
});
Route::get('/subscription', function () {
    return view('subscription');
});
Route::get('/checkout', function () {
    return view('checkout');
});
Route::post('/process-subscription', 'App\Http\Controllers\SuscripcionController@processSubscription');
Route::get('/upgrade-subscription', 'App\Http\Controllers\SuscripcionController@upgradeSubscription');
Route::get('/cancel-subscription', 'App\Http\Controllers\SuscripcionController@cancelSubscription');

Route::get('/invoices', 'App\Http\Controllers\SuscripcionController@invoices');
Route::get('/invoice/{invoice_id}', 'App\Http\Controllers\SuscripcionController@invoice');


/** second way */
Route::get('/subscription/create', ['as'=>'home','uses'=>'App\Http\Controllers\SuscripcionController@index'])->name('subscription.create');
Route::post('order-post', ['as'=>'order-post','uses'=>'App\Http\Controllers\SuscripcionController@orderPost']);
/** second way */

/** third way */
Route::get('/subscribe', 'App\Http\Controllers\SuscripcionController@showSubscription');
Route::post('/subscribe', 'App\Http\Controllers\SuscripcionController@processSubscription');      // welcome page only for subscribed users
Route::get('/welcome', 'App\Http\Controllers\SuscripcionController@showWelcome')->middleware('subscribed');
/** third way */