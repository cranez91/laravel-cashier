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

Route::post('/payment', 'App\Http\Controllers\SuscripcionController@pago');
Route::get('/subscription', function () {
    return view('subscription');
});
Route::post('/process-subscription', 'App\Http\Controllers\SuscripcionController@processSubscription');
Route::get('/upgrade-subscription', 'App\Http\Controllers\SuscripcionController@upgradeSubscription');
Route::get('/cancel-subscription', 'App\Http\Controllers\SuscripcionController@cancelSubscription');

Route::get('/invoices', 'App\Http\Controllers\SuscripcionController@invoices');
Route::get('/invoice/{invoice_id}', 'App\Http\Controllers\SuscripcionController@invoice');