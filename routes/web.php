<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SuratPengajuanPelatihanController;
use App\Http\Controllers\IncomingLetterController;
use App\Http\Controllers\OutgoingLetterController;
use App\Http\Controllers\DispositionController;
use App\Http\Controllers\LetterGalleryController;
use App\Http\Controllers\ClassificationController;
use App\Http\Controllers\LetterStatusController;

Route::middleware(['auth'])->group(function () {
    Route::get('/', [PageController::class, 'index'])->name('home');

    Route::resource('user', UserController::class)
        ->except(['show', 'edit', 'create'])
        ->middleware(['role:admin,department_admin,division_admin']);

    // Profile routes
    Route::get('/profile', [PageController::class, 'profile'])->name('profile.show');
    Route::put('/profile', [PageController::class, 'profileUpdate'])->name('profile.update');
    Route::put('/profile/deactivate', [PageController::class, 'deactivate'])
        ->name('profile.deactivate')
        ->middleware(['role:staff']);

    Route::post('/profile/upload-signature', [PageController::class, 'uploadSignature'])->name('profile.upload.signature');
    Route::post('/profile/upload-paraf', [PageController::class, 'uploadParaf'])->name('profile.upload.paraf');

    // Settings
    Route::get('settings', [PageController::class, 'settings'])
        ->name('settings.show')
        ->middleware(['role:admin']);
    Route::put('settings', [PageController::class, 'settingsUpdate'])
        ->name('settings.update')
        ->middleware(['role:admin']);

    Route::delete('attachment', [PageController::class, 'removeAttachment'])->name('attachment.destroy');

    // Transactions
    Route::prefix('transaction')->as('transaction.')->group(function () {
        Route::resource('incoming', IncomingLetterController::class);
        Route::resource('outgoing', OutgoingLetterController::class);
        Route::resource('{letter}/disposition', DispositionController::class)->except(['show']);
    });

    // Agenda
    Route::prefix('agenda')->as('agenda.')->group(function () {
        Route::get('incoming', [IncomingLetterController::class, 'agenda'])->name('incoming');
        Route::get('incoming/print', [IncomingLetterController::class, 'print'])->name('incoming.print');
        Route::get('outgoing', [OutgoingLetterController::class, 'agenda'])->name('outgoing');
        Route::get('outgoing/print', [OutgoingLetterController::class, 'print'])->name('outgoing.print');
    });

    // Gallery
    Route::prefix('gallery')->as('gallery.')->group(function () {
        Route::get('incoming', [LetterGalleryController::class, 'incoming'])->name('incoming');
        Route::get('outgoing', [LetterGalleryController::class, 'outgoing'])->name('outgoing');
    });

    // References
    Route::prefix('reference')->as('reference.')->middleware(['role:admin'])->group(function () {
        Route::resource('classification', ClassificationController::class)->except(['show', 'create', 'edit']);
        Route::resource('status', LetterStatusController::class)->except(['show', 'create', 'edit']);
    });

    // Surat Pengajuan Pelatihan
    Route::prefix('training/suratpengajuan')->as('training.suratpengajuan.')->group(function () {
        Route::get('/', [SuratPengajuanPelatihanController::class, 'index'])->name('index');
        Route::get('/create', [SuratPengajuanPelatihanController::class, 'create'])->name('create');
        Route::post('/', [SuratPengajuanPelatihanController::class, 'store'])->name('store');
        Route::get('/{example}/preview', [SuratPengajuanPelatihanController::class, 'preview'])->name('preview');
    });
});
