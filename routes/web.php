<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PengajuanController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {

    // ✅ Dashboard beneran
    Route::get('/dashboard', [PengajuanController::class, 'dashboard'])->name('dashboard');

    // ✅ Information (FAQ)
    Route::get('/information', function () {
        return view('pengajuan.info'); // pastikan file-nya resources/views/pengajuan/info.blade.php
    })->name('information');

    // CRUD Pengajuan
    Route::get('/pengajuans', [PengajuanController::class, 'index'])->name('pengajuans.index');
    Route::get('/pengajuans/create', [PengajuanController::class, 'create'])->name('pengajuans.create');
    Route::post('/pengajuans', [PengajuanController::class, 'store'])->name('pengajuans.store');

    Route::get('/pengajuans/{pengajuan}', [PengajuanController::class, 'show'])->name('pengajuans.show');
    Route::get('/pengajuans/{pengajuan}/edit', [PengajuanController::class, 'edit'])->name('pengajuans.edit');
    Route::put('/pengajuans/{pengajuan}', [PengajuanController::class, 'update'])->name('pengajuans.update');
    Route::delete('/pengajuans/{pengajuan}', [PengajuanController::class, 'destroy'])->name('pengajuans.destroy');

    // Approve / Reject
    Route::post('/pengajuans/{pengajuan}/approve', [PengajuanController::class, 'approve'])->name('pengajuans.approve');
    Route::post('/pengajuans/{pengajuan}/reject', [PengajuanController::class, 'reject'])->name('pengajuans.reject');

    // ✅ Download single (ini yang error kamu)
    Route::get('/pengajuans/{pengajuan}/download', [PengajuanController::class, 'download'])
        ->name('pengajuans.download');

    // Download selected (kalau kamu memang pakai)
    Route::post('/pengajuans/download-selected', [PengajuanController::class, 'downloadSelected'])
        ->name('pengajuans.downloadSelected');

    // IAG form
    Route::get('/pengajuans/{pengajuan}/iag', [PengajuanController::class, 'iagEdit'])->name('pengajuans.iag.edit');
    Route::put('/pengajuans/{pengajuan}/iag', [PengajuanController::class, 'iagUpdate'])->name('pengajuans.iag.update');
});

require __DIR__ . '/auth.php';