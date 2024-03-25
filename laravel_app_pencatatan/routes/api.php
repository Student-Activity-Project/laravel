<?php

use App\Http\Controllers\api\listdataController;
use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\JadwalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

use App\Http\Controllers\UserController;
Route::post('/login', [AuthenticationController::class, 'login']);
Route::post('/register', [AuthenticationController::class, 'register']);



Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::apiResource('user', UserController::class);
    Route::apiResource('jadwal', JadwalController::class);
    Route::apiResource('absen', ListdataController::class);
});

