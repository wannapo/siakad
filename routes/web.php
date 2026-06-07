<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ImportExportController;
use App\Http\Controllers\LogController;

// ── Auth ──
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Protected Routes ──
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Mahasiswa CRUD
    Route::resource('mahasiswa', MahasiswaController::class)->except(['show']);

    // Pencarian
    Route::get('/search', [SearchController::class, 'index'])->name('search.index');

    // Import / Export
    Route::get('/import-export',          [ImportExportController::class, 'index'])->name('import-export.index');
    Route::post('/import-export/import',  [ImportExportController::class, 'import'])->name('import-export.import');
    Route::get('/import-export/export/{format}', [ImportExportController::class, 'export'])->name('import-export.export');

    // Activity Log
    Route::get('/log',    [LogController::class, 'index'])->name('log.index');
    Route::delete('/log', [LogController::class, 'clear'])->name('log.clear');
});
