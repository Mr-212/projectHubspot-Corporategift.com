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

// Route::get('/', function () {
//     return view('welcome');
// });



 Route::prefix('auth')->group(function(){
    Route::get('login', [AuthController::class, 'get_login'])->name('login');
    Route::post('login', [AuthController::class, 'post_login']);
    Route::get('sign-up', [AuthController::class, 'get_sign_up']);
    Route::post('sign-up', [AuthController::class, 'post_sign_up']);
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('forget-password', [AuthController::class, 'get_forget_password']);
    Route::post('forget-password', [AuthController::class, 'post_forget_password']);
 });

 Route::prefix('password')->group(function(){
    
 });
 Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');
