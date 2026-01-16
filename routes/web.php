<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PengajuanController;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {

    /* =========================
     * DASHBOARD
     * ========================= */
    Route::get('/dashboard', [PengajuanController::class, 'dashboard'])
        ->name('dashboard');

    Route::get('/information', fn () => view('pengajuan.info'))
        ->name('information');

    /* =========================
     * PENGAJUAN
     * ========================= */

    Route::get('/pengajuans', [PengajuanController::class, 'index'])
        ->name('pengajuans.index');

    Route::get('/pengajuans/create', [PengajuanController::class, 'create'])
        ->name('pengajuans.create');

    Route::post('/pengajuans', [PengajuanController::class, 'store'])
        ->name('pengajuans.store');

    Route::get('/pengajuans/{pengajuan}', [PengajuanController::class, 'show'])
        ->whereNumber('pengajuan')
        ->name('pengajuans.show');

    /**
     * 🔥 INI KUNCI UTAMA
     * EDIT HARUS MASUK CONTROLLER
     * BIAR ROLE IAG DIBELIHKAN KE FORM IAG
     */
    Route::get('/pengajuans/{pengajuan}/edit', [PengajuanController::class, 'edit'])
        ->whereNumber('pengajuan')
        ->name('pengajuans.edit');

    Route::put('/pengajuans/{pengajuan}', [PengajuanController::class, 'update'])
        ->whereNumber('pengajuan')
        ->name('pengajuans.update');

    Route::delete('/pengajuans/{pengajuan}', [PengajuanController::class, 'destroy'])
        ->whereNumber('pengajuan')
        ->name('pengajuans.destroy');

    /* =========================
     * APPROVAL (GH CRV & GH IAG)
     * ========================= */

    Route::post('/pengajuans/{pengajuan}/approve', [PengajuanController::class, 'approve'])
        ->whereNumber('pengajuan')
        ->name('pengajuans.approve');

    Route::post('/pengajuans/{pengajuan}/reject', [PengajuanController::class, 'reject'])
        ->whereNumber('pengajuan')
        ->name('pengajuans.reject');

    /* =========================
     * FORM IAG (KHUSUS ROLE IAG)
     * ========================= */

    Route::get('/pengajuans/{pengajuan}/iag', [PengajuanController::class, 'iagEdit'])
        ->whereNumber('pengajuan')
        ->name('pengajuans.iag.edit');

    Route::put('/pengajuans/{pengajuan}/iag', [PengajuanController::class, 'iagUpdate'])
        ->whereNumber('pengajuan')
        ->name('pengajuans.iag.update');

    /* =========================
     * DOWNLOAD
     * ========================= */

    Route::get('/pengajuans/{pengajuan}/download', [PengajuanController::class, 'download'])
        ->whereNumber('pengajuan')
        ->name('pengajuans.download');

    Route::post('/pengajuans-download-selected', [PengajuanController::class, 'downloadSelected'])
        ->name('pengajuans.downloadSelected');
});

require __DIR__.'/auth.php';
