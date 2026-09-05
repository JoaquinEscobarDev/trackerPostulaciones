<?php

use App\Http\Controllers\PostulacionExportController;
use App\Http\Controllers\PostularDesdeEmpleoController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::redirect('dashboard', '/postulaciones')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('postulaciones', 'postulaciones')->name('postulaciones');
    Route::get('postulaciones/export/csv', [PostulacionExportController::class, 'csv'])->name('postulaciones.export.csv');
    Route::get('postulaciones/export/pdf', [PostulacionExportController::class, 'pdf'])->name('postulaciones.export.pdf');
    Route::get('empleos/postular', PostularDesdeEmpleoController::class)->name('empleos.postular');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
