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

Route::group(['prefix' => 'v1'], function(){
    Route::post('customer', 'CustomerController@customer');
    Route::post('userinfo', 'CustomerController@getuserinfo');
    Route::post('getcatalog', 'CatalogController@index');
    Route::get('getcolors', 'ColorsController@index');
    Route::post('getinvoices', 'InvoiceController@index');
    Route::post('getoneinvoice', 'InvoiceController@findone');
    Route::post('getinvoicesdetails', 'InvoiceController@detail');
    Route::post('getinvoicesummary', 'InvoiceController@summary');
    Route::post('getstatement', 'StatementController@index');
    Route::post('submitorder', 'OrderController@submit');
});
