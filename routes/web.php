<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\InitialSoController;
use App\Http\Controllers\InputKksoController;
use App\Http\Controllers\InputLokasiController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MonitoringSoController;
use App\Http\Controllers\ProsesBaSoController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SetLimitSoController;
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

        Route::group(['prefix' => 'addon'], function(){
            Route::get('/get-plu', [ReportController::class, 'getPlu']);
            Route::get('/get-description/{prd_prdcd}', [ReportController::class, 'getPluDesc']);
        });

        Route::group(['prefix' => 'list-form-kkso'], function(){
            Route::get('/', [ReportController::class, 'index']);
            Route::post('/show-pdf', [ReportController::class, 'reportListFormKkso']);
            Route::get('/pdf', [ReportController::class, 'reportListFormKkso']);
        });

        Route::group(['prefix' => 'register-kkso'], function(){
            Route::get('/', [ReportController::class, 'index']);
            Route::post('/show-pdf', [ReportController::class, 'reportRegisterKkso1']);
            Route::get('/pdf', [ReportController::class, 'reportRegisterKkso1']);
        });

        Route::group(['prefix' => 'edit-list-kkso'], function(){
            Route::get('/', [ReportController::class, 'index']);
            Route::post('/show-pdf', [ReportController::class, 'reportEditListKkso']);
            Route::get('/pdf', [ReportController::class, 'reportEditListKkso']);
        });

        Route::group(['prefix' => 'register-kkso-2'], function(){
            Route::get('/', [ReportController::class, 'index']);
            Route::post('/show-pdf', [ReportController::class, 'reportRegisterKkso2']);
            Route::get('/pdf', [ReportController::class, 'reportRegisterKkso2']);
        });

        Route::group(['prefix' => 'perincian-baso'], function(){
            Route::get('/', [ReportController::class, 'index']);
            Route::post('/show-pdf', [ReportController::class, 'reportPerincianBaso']);
            Route::get('/pdf', [ReportController::class, 'reportPerincianBaso']);
        });

        Route::group(['prefix' => 'ringkasan-baso'], function(){
            Route::get('/', [ReportController::class, 'index']);
            Route::post('/show-pdf', [ReportController::class, 'reportRingkasanBaso']);
            Route::get('/pdf', [ReportController::class, 'reportRingkasanBaso']);
        });

        Route::group(['prefix' => 'daftar-item-adjustment'], function(){
            Route::get('/', [ReportController::class, 'index']);
            Route::post('/show-pdf', [ReportController::class, 'reportDaftarItemYangSudahAdjust']);
            Route::get('/pdf', [ReportController::class, 'reportDaftarItemYangSudahAdjust']);
        });

        Route::group(['prefix' => 'daftar-kkso-acost-0'], function(){
            Route::get('/', [ReportController::class, 'index']);
            Route::post('/show-pdf', [ReportController::class, 'reportDafterKksoAcost']);
            Route::get('/pdf', [ReportController::class, 'reportDafterKksoAcost']);
        });

        Route::group(['prefix' => 'daftar-master-lokasi-so'], function(){
            Route::get('/', [ReportController::class, 'index']);
            Route::post('/show-pdf', [ReportController::class, 'reportDaftarMasterLokasiSo']);
            Route::get('/pdf', [ReportController::class, 'reportDaftarMasterLokasiSo']);
        });

        Route::group(['prefix' => 'daftar-item-tidak-di-master'], function(){
            Route::get('/', [ReportController::class, 'index']);
            Route::post('/show-pdf', [ReportController::class, 'reportDaftarItemBelumAdaDiMaster']);
            Route::get('/pdf', [ReportController::class, 'reportDaftarItemBelumAdaDiMaster']);
        });

        Route::group(['prefix' => 'lokasi-rak-belum-di-so'], function(){
            Route::get('/', [ReportController::class, 'index']);
            Route::post('/show-pdf', [ReportController::class, 'reportRakBelumSo']);
            Route::get('/pdf', [ReportController::class, 'reportRakBelumSo']);
        });

        Route::group(['prefix' => 'inquiry-plano-sonas'], function(){
            Route::get('/', [ReportController::class, 'index']);
            Route::get('/download-excel', [ReportController::class, 'reportInqueryPlanoSonasExcel']);
        });

        Route::group(['prefix' => 'lpp-month-end'], function(){
            Route::get('/', [ReportController::class, 'index']);
            Route::get('/datatables', [ReportController::class, 'reportLppMonthEndExcelDatatables']);

            Route::group(['prefix' => 'action'], function(){
                Route::post('/cetak-lpp', [ReportController::class, 'reportLppMonthEndExcelActionCetak']);
                Route::post('/simpan-data', [ReportController::class, 'reportLppMonthEndExcelActionSimpanDataPlu']);
                Route::delete('/reset-data', [ReportController::class, 'reportLppMonthEndExcelActionResetData']);
            });
        });

        Route::group(['prefix' => 'cetak-draft-lhso'], function(){
            Route::get('/', [ReportController::class, 'index']);
            Route::post('/show-pdf', [ReportController::class, 'reportCetakDraftLhso']);
            Route::get('/pdf', [ReportController::class, 'reportCetakDraftLhso']);
        });

        Route::group(['prefix' => 'cetak-draft-sebelum-lhso'], function(){
            Route::get('/', [ReportController::class, 'index']);
        });

        Route::group(['prefix' => 'lokasi-so'], function(){
            Route::get('/', [ReportController::class, 'index']);
            Route::post('/show-pdf', [ReportController::class, 'reportLokasiSo']);
            Route::get('/pdf', [ReportController::class, 'reportLokasiSo']);
        });
    });

    Route::group(['prefix' => 'monitoring'], function(){
        Route::get('/', [MonitoringSoController::class, 'index']);

        Route::get('/get-monitoring', [MonitoringSoController::class, 'getMonitoring']);
        Route::get('/get-monitoring-level/{lso_koderak}/{lso_kodesubrak?}/{lso_tiperak?}', [MonitoringSoController::class, 'showLevel']);

        Route::get('/datatables', [MonitoringSoController::class, 'datatables']);
        Route::get('/print-struk-so/{tanggal_start_so}/{KodeRak}/{KodeSubRak}/{TipeRak?}/{ShelvingRak?}', [MonitoringSoController::class, 'printStrukSO']);
    });

    Route::group(['prefix' => 'set-limit-so'], function(){
        Route::get('/', [SetLimitSoController::class, 'index']);

        Route::group(['prefix' => 'action'], function(){
            Route::get('/datatables/{tahap}/{tglSO}', [SetLimitSoController::class, 'loadDatatables']);
            Route::post('/proses-ba-so', [SetLimitSoController::class, 'prosesBaSo']);
        });
    });
});
