@extends('layout.main')

@section('content')
<div class="container">
    <h4 class="mb-4">Form Evaluasi Level 3 - Peserta</h4>

    {{-- Pelatihan Info --}}
    <div class="card mb-4">
        <div class="card-body">
            <strong>Kode:</strong> {{ $pelatihan->kode_pelatihan }} <br>
            <strong>Judul:</strong> {{ $pelatihan->judul }} <br>
            <strong>Penyelenggara:</strong> {{ $pelatihan->penyelenggara }}
        </div>
    </div>

    <form action="{{ route('evaluasi-level-3.peserta.store', $pelatihan->id) }}" method="POST">
        @csrf

        {{-- Action Plan Section --}}
        <h4 class="mb-3">Action Plan</h4>
        <div class="mb-3">
            <small>
                <strong>Keterangan:</strong><br>
                *) Action Plan: rencana langkah-langkah dalam mengaplikasikan materi pelatihan<br>
                **) Frekuensi: 0 (tidak pernah), 1 (sekali), 2 (sering), 3 (selalu)<br>
                ***) Hasil: 1 (tidak berhasil), 2 (cukup berhasil), 3 (berhasil), 4 (sangat berhasil)
            </small>
        </div>
        <table class="table table-bordered" id="action-plan-table">
            <thead>
                <tr>
                    <th>Action Plan</th>
                    <th>Diaplikasikan</th>
                    <th>Frekuensi (0-3)</th>
                    <th>Hasil (1-4)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr data-row-index="0">
                    <td><input type="text" name="action_plan[0]" class="form-control" required></td>
                    <td class="text-center">
                        <input type="hidden" name="diaplikasikan[0]" value="0">
                        <input type="checkbox" name="diaplikasikan[0]" value="1">
                    </td>
                    <td><input type="number" name="frekuensi[0]" class="form-control" min="0" max="3" value="0" required></td>
                    <td><input type="number" name="hasil[0]" class="form-control" min="1" max="4" value="1" required></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-primary btn-sm mb-3" id="add-action-plan">Tambah Baris</button>

        {{-- Feedback Section --}}
        <h4 class="mb-3">Feedback</h4>
        <h5 class="mb-3">Apa yang bisa dilakukan oleh HC Department untuk membantu anda mengaplikasikan hasil training</h5>
        <table class="table table-bordered" id="feedback-table">
            <thead>
                <tr>
                    <th>Saya Sudah Mampu</th>
                    <th>Butuh Bantuan untuk Cara Mengaplikasikan</th>
                    <th>Materi Tidak Bisa Diaplikasikan Karena</th>
                    <th>Memberikan Informasi Mengenai</th>
                    <th>Lain-lain</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center"><input type="checkbox" name="telah_mampu" value="1"></td>
                    <td class="text-center"><input type="checkbox" name="membantu_mengaplikasikan" value="1"></td>
                    <td><input type="text" name="tidak_diaplikasikan_karena" class="form-control"></td>
                    <td><input type="text" name="memberikan_informasi_mengenai" class="form-control"></td>
                    <td><input type="text" name="lain_lain" class="form-control"></td>
                </tr>
            </tbody>
        </table>

        {{-- General Section --}}
        <h4 class="mb-3">General</h4>
        <div class="mb-3">
            <label class="form-label">Setelah pelatihan ini saya mampu untuk...</label>
            <textarea name="manfaat_pelatihan" class="form-control" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Secara keseluruhan, saya menilai training tersebut dapat meningkatkan kinerja...</label>
            <input type="number" name="kinerja" class="form-control" min="0" max="2">
            <small class="form-text text-muted">
                0 = tidak sama sekali, 1 = cukup membantu, 2 = sangat membantu
            </small>
        </div>
        <div class="mb-3">
            <label class="form-label">Saran dan Masukan Penyelenggara Training Sejenis</label>
            <textarea name="saran" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('evaluasi-level-3.peserta.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>

    </form>
</div>

<!-- INLINE SCRIPT FOR DEBUGGING -->
<script>
console.log('=== SCRIPT TAG LOADED ===');

document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DEBUG: DOM Content Loaded ===');
    
    let actionPlanRowCounter = 1; // Start from 1 since we already have row 0

    // Check if button exists
    const addButton = document.getElementById('add-action-plan');
    console.log('DEBUG: Add button found:', addButton);
    console.log('DEBUG: Add button HTML:', addButton ? addButton.outerHTML : 'BUTTON NOT FOUND');
    
    // Check if table exists
    const table = document.getElementById('action-plan-table');
    const tableBody = document.querySelector('#action-plan-table tbody');
    console.log('DEBUG: Table found:', table);
    console.log('DEBUG: Table body found:', tableBody);
    console.log('DEBUG: Current rows count:', tableBody ? tableBody.rows.length : 'TABLE BODY NOT FOUND');

    if (!addButton) {
        console.error('ERROR: Add button not found!');
        return;
    }

    if (!tableBody) {
        console.error('ERROR: Table body not found!');
        return;
    }

    // Add new action plan row
    addButton.addEventListener('click', function(event) {
        console.log('=== DEBUG: ADD BUTTON CLICKED ===');
        console.log('DEBUG: Event object:', event);
        console.log('DEBUG: Button that was clicked:', this);
        console.log('DEBUG: Current counter:', actionPlanRowCounter);
        
        // Prevent any default behavior
        event.preventDefault();
        event.stopPropagation();
        
        console.log('DEBUG: About to add new row...');
        
        // Create new row HTML
        const newRowHTML = `
            <tr data-row-index="${actionPlanRowCounter}">
                <td><input type="text" name="action_plan[${actionPlanRowCounter}]" class="form-control" required></td>
                <td class="text-center">
                    <input type="hidden" name="diaplikasikan[${actionPlanRowCounter}]" value="0">
                    <input type="checkbox" name="diaplikasikan[${actionPlanRowCounter}]" value="1">
                </td>
                <td><input type="number" name="frekuensi[${actionPlanRowCounter}]" class="form-control" min="0" max="3" value="0" required></td>
                <td><input type="number" name="hasil[${actionPlanRowCounter}]" class="form-control" min="1" max="4" value="1" required></td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button>
                </td>
            </tr>
        `;
        
        console.log('DEBUG: New row HTML:', newRowHTML);
        
        // Insert the new row
        console.log('DEBUG: Before insert - rows count:', tableBody.rows.length);
        tableBody.insertAdjacentHTML('beforeend', newRowHTML);
        console.log('DEBUG: After insert - rows count:', tableBody.rows.length);
        
        // Increment counter for next row
        actionPlanRowCounter++;
        
        console.log('DEBUG: Row successfully added! New counter:', actionPlanRowCounter);
    });

    // Remove action plan row (using event delegation)
    document.addEventListener('click', function(e) {
        console.log('DEBUG: Document click detected on:', e.target);
        
        if (e.target && e.target.classList.contains('remove-row')) {
            console.log('DEBUG: Remove button clicked');
            
            // Only allow removal if there's more than one row
            if (tableBody.rows.length > 1) {
                e.target.closest('tr').remove();
                console.log('DEBUG: Row removed, remaining rows:', tableBody.rows.length);
            } else {
                alert('Minimal harus ada satu baris Action Plan');
            }
        }
    });

    // Test button existence every second for 5 seconds
    let testCount = 0;
    const testInterval = setInterval(function() {
        testCount++;
        console.log(`DEBUG: Test ${testCount} - Button exists:`, !!document.getElementById('add-action-plan'));
        
        if (testCount >= 5) {
            clearInterval(testInterval);
        }
    }, 1000);
});

// Also add a global function for manual testing
window.testAddRow = function() {
    console.log('=== MANUAL TEST ADD ROW ===');
    const tableBody = document.querySelector('#action-plan-table tbody');
    if (tableBody) {
        const newRowHTML = `
            <tr>
                <td><input type="text" name="action_plan[999]" class="form-control" required value="TEST ROW"></td>
                <td class="text-center">
                    <input type="hidden" name="diaplikasikan[999]" value="0">
                    <input type="checkbox" name="diaplikasikan[999]" value="1">
                </td>
                <td><input type="number" name="frekuensi[999]" class="form-control" min="0" max="3" value="0" required></td>
                <td><input type="number" name="hasil[999]" class="form-control" min="1" max="4" value="1" required></td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button>
                </td>
            </tr>
        `;
        tableBody.insertAdjacentHTML('beforeend', newRowHTML);
        console.log('MANUAL TEST: Row added successfully');
    } else {
        console.log('MANUAL TEST: Table body not found');
    }
};

// Immediate test
console.log('=== IMMEDIATE TEST ===');
console.log('Button exists:', !!document.getElementById('add-action-plan'));
console.log('Table exists:', !!document.getElementById('action-plan-table'));
</script>
@endsection