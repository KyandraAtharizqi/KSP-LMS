<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SuratPengajuanPelatihanController;
use App\Http\Controllers\SuratTugasPelatihanController;
use App\Http\Controllers\DaftarHadirPelatihanController;
use App\Http\Controllers\IncomingLetterController;
use App\Http\Controllers\OutgoingLetterController;
use App\Http\Controllers\DispositionController;
use App\Http\Controllers\LetterGalleryController;
use App\Http\Controllers\ClassificationController;
use App\Http\Controllers\LetterStatusController;
use App\Http\Controllers\ProfileController;

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Global Single Routes (non-grouped)
    |--------------------------------------------------------------------------
    */

    // Surat Pengajuan Pelatihan download
    Route::get('/surat-pengajuan/{id}/download', [SuratPengajuanPelatihanController::class, 'downloadPDF'])
        ->name('surat.pengajuan.download');

    // Surat Tugas Pelatihan PDF download
    Route::get('/training/surattugas/download/{id}', [SuratTugasPelatihanController::class, 'download'])
        ->name('training.surattugas.download');

    // Home
    Route::get('/', [PageController::class, 'index'])->name('home');

    /*
    |--------------------------------------------------------------------------
    | User Management
    |--------------------------------------------------------------------------
    */
    Route::resource('user', UserController::class)
        ->except(['show', 'edit', 'create'])
        ->middleware(['role:admin,department_admin,division_admin']);

    // CSV Import Route for User
    Route::post('/users/import-csv', [UserController::class, 'importCsv'])
        ->name('users.import.csv')
        ->middleware(['role:admin,department_admin,division_admin']);

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [PageController::class, 'profile'])->name('profile.show');
    Route::post('/profile', [PageController::class, 'profileUpdate'])->name('profile.update');
    Route::post('/profile/deactivate', [PageController::class, 'deactivate'])
        ->name('profile.deactivate')
        ->middleware(['role:staff']);

    Route::post('/profile/upload-signature', [PageController::class, 'uploadSignature'])->name('profile.upload.signature');
    Route::post('/profile/upload-paraf', [PageController::class, 'uploadParaf'])->name('profile.upload.paraf');

    /*
    |--------------------------------------------------------------------------
    | Settings (Admin)
    |--------------------------------------------------------------------------
    */
    Route::get('settings', [PageController::class, 'settings'])
        ->name('settings.show')
        ->middleware(['role:admin']);
    Route::put('settings', [PageController::class, 'settingsUpdate'])
        ->name('settings.update')
        ->middleware(['role:admin']);

    Route::delete('attachment', [PageController::class, 'removeAttachment'])->name('attachment.destroy');

    /*
    |--------------------------------------------------------------------------
    | Transaction Letters
    |--------------------------------------------------------------------------
    */
    Route::prefix('transaction')->as('transaction.')->group(function () {
        Route::resource('incoming', IncomingLetterController::class);
        Route::resource('outgoing', OutgoingLetterController::class);
        Route::resource('{letter}/disposition', DispositionController::class)->except(['show']);
    });

    /*
    |--------------------------------------------------------------------------
    | Knowledge (printable letter views)
    |--------------------------------------------------------------------------
    */
    Route::prefix('knowledge')->as('knowledge.')->group(function () {
        Route::get('incoming', [IncomingLetterController::class, 'knowledge'])->name('incoming');
        Route::get('incoming/print', [IncomingLetterController::class, 'print'])->name('incoming.print');
        Route::get('outgoing', [OutgoingLetterController::class, 'knowledge'])->name('outgoing');
        Route::get('outgoing/print', [OutgoingLetterController::class, 'print'])->name('outgoing.print');
    });

    /*
    |--------------------------------------------------------------------------
    | Gallery
    |--------------------------------------------------------------------------
    */
    Route::prefix('gallery')->as('gallery.')->group(function () {
        Route::get('incoming', [LetterGalleryController::class, 'incoming'])->name('incoming');
        Route::get('outgoing', [LetterGalleryController::class, 'outgoing'])->name('outgoing');
    });

    /*
    |--------------------------------------------------------------------------
    | Reference Data (Admin)
    |--------------------------------------------------------------------------
    */
    Route::prefix('reference')->as('reference.')->middleware(['role:admin'])->group(function () {
        Route::resource('classification', ClassificationController::class)->except(['show', 'create', 'edit']);
        Route::resource('status', LetterStatusController::class)->except(['show', 'create', 'edit']);
    });

    /*
    |--------------------------------------------------------------------------
    | Surat Pengajuan Pelatihan
    |--------------------------------------------------------------------------
    */
    Route::prefix('training/suratpengajuan')->as('training.suratpengajuan.')->group(function () {
        Route::get('/', [SuratPengajuanPelatihanController::class, 'index'])->name('index');
        Route::get('/create', [SuratPengajuanPelatihanController::class, 'create'])->name('create');
        Route::post('/', [SuratPengajuanPelatihanController::class, 'store'])->name('store');

        Route::get('/{id}/preview', [SuratPengajuanPelatihanController::class, 'preview'])->name('preview');
        Route::get('/{id}/edit', [SuratPengajuanPelatihanController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SuratPengajuanPelatihanController::class, 'update'])->name('update');

        Route::post('/{id}/approval/{approval}/approve', [SuratPengajuanPelatihanController::class, 'approve'])->name('approve');
        Route::post('/{id}/approval/{approval}/reject', [SuratPengajuanPelatihanController::class, 'reject'])->name('reject');
    });

    /*
    |--------------------------------------------------------------------------
    | Surat Tugas Pelatihan
    |--------------------------------------------------------------------------
    */
    Route::prefix('training/surattugas')->as('training.surattugas.')->group(function () {
        Route::get('/', [SuratTugasPelatihanController::class, 'index'])->name('index');
        Route::get('/{id}/preview', [SuratTugasPelatihanController::class, 'preview'])->name('preview');

        // Assign signers/parafs
        Route::get('/{id}/assign', [SuratTugasPelatihanController::class, 'assignView'])->name('assign.view');
        Route::post('/assign', [SuratTugasPelatihanController::class, 'assignSave'])->name('assign.submit');

        // Approve / reject
        Route::get('/{id}/approve/{approval}', [SuratTugasPelatihanController::class, 'approveView'])->name('approve.view');
        Route::post('/{id}/approve/{approval}', [SuratTugasPelatihanController::class, 'approve'])->name('approve');
        Route::get('/{id}/reject/{approval}', [SuratTugasPelatihanController::class, 'rejectView'])->name('reject.view');
        Route::post('/{id}/reject/{approval}', [SuratTugasPelatihanController::class, 'reject'])->name('reject');
    });

    /*
    |--------------------------------------------------------------------------
    | Daftar Hadir Pelatihan
    |--------------------------------------------------------------------------
    */
    Route::prefix('training/daftar-hadir-pelatihan')->as('training.daftarhadirpelatihan.')->group(function () {
        // Index: list pelatihans with approved Surat Tugas
        Route::get('/', [DaftarHadirPelatihanController::class, 'index'])->name('index');

        // Show: training-level overview
        Route::get('/{pelatihan}', [DaftarHadirPelatihanController::class, 'show'])
            ->whereNumber('pelatihan')
            ->name('show');

        // Day-level routes
        Route::get('/{pelatihan}/day/{date}', [DaftarHadirPelatihanController::class, 'day'])
            ->where(['pelatihan' => '[0-9]+', 'date' => '[0-9]{4}-[0-9]{2}-[0-9]{2}'])
            ->name('day');

        Route::post('/{pelatihan}/day/{date}/import', [DaftarHadirPelatihanController::class, 'import'])
            ->where(['pelatihan' => '[0-9]+', 'date' => '[0-9]{4}-[0-9]{2}-[0-9]{2}'])
            ->name('import');

        Route::get('/{pelatihan}/day/{date}/export', [DaftarHadirPelatihanController::class, 'export'])
            ->where(['pelatihan' => '[0-9]+', 'date' => '[0-9]{4}-[0-9]{2}-[0-9]{2}'])
            ->name('export');

        Route::post('/{pelatihan}/day/{date}/save', [DaftarHadirPelatihanController::class, 'save'])
            ->where(['pelatihan' => '[0-9]+', 'date' => '[0-9]{4}-[0-9]{2}-[0-9]{2}'])
            ->name('save');

        Route::post('/{pelatihan}/day/{date}/complete', [DaftarHadirPelatihanController::class, 'markComplete'])
            ->where(['pelatihan' => '[0-9]+', 'date' => '[0-9]{4}-[0-9]{2}-[0-9]{2}'])
            ->name('complete');
    });

});
