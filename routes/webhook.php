<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HupSpotServiceController;

/*
|--------------------------------------------------------------------------
| Webhook Routes
|--------------------------------------------------------------------------
|
| Here is where you can register webhook routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::prefix('webhook')->group(function () {
    Route::prefix('hubspot')->group(function () {
        Route::match(['get', 'post'], 'contact',[WebhookController::class,'hubspot_contact']);
    });
    
});