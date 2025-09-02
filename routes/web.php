<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\PageController;
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
use App\Http\Controllers\DaftarHadirPelatihanPresenterController;
use App\Http\Controllers\Knowledge\NotaDinasController;
use App\Http\Controllers\DaftarHadirKnowledgeController;
use App\Http\Controllers\PengajuanKnowledgeController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\UserController;

/* 👇 Evaluation controllers */
use App\Http\Controllers\EvaluasiLevel1Controller;
use App\Http\Controllers\EvaluasiLevel3PesertaController;
use App\Http\Controllers\EvaluasiLevel3AtasanController;

use App\Http\Controllers\TrainingEvaluation2Controller;
use App\Http\Controllers\TrainingEvaluationAtasanController;
use App\Http\Controllers\TrainingEvaluationRekapController;


Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Global Single Routes (non-grouped)
    |--------------------------------------------------------------------------
    */

    // Surat Pengajuan Pelatihan PDF download
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
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/deactivate', [PageController::class, 'deactivate'])
        ->name('profile.deactivate')
        ->middleware(['role:staff']);

    Route::post('/profile/upload-signature', [ProfileController::class, 'uploadSignature'])->name('profile.upload.signature');
    Route::post('/profile/upload-paraf', [ProfileController::class, 'uploadParaf'])->name('profile.upload.paraf');

    /*
    |--------------------------------------------------------------------------
    | Notifikasi
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth'])->group(function () {
        Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
        Route::post('/notifikasi/{id}/read', [NotifikasiController::class, 'markAsRead'])->name('notifikasi.read');
        Route::post('/notifikasi/read-all', [NotifikasiController::class, 'markAllAsRead'])->name('notifikasi.readAll');
        Route::post('/notifikasi/{id}/delete', [NotifikasiController::class, 'delete'])->name('notifikasi.delete');

        // Route untuk testing notifikasi (sementara aja, matiin kalau production)
        Route::get('/test-notification', function() {
            $notifikasiController = new NotifikasiController();
            return $notifikasiController->sendNotification(
                auth()->id(),
                'knowledge_sharing',
                [
                    'judul' => 'Test Notifikasi',
                    'pemateri' => 'Test Pemateri',
                    'tanggal_mulai' => now(),
                    'tanggal_selesai' => now()->addHour(),
                    'id' => 1
                ]
            );
        })->name('notification.test');
    });


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
        Route::get('/',        [SuratPengajuanPelatihanController::class, 'index'])->name('index');
        Route::get('/create',  [SuratPengajuanPelatihanController::class, 'create'])->name('create');
        Route::post('/',       [SuratPengajuanPelatihanController::class, 'store'])->name('store');

        Route::get('/{id}/preview', [SuratPengajuanPelatihanController::class, 'preview'])->name('preview');
        Route::get('/{id}/edit',    [SuratPengajuanPelatihanController::class, 'edit'])->name('edit');
        Route::put('/{id}',         [SuratPengajuanPelatihanController::class, 'update'])->name('update');

        Route::post('/{id}/approval/{approval}/approve', [SuratPengajuanPelatihanController::class, 'approve'])->name('approve');
        Route::post('/{id}/approval/{approval}/reject',  [SuratPengajuanPelatihanController::class, 'reject'])->name('reject');
    });


    /*
    |--------------------------------------------------------------------------
    | Surat Tugas Pelatihan
    |--------------------------------------------------------------------------
    */
    Route::prefix('training/surattugas')->as('training.surattugas.')->group(function () {
        Route::get('/',          [SuratTugasPelatihanController::class, 'index'])->name('index');
        Route::get('/{id}/preview', [SuratTugasPelatihanController::class, 'preview'])->name('preview');

        // Assign signers/parafs
        Route::get('/{id}/assign', [SuratTugasPelatihanController::class, 'assignView'])->name('assign.view');
        Route::post('/assign',     [SuratTugasPelatihanController::class, 'assignSave'])->name('assign.submit');

        // Approve / reject
        Route::get('/{id}/approve/{approval}', [SuratTugasPelatihanController::class, 'approveView'])->name('approve.view');
        Route::post('/{id}/approve/{approval}',[SuratTugasPelatihanController::class, 'approve'])->name('approve');
        Route::get('/{id}/reject/{approval}',  [SuratTugasPelatihanController::class, 'rejectView'])->name('reject.view');
        Route::post('/{id}/reject/{approval}', [SuratTugasPelatihanController::class, 'reject'])->name('reject');
    });


    /*
    |--------------------------------------------------------------------------
    | Daftar Hadir Pelatihan Routes 
    |--------------------------------------------------------------------------
    */
    Route::prefix('training/daftar-hadir-pelatihan')->as('training.daftarhadirpelatihan.')->group(function () {
        // Index: List pelatihans with approved Surat Tugas
        Route::get('/', [DaftarHadirPelatihanController::class, 'index'])->name('index');

        // Show: training-level overview (all days)
        Route::get('/{pelatihan}', [DaftarHadirPelatihanController::class, 'show'])
            ->whereNumber('pelatihan')
            ->name('show');

        // View daftar hadir for a specific day
        Route::get('/{pelatihan}/day/{date}', [DaftarHadirPelatihanController::class, 'day'])
            ->where([
                'pelatihan' => '[0-9]+',
                'date' => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
            ])
            ->name('day');

        Route::post('/{pelatihan}/day/{date}/import', [DaftarHadirPelatihanController::class, 'import'])->name('import');
        Route::get('/{pelatihan}/day/{date}/export', [DaftarHadirPelatihanController::class, 'export'])->name('export');
        Route::post('/{pelatihan}/day/{date}/save', [DaftarHadirPelatihanController::class, 'save'])->name('save');
        Route::post('/{pelatihan}/day/{date}/complete', [DaftarHadirPelatihanController::class, 'markComplete'])->name('complete');


        Route::get('/{pelatihan}/preview/{date}', [DaftarHadirPelatihanController::class, 'preview'])
            ->name('preview');
        Route::get('/{pelatihan}/pdf/{date}', [DaftarHadirPelatihanController::class, 'pdf'])
            ->name('download');

        /*
        |--------------------------------------------------------------------------
        | Presenter Assignment (Per Pelatihan, Across Days)
        |--------------------------------------------------------------------------
        */
        Route::get('/{pelatihan}/presenter', [DaftarHadirPelatihanPresenterController::class, 'index'])->name('presenter.index');
        Route::post('/{pelatihan}/presenter/{date}', [DaftarHadirPelatihanPresenterController::class, 'update'])->name('presenter.update');
        Route::post('/presenter/store-inline', [DaftarHadirPelatihanPresenterController::class, 'storeInlinePresenter'])->name('presenter.storeInline');
        Route::delete('/{pelatihan}/presenter', [DaftarHadirPelatihanPresenterController::class, 'destroy'])->name('presenter.destroy');
        Route::post('/presenter/store-external', [DaftarHadirPelatihanPresenterController::class, 'storeExternalPresenter']) ->name('presenter.storeExternal');
        Route::post('/training/{pelatihan}/presenter/submit/{date}', [DaftarHadirPelatihanPresenterController::class, 'submitFinal'])
        ->name('presenter.submit');

    });


    /*
        |--------------------------------------------------------------------------
        | Evaluation (Training Follow-up)
        | Placeholder routes – implement controllers as needed.
        |--------------------------------------------------------------------------
        */

        Route::prefix('training/evaluasi-level-1')->as('training.evaluasilevel1.')->group(function () {
            Route::get('/', [EvaluasiLevel1Controller::class, 'index'])->name('index');
            Route::get('/{pelatihan}/create', [EvaluasiLevel1Controller::class, 'create'])->name('create');
            Route::post('/{pelatihan}', [EvaluasiLevel1Controller::class, 'store'])->name('store');
            Route::get('/{pelatihan}/show', [EvaluasiLevel1Controller::class, 'show'])->name('show');
            Route::get('/{pelatihan}/pdf', [EvaluasiLevel1Controller::class, 'pdfView'])->name('pdf');

            // ✅ Only this part changes
            Route::put('/{evaluasi}/update-superior', [EvaluasiLevel1Controller::class, 'updateSuperior'])
                ->name('updateSuperior');
        });

        // Evaluation 2 (Peserta) – e.g., post-training learning assessment
        Route::prefix('training/evaluation2')->as('training.evaluation2.')->group(function () {
            Route::get('/', [TrainingEvaluation2Controller::class, 'index'])->name('index');
        });

        Route::prefix('training/evaluasi-level-3/peserta')->name('evaluasi-level-3.peserta.')->group(function () {
            Route::get('/', [EvaluasiLevel3PesertaController::class, 'index'])->name('index');
            Route::get('/create/{pelatihan}', [EvaluasiLevel3PesertaController::class, 'create'])->name('create');
            Route::post('/store/{pelatihan}', [EvaluasiLevel3PesertaController::class, 'store'])->name('store');
            Route::get('/preview/{pelatihan}', [EvaluasiLevel3PesertaController::class, 'preview'])->name('preview');
            Route::get('/edit/{pelatihan}', [EvaluasiLevel3PesertaController::class, 'edit'])->name('edit');
            Route::put('/update/{pelatihan}', [EvaluasiLevel3PesertaController::class, 'update'])->name('update');
            Route::get('/pdf/{pelatihan}', [EvaluasiLevel3PesertaController::class, 'pdfView'])->name('pdf');



        });



        Route::prefix('training/evaluasi-level-3/atasan')
            ->name('evaluasi-level-3.atasan.')
            ->group(function () {

                // Index - list all peserta evaluations for this supervisor
                Route::get('/', [EvaluasiLevel3AtasanController::class, 'index'])->name('index');

                // Approval page - preview a peserta evaluation and approve/rollback
                Route::get('/approval/{evaluasi}', [EvaluasiLevel3AtasanController::class, 'approval'])->name('approval');
                Route::post('/approval/{evaluasi}', [EvaluasiLevel3AtasanController::class, 'submitApproval'])->name('submitApproval');

                // Create evaluation by supervisor
                Route::get('/create/{evaluasi}', [EvaluasiLevel3AtasanController::class, 'create'])->name('create');
                Route::post('/store/{evaluasi}', [EvaluasiLevel3AtasanController::class, 'store'])->name('store');
            });
                

        // Evaluation Atasan (manager follow‑up on behavior/transfer)
        Route::prefix('training/evaluation/atasan')->as('training.evaluation.atasan.')->group(function () {
            Route::get('/', [TrainingEvaluationAtasanController::class, 'index'])->name('index');
        });

        // Rekapitulasi Jam (hours accumulation / summary across trainings)
        Route::prefix('training/evaluation/rekap')->as('training.evaluation.rekap.')->group(function () {
            Route::get('/', [TrainingEvaluationRekapController::class, 'index'])->name('index');
        });

    
    /*
    |--------------------------------------------------------------------------
    | Pengajuan Knowledge Sharing
    |--------------------------------------------------------------------------
    */
    Route::prefix('knowledge')->name('knowledge.')->group(function () {
        Route::prefix('pengajuan')->name('pengajuan.')->group(function () {
            // Approval-specific routes first
            Route::patch('/{id}/approve', [PengajuanKnowledgeController::class, 'approve'])->name('approve');
            Route::patch('/{id}/reject', [PengajuanKnowledgeController::class, 'reject'])->name('reject');

            // Then the resource-like routes
            Route::get('/', [PengajuanKnowledgeController::class, 'index'])->name('index');
            Route::get('/create', [PengajuanKnowledgeController::class, 'create'])->name('create');
            Route::post('/', [PengajuanKnowledgeController::class, 'store'])->name('store');
            Route::get('/{id}', [PengajuanKnowledgeController::class, 'preview'])->name('preview');
            Route::get('/{id}/edit', [PengajuanKnowledgeController::class, 'edit'])->name('edit');
            Route::patch('/{id}', [PengajuanKnowledgeController::class, 'update'])->name('update');
            Route::delete('/{id}', [PengajuanKnowledgeController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/download', [PengajuanKnowledgeController::class, 'download'])->name('download');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Undangan Knowledge SHaring
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'knowledge', 'middleware' => ['auth']], function() {
        Route::get('undangan', [App\Http\Controllers\SuratUndanganController::class, 'index'])->name('knowledge.undangan.index');
    });

    /*
    |--------------------------------------------------------------------------
    | Nota Dinas
    |--------------------------------------------------------------------------
    */
    Route::prefix('knowledge')->name('knowledge.')->group(function () {
        Route::prefix('notadinas')->name('notadinas.')->group(function () {
            Route::get('/', [NotaDinasController::class, 'index'])->name('index');          // GET /knowledge/notadinas
            Route::get('/create', [NotaDinasController::class, 'create'])->name('create');  // GET /knowledge/notadinas/create
            Route::post('/', [NotaDinasController::class, 'store'])->name('store');         // POST /knowledge/notadinas
            Route::get('/{id}', [NotaDinasController::class, 'show'])->name('show');        // GET /knowledge/notadinas/{id}
            Route::get('/{id}/edit', [NotaDinasController::class, 'edit'])->name('edit');   // GET /knowledge/notadinas/{id}/edit
            Route::put('/{id}', [NotaDinasController::class, 'update'])->name('update');    // PUT /knowledge/notadinas/{id}
            Route::delete('/{id}', [NotaDinasController::class, 'destroy'])->name('destroy'); // DELETE /knowledge/notadinas/{id}
            Route::get('/{id}/download', [NotaDinasController::class, 'download'])->name('download');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Daftar Hadir Knowledge Sharing
    |--------------------------------------------------------------------------
    */
    Route::prefix('knowledge')->name('knowledge.')->group(function () {
        Route::resource('daftarhadir', DaftarHadirKnowledgeController::class);
    });

});
