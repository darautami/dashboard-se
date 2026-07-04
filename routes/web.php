<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\PetugasReferenceController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

// Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard (viewer & admin)
Route::middleware('auth.dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/export', [ExportController::class, 'index'])->name('export');
});

// Upload (admin)
Route::middleware(['auth.dashboard', 'auth.admin'])->group(function () {
    Route::get('/upload', [UploadController::class, 'index'])->name('uploads.index');
    Route::post('/upload', [UploadController::class, 'store'])->name('uploads.store');
    Route::delete('/upload/{upload}', [UploadController::class, 'destroy'])->name('uploads.destroy');
    Route::post('/upload-referensi-petugas', [PetugasReferenceController::class, 'store'])->name('references.store');
});