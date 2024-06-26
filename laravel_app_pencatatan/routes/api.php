<?php

use App\Http\Controllers\api\StokmobilController;
use App\Http\Controllers\api\StatistikController;
use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\api\RekapdataController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthenticationController::class, 'register']);
Route::post('/login', [AuthenticationController::class, 'login']);
Route::delete('/deleteUser/{id}', [AuthenticationController::class, 'deleteUser']);
Route::get('/restoreUser/{id}', [AuthenticationController::class, 'restoreUser']);

Route::group(['middleware' => ['auth:sanctum']], function(){

    Route::apiResource('listdata', StokmobilController::class);

    Route::post('updateUser/{id}', [AuthenticationController::class, 'updateUser']);

    Route::put('listdata/{id}/status', [StokmobilController::class, 'updateStatus']);
    Route::post('updateFoto/{id}', [StokmobilController::class, 'updateFoto']);

    Route::get('total-unit-keseluruhan', [StatistikController::class, 'totalUnitKeseluruhan']);
    Route::get('total-unit-tersedia', [StatistikController::class, 'totalUnitTersedia']);
    Route::get('total-unit-terjual', [StatistikController::class, 'totalUnitTerjual']);
    Route::get('total-unit-manual', [StatistikController::class, 'totalTransmisiManual']);
    Route::get('total-unit-matic', [StatistikController::class, 'totalTransmisiMatic']);
    Route::get('total-penjualan', [StatistikController::class, 'getTotalPenjualan']);
    Route::get('total-penjualan-tanggal', [StatistikController::class, 'getTotalPenjualanTanggal']);

    Route::get('data-by-date', [RekapdataController::class, 'dataListByDateRange']);
    Route::get('rekap/merk', [RekapdataController::class, 'dataListByMerk']);
    Route::get('rekap/transmisi', [RekapdataController::class, 'dataListByTransmisi']);
    Route::get('rekap/tahun', [RekapdataController::class, 'dataListByTahun']);
});


