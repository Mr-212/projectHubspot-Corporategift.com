<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HupSpotServiceController;

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

Route::get('/clear-cache', function() {

    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('route:cache');

    return 'cache has been cleared!';

});

Route::get('/generate-key', function() {

    Artisan::call('key:generate');

    return 'KEY GENERATED!';

});

Route::get('/', function () {
    return view('welcome');
});



Route::match(['get', 'post'], 'hupspot-authentication',[HupSpotServiceController::class,'hupspot_auth_token_generator']);
Route::match(['get', 'post'], 'callback',[HupSpotServiceController::class,'callback']);

//Route::match(['get', 'post'], 'hupspot-authentication',[HupSpotServiceController::class,'get_access_token']);


Route::middleware(['hubspot_signature_verification'])->group(function(){
    Route::match(['get', 'post'], 'hupspot-data-fetch-request',[HupSpotServiceController::class,'hupspot_data_fetch_request']);
});

Route::match(['get', 'post'], 'get_all_gift_products',[HupSpotServiceController::class,'get_all_gift_products']);

//Route::match(['get', 'post'], 'hupspot-data-fetch-request',[HupSpotServiceController::class,'hupspot_data_fetch_request']);

Route::match(['get', 'post'], 'getGiftById',[HupSpotServiceController::class,'getGiftById']);
Route::match(['get', 'post'], 'createGiftProductOrder',[HupSpotServiceController::class,'createGiftProductOrder']);
Route::match(['get', 'post'], 'createGiftByProductId',[HupSpotServiceController::class,'createGiftByProductId']);
//Route::match(['get', 'post'], 'get_all_gift_products',[HupSpotServiceController::class,'get_all_gift_products']);


//Route::get('hupspot-data-fetch-request',[HupSpotServiceController::class,'hupspot_data_fetch_request']);
Route::match(['get', 'post'], 'get_hupspot_send_gift_request',[HupSpotServiceController::class,'get_hupspot_send_gift_request']);
Route::match(['get', 'post'], 'post_hubspot_send_gift_request',[HupSpotServiceController::class,'post_hubspot_send_gift_request']);
Route::match(['get', 'post'], 'create_gift_form',[HupSpotServiceController::class,'create_gift_form']);
//Route::match(['get', 'post'], 'create_gift_form',[HupSpotServiceController::class,'create_gift_form']);