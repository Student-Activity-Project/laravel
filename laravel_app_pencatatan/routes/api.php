<?php

use App\Http\Controllers\api\ListdataController;
use App\Http\Controllers\api\TransaksiController;
use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\JadwalController;
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

Route::post('listdata/{id}/sold', [ListDataController::class, 'markAsSold']);
