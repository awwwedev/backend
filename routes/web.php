<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('make-storage', function () {
    return \Illuminate\Support\Facades\Artisan::call('storage:link');
});

Route::get('/admin/{any?}', function () {
    return view('admin-panel');
})->where('any', '.+');

Route::get('/lc/{any?}', function () {
    return view('lc');
})->where('any', '.+');

Route::get('/{any?}', function () {
    return view('index');
})->where('any', '.+');

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
