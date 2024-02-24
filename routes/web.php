<?php

use App\Http\Controllers\CashierController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\StrukController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//LOGIN
Route::post('/login', [LoginController::class, 'login']);
Route::get('/login', [LoginController::class, 'index']);
Route::get('/logout', [LoginController::class, 'logout']);

Route::middleware(['mylogin'])->group(function () {
    //HOME
    Route::group(['prefix' => 'home'], function(){

        Route::get('/', [HomeController::class, 'index']);
        Route::get('/load-toko', [HomeController::class, 'loadToko']);
        Route::get('/load-pb', [HomeController::class, 'loadPb']);
        Route::get('/load-transaction/{cbToko}/{cbNoPb}/{kodeCustomer}', [HomeController::class, 'selectNoPb']);
        
        Route::post('/login-update', [HomeController::class, 'authentication']);
        Route::post('/update-tempo', [HomeController::class, 'updateJatuhTempo']);
    });
});