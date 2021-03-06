<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HupSpotServiceController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\WebhookController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

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

// Route::get('/migrate_fresh', function() {

//     Artisan::call('migrate:fresh');
//     return 'migrations refreshed!';

// });

// Route::get('/migrate_rollback', function() {

//     Artisan::call('migrate:rollback --step=1');
//     Artisan::call('migrate');
//     return 'migrations done!';

// });

// Route::get('/generate-key', function() {

//     Artisan::call('key:generate');

//     return 'KEY GENERATED!';

// });

Route::middleware(['auth'])->group(function(){
    Route::get('/dashboard', [DashboardController::class,'dashboard']);
    Route::match(['get', 'post'], 'hupspot-authentication',[HupSpotServiceController::class,'hupspot_auth_token_generator']);

});

Route::prefix('/')->group(function () {
   
    Route::get('/', [DashboardController::class,'index']);
  
    Route::middleware(['hubspot_signature_verification'])->group(function(){
         Route::match(['get', 'post'], 'hupspot_data_fetch_request',[HupSpotServiceController::class,'hupspot_data_fetch_request']);
    });

    Route::match(['get', 'post'], 'get_all_gift_products',[HupSpotServiceController::class,'get_all_gift_products']);
    Route::match(['get', 'post'], 'getGiftById',[HupSpotServiceController::class,'getGiftById']);
    Route::match(['get', 'post'], 'createGiftProductOrder',[HupSpotServiceController::class,'createGiftProductOrder']);
    Route::match(['get', 'post'], 'createGiftByProductId',[HupSpotServiceController::class,'createGiftByProductId']);
    //Route::match(['get', 'post'], 'get_all_gift_products',[HupSpotServiceController::class,'get_all_gift_products']);

    Route::match(['get', 'post'], 'get_hupspot_send_gift_request/{identifier}',[HupSpotServiceController::class,'get_hupspot_send_gift_request']);
    Route::match(['get', 'post'], 'post_hubspot_send_gift_request',[HupSpotServiceController::class,'post_hubspot_send_gift_request']);
    //Route::match(['get', 'post'], 'create_gift_form',[HupSpotServiceController::class,'create_gift_form']);
    Route::post('post_corporate_gift_token',[HupSpotServiceController::class,'post_corporate_gift_token']);
    Route::post('refresh_access_token',[HupSpotServiceController::class,'refresh_access_token']);

});


Route::get('setup-guide',[KnowledgeBaseController::class,'setup_guide_doc']);
Route::get('privacy-policy',[KnowledgeBaseController::class,'privacy_policy']);
Route::get('terms-of-services',[KnowledgeBaseController::class,'terms_of_services']);



