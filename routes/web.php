<?php

use App\Http\Controllers\AHPController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KmeansController;
use App\Http\Controllers\LoginController;
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

Route::get('/', function () {
    return redirect()->route('login');
});
Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::resource('home', HomeController::class);

Route::group(['middleware' => 'auth'], function () {
    Route::resource('k-means', KmeansController::class);
    Route::prefix('k-means')->as('k-means.')->group(function () {
        Route::post('import', [KmeansController::class, 'import'])->name('import');
    });
    Route::get('cluster', [KmeansController::class, 'cluster'])->name('cluster');
    Route::post('cluster', [KmeansController::class, 'processCluster'])->name('process');

    Route::prefix('ahps')->as('ahps.')->group(function () {
        Route::get('weight-alternatif', [AHPController::class, 'weightAlternatif'])->name('weight-alternatif');
        Route::get('final-calculate', [AHPController::class, 'finalCalculate'])->name('final-calculate');
        Route::post('reset', [AHPController::class, 'resetWeightCriteria'])->name('reset');
        Route::post('reset-alternatif', [AHPController::class, 'resetWeightAlternatif'])->name('reset-weight-alternatif');
        Route::post('store-weight-alternatif', [AHPController::class, 'storeWeightAlternatif'])->name('store-weight-alternatif');
        Route::get('data-alternatif', [AHPController::class, 'getDataAlternatif'])->name('data-alternatif');
    });
    Route::resource('ahps', AHPController::class);
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
});
