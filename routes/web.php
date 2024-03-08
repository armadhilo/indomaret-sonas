<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\InitialSoController;
use App\Http\Controllers\InputKksoController;
use App\Http\Controllers\InputLokasiController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProsesBaSoController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingJalur;
use App\Http\Controllers\SettingJalurHHController;
use Illuminate\Support\Facades\Redirect;
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
    });

    Route::group(['prefix' => 'initial-so'], function(){
        Route::get('/', [InitialSoController::class, 'index']);

        Route::group(['prefix' => 'action'], function(){
            Route::get('/check-persiapan-data-so', [InitialSoController::class, 'checkPersiapanDataSo']);
            Route::post('/start-data-so', [InitialSoController::class, 'actionStartPersiapanDataSo']);
            Route::post('/copy-master-lokasi-so', [InitialSoController::class, 'actionCopyMasterLokasi']);
        });
    });

    Route::group(['prefix' => 'input-lokasi'], function(){
        Route::get('/', [InputLokasiController::class, 'index']);
        Route::get('/detail', [InputLokasiController::class, 'detail']);

        Route::group(['prefix' => 'action'], function(){
            Route::post('/action-add-lokasi', [InputLokasiController::class, 'actionAddLokasi']);

            // Detail Action
            Route::get('/detail-datatables', [InputLokasiController::class, 'datatablesDetailLokasi']);

            Route::get('/get-last-number', [InputLokasiController::class, 'getLastNumber']);
            Route::get('/get-desc-plu/{prdcd}', [InputLokasiController::class, 'getDescPlu']);
            Route::post('/action-save', [InputLokasiController::class, 'actionSave']);
        });
    });

    Route::group(['prefix' => 'input-kkso'], function(){
        Route::get('/', [InputKksoController::class, 'index']);
        Route::get('/datatables', [InputKksoController::class, 'getData']);

        Route::group(['prefix' => 'action'], function(){
            Route::post('/update', [InputKksoController::class, 'actionUpdate']);
        });
    });

    Route::group(['prefix' => 'setting-jalur'], function(){
        Route::get('/', [SettingJalurHHController::class, 'index']);

        Route::group(['prefix' => 'action'], function(){
            Route::post('/update-jalur', [SettingJalurHHController::class, 'actionUpdate']);
        });
    });

    Route::group(['prefix' => 'proses-ba-so'], function(){
        Route::get('/', [ProsesBaSoController::class, 'index']);

        Route::group(['prefix' => 'action'], function(){
            Route::post('/draft-action', [ProsesBaSoController::class, 'action']);
            Route::post('/proses-ba-so', [ProsesBaSoController::class, 'prosesBaSo']);
        });
    });

    Route::group(['prefix' => 'report'], function(){
        Route::get('/', [ReportController::class, 'index']);

        Route::group(['prefix' => 'list-form-kkso'], function(){
            Route::get('/', [ReportController::class, 'index']);
            Route::get('/pdf', [ReportController::class, 'dummyData']);
        });

        Route::group(['prefix' => 'register-kkso'], function(){
            Route::get('/', [ReportController::class, 'index']);
            Route::get('/pdf', [ReportController::class, 'dummyData']);
            
        });

        Route::group(['prefix' => 'edit-list-kkso'], function(){
            Route::get('/', [ReportController::class, 'index']);
            Route::get('/pdf', [ReportController::class, 'dummyData']);

        });

        Route::group(['prefix' => 'register-kkso-2'], function(){
            Route::get('/', [ReportController::class, 'index']);
            Route::get('/pdf', [ReportController::class, 'dummyData']);

        });

        Route::group(['prefix' => 'perincian-baso'], function(){
            Route::get('/', [ReportController::class, 'index']);
            Route::get('/pdf', [ReportController::class, 'dummyData']);
        });

        Route::group(['prefix' => 'ringkasan-baso'], function(){
            Route::get('/', [ReportController::class, 'index']);
            Route::get('/pdf', [ReportController::class, 'dummyData']);
        });

        Route::group(['prefix' => 'daftar-item-adjustment'], function(){
            Route::get('/', [ReportController::class, 'index']);
            Route::get('/pdf', [ReportController::class, 'dummyData']);
        });

        Route::group(['prefix' => 'daftar-kkso-acost-0'], function(){
            Route::get('/', [ReportController::class, 'index']);
            Route::get('/pdf', [ReportController::class, 'dummyData']);
        });

        Route::group(['prefix' => 'daftar-master-lokasi-so'], function(){
            Route::get('/', [ReportController::class, 'index']);
            Route::get('/pdf', [ReportController::class, 'dummyData']);
        });

        Route::group(['prefix' => 'daftar-item-tidak-di-master'], function(){
            Route::get('/', [ReportController::class, 'index']);
            Route::get('/pdf', [ReportController::class, 'dummyData']);
        });

        Route::group(['prefix' => 'lokasi-rak-belum-di-so'], function(){
            Route::get('/', [ReportController::class, 'index']);
        });

        Route::group(['prefix' => 'inquiry-plano-sonas'], function(){
            Route::get('/', [ReportController::class, 'index']);
        });

        Route::group(['prefix' => 'list'], function(){
            Route::get('/', [ReportController::class, 'index']);
        });

        Route::group(['prefix' => 'cetak-draft-lhso'], function(){
            Route::get('/', [ReportController::class, 'index']);
        });

        Route::group(['prefix' => 'list'], function(){
            Route::get('/', [ReportController::class, 'index']);
        });

        Route::group(['prefix' => 'lokasi-so'], function(){
            Route::get('/', [ReportController::class, 'index']);
        });
    });
});
