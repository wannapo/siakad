<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MahasiswaController;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — SIAKAD (Sistem Informasi Akademik)
|--------------------------------------------------------------------------
*/

// ---- Auth Routes ----
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ---- Protected Routes (Harus Login) ----
Route::middleware('auth')->group(function () {

    // Dashboard / Redirect root
    Route::get('/', fn() => redirect()->route('mahasiswa.index'));

    // ---- MAIN MAHASISWA ROUTES (Urutan Krusial) ----
    Route::get('/mahasiswa',            [MahasiswaController::class, 'index'])->name('mahasiswa.index');
    Route::get('/mahasiswa/create',     [MahasiswaController::class, 'create'])->name('mahasiswa.create');
    Route::post('/mahasiswa',           [MahasiswaController::class, 'store'])->name('mahasiswa.store');
    
    // Taruh Export dan Import di sini (Sebelum rute dinamis {mahasiswa})
    Route::get('/mahasiswa/export',     [MahasiswaController::class, 'export'])->name('mahasiswa.export');
    Route::get('/mahasiswa/import/form',[MahasiswaController::class, 'importForm'])->name('mahasiswa.import.form');
    Route::post('/mahasiswa/import',    [MahasiswaController::class, 'import'])->name('mahasiswa.import');

    // Rute detail parameter dinamis ditaruh paling bawah agar tidak bentrok
    Route::get('/mahasiswa/{mahasiswa}',        [MahasiswaController::class, 'show'])->name('mahasiswa.show');
    Route::get('/mahasiswa/{mahasiswa}/edit',   [MahasiswaController::class, 'edit'])->name('mahasiswa.edit');
    Route::put('/mahasiswa/{mahasiswa}',        [MahasiswaController::class, 'update'])->name('mahasiswa.update');
    Route::delete('/mahasiswa/{mahasiswa}',     [MahasiswaController::class, 'destroy'])->name('mahasiswa.destroy');

    // ---- Activity Log ----
    Route::get('/logs', function () {
        $logs = ActivityLog::with('user')->latest()->paginate(20);
        return view('logs.index', compact('logs'));
    })->name('logs.index');

    Route::post('/logs/{log}/comment', [MahasiswaController::class, 'addComment'])->name('logs.comment');
});