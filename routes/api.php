<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\Object1cController;
use \App\Http\Controllers\RealtyController;
use App\Http\Controllers\RealtyTypeController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\SlideController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Models\Role;
use App\Models\User;
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

Route::post('login', [AuthController::class, 'login']);
Route::post('mail', [RequestController::class, 'index']);
Route::prefix('realty')->group(
    function () {
        Route::get('map', [RealtyController::class, 'mapRealty']);
        Route::get('count', [RealtyController::class, 'count']);
        Route::get('minMax', [RealtyController::class, 'minMax']);
    }
);

Route::apiResource('realtyType', RealtyTypeController::class)->only(['index', 'show']);
Route::apiResource('news', NewsController::class)->only(['index', 'show']);
Route::apiResource('slide', SlideController::class)->only(['index', 'show']);
Route::apiResource('contact', ContactController::class)->only(['index', 'show']);
Route::apiResource('realty', RealtyController::class)->only(['index', 'show']);
Route::apiResource('equipment', EquipmentController::class)->only(['index', 'show']);
Route::apiResource('ticket', TicketController::class)->only(['index', 'store']);
Route::get('ticket/count', [TicketController::class, 'count']);


Route::middleware(['auth:sanctum'])->group(
    function () {
        Route::get('user/byToken', [UserController::class, 'byToken']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user/{user}/messages', [UserController::class, 'ticketMessages']);


        Route::middleware(['tenant'])->group(
            function () {
                Route::apiResource('object1cs', Object1cController::class)->only(['index', 'show']);

                Route::get('contract', [Object1cController::class, 'getContract']);
                Route::get('check', [Object1cController::class, 'getBill']);
                Route::get('bills', [Object1cController::class, 'getBills']);
                Route::get('debts', [Object1cController::class, 'getDebts']);
                Route::get('counters', [Object1cController::class, 'getCounters']);
                Route::get('statistics', [Object1cController::class, 'getStatistics']);

            }
        );

        //
        Route::middleware(['admin'])->group(
            function () {
                Route::get('obj1c/all', [Object1cController::class, 'getAll']);
                Route::apiResource('objects1c', UserController::class)->only(['show', 'update', 'store', 'destroy']);


                Route::apiResource('user', UserController::class)
                    ->only(['index', 'show', 'update', 'store', 'destroy']);

                Route::get(
                    'role',
                    function () {
                        return Role::all();
                    }
                );

                Route::apiResource('realty', RealtyController::class)->only(['update', 'store', 'destroy']);
                Route::apiResource('realtyType', RealtyTypeController::class)->only(['update', 'store', 'destroy']);
                Route::apiResource('news', NewsController::class)->only(['update', 'store', 'destroy']);
                Route::apiResource('equipment', EquipmentController::class)->only(['update', 'store', 'destroy']);
                Route::apiResource('slide', SlideController::class)->only(['update', 'store', 'destroy']);
                Route::apiResource('contact', ContactController::class)->only(['update', 'store', 'destroy']);


                Route::delete('realty', [RealtyController::class, 'destroyMultiple']);
                Route::delete('realtyType', [RealtyTypeController::class, 'destroyMultiple']);
                Route::delete('news', [NewsController::class, 'destroyMultiple']);
                Route::delete('equipment', [EquipmentController::class, 'destroyMultiple']);
                Route::delete('slide', [SlideController::class, 'destroyMultiple']);
                Route::delete('contact', [ContactController::class, 'destroyMultiple']);
            }
        );
    }
);
