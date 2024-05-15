<?php

use App\Http\Controllers\api\ListdataController;
use App\Http\Controllers\api\TransaksiController;
use App\Http\Controllers\api\StatistikController;
use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\JadwalController;
use App\Http\Controllers\api\RekapdataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::post('/login', [AuthenticationController::class, 'login']);
Route::post('/register', [AuthenticationController::class, 'register']);


Route::group(['middleware' => 'auth:sanctum'], function() {
});

Route::apiResource('user', UserController::class);
Route::apiResource('listdata', ListdataController::class);
Route::apiResource('transaksi', TransaksiController::class);

Route::put('listdata/{id}/status', [ListdataController::class, 'updateStatus']);
Route::get('listdata/{status}', 'ListDataController@index');

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

