<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\NewsController;
use \App\Http\Controllers\RealtyController;
use App\Http\Controllers\RealtyTypeController;
use App\Http\Controllers\SlideController;
use App\Http\Controllers\UserController;
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

Route::post('login', [AuthController::class, 'login']);

Route::prefix('realty')->group(function () {
    Route::get('map', [RealtyController::class, 'mapRealty']);
    Route::get('count', [RealtyController::class, 'count']);
    Route::get('minMax', [RealtyController::class, 'minMax']);
});

Route::apiResource('realtyType', RealtyTypeController::class)->only(['index', 'show']);
Route::apiResource('news', NewsController::class)->only(['index', 'show']);
Route::apiResource('slide', SlideController::class)->only(['index']);
Route::apiResource('contact', ContactController::class)->only(['index']);
Route::apiResource('realty', RealtyController::class)->only(['index', 'update', 'show']);
Route::apiResource('equipment', EquipmentController::class)->only(['index']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('user/byToken', [UserController::class, 'byToken']);
    Route::post('logout', [AuthController::class, 'logout']);

});
