<?php

use App\Http\Controllers\api\listdataController;
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
