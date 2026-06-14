<?php

// =========================================================
// Tambahkan route ini ke file routes/web.php lo yang sudah ada
// Di dalam middleware auth / group yang sesuai
// =========================================================

use App\Http\Controllers\EmailController;

Route::middleware(['auth'])->group(function () {

    // Broadcast ke semua mahasiswa
    Route::post('/email/broadcast', [EmailController::class, 'broadcast'])
        ->name('email.broadcast');

    // Kirim ke mahasiswa terpilih
    Route::post('/email/individual', [EmailController::class, 'individual'])
        ->name('email.individual');

});
