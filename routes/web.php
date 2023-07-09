<?php

use App\Http\Controllers\KmeansController;
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

Route::resource('k-means', KmeansController::class);
Route::prefix('k-means')->as('k-means.')->group(function () {
    Route::post('import', [KmeansController::class, 'import'])->name('import');
});
Route::get('cluster', [KmeansController::class, 'cluster'])->name('cluster');
Route::post('cluster', [KmeansController::class, 'processCluster'])->name('process');
