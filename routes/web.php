<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\PetugasReferenceController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/upload', [UploadController::class, 'index'])->name('uploads.index');
Route::post('/upload', [UploadController::class, 'store'])->name('uploads.store');
Route::delete('/upload/{upload}', [UploadController::class, 'destroy'])->name('uploads.destroy');

Route::post('/upload-referensi-petugas', [PetugasReferenceController::class, 'store'])->name('references.store');

Route::get('/export', [ExportController::class, 'index'])->name('export');
