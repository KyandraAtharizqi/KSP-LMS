@extends('layout.main')

@section('title', 'Assign Paraf & Signature - Surat Tugas Pelatihan')

@push('script')
<style>
    .modal-lg { max-width: 1100px !important; }
    .table th, .table td { vertical-align: middle; text-align: center; min-width: 140px; font-size: 15px; }
    .table th { background: #f8f9fa; }
    .pre-filled {
        border-left: 3px solid #fd7e14 !important;
        padding-left: 10px !important;
    }
    .reference-label {
        font-size: 12px;
        color: #fd7e14;
        display: block;
        margin-top: 2px;
    }
    .form-control.pre-filled:focus {
        box-shadow: 0 0 0 0.25rem rgba(253, 126, 20, 0.25);
    }
</style>

<script>
// Debug information to console - this will help identify issues
console.log("Debug - existingSuratTugas:", @json($existingSuratTugas));
console.log("Debug - latestRejection:", @json($latestRejection));

// Fix initialization of data from existing approval records
@php
    // Handle potential null/empty values to prevent errors
    $latestRound = 1;  // Default value if no rounds exist
    $existingParafsJson = '[]';
    $existingSignatureJson = 'null';
    
    if ($existingSuratTugas) {
        try {
            // Get latest round DYNAMICALLY from existing approvals
            $latestRound = $existingSuratTugas->signaturesAndParafs()->max('round') ?? 1;
            
            // Get parafs safely for the LATEST round only
            $parafs = $existingSuratTugas->signaturesAndParafs()
                ->where('type', 'paraf')
                ->where('round', $latestRound)  // Using the dynamically determined latest round
                ->get();
                
            if ($parafs->count() > 0) {
                $existingParafsJson = $parafs->map(function($p) {
                    if ($p->user) {
                        return [
                            'id' => $p->user_id,
                            'name' => $p->user->name,
                            'registration_id' => $p->user->registration_id,
                            'jabatan_full' => $p->user->jabatan_full ?? ($p->user->jabatan ? $p->user->jabatan->name : '-'),
                        ];
                    }
                    return null;
                })->filter()->values()->toJson();
            }
            
            // Get signature safely for the LATEST round only
            $signature = $existingSuratTugas->signaturesAndParafs()
                ->where('type', 'signature')
                ->where('round', $latestRound)  // Using the same latest round
                ->first();
                
            if ($signature && $signature->user) {
                $existingSignatureJson = json_encode([
                    'id' => $signature->user_id,
                    'name' => $signature->user->name,
                    'registration_id' => $signature->user->registration_id,
                    'jabatan_full' => $signature->user->jabatan_full ?? ($signature->user->jabatan ? $signature->user->jabatan->name : '-'),
                ]);
            }

            // Add debug info to help troubleshoot
            \Log::info("SuratTugas #{$existingSuratTugas->id} - Latest round: {$latestRound}");
            \Log::info("Parafs found: " . $parafs->count());
            \Log::info("Signature found: " . ($signature ? 'Yes' : 'No'));
        } catch (\Exception $e) {
            // Log error but don't crash
            \Log::error("Error loading existing approvals: " . $e->getMessage());
        }
    }
@endphp

// Properly initialize the selected arrays with error handling
let selectedParafs = [];
try {
    selectedParafs = {!! $existingParafsJson !!} || [];
} catch (e) {
    console.error("Error parsing paraf data:", e);
    selectedParafs = [];
}

let selectedSignature = null;
try {
    selectedSignature = {!! $existingSignatureJson !!};
} catch (e) {
    console.error("Error parsing signature data:", e);
}

let selectedTanggalPelaksanaan = [];

// Handle tanggal_pelaksanaan from existing data with better error handling
@if($existingSuratTugas && $existingSuratTugas->tanggal_pelaksanaan)
    try {
        let pelaksanaanData = @json($existingSuratTugas->tanggal_pelaksanaan);
        if (typeof pelaksanaanData === 'string' && pelaksanaanData.trim() !== '') {
            selectedTanggalPelaksanaan = JSON.parse(pelaksanaanData);
        } else if (Array.isArray(pelaksanaanData)) {
            selectedTanggalPelaksanaan = pelaksanaanData;
        }
    } catch(e) {
        console.error("Error parsing tanggal pelaksanaan:", e);
        selectedTanggalPelaksanaan = [];
    }
@endif

// Log the parsed data for debugging
console.log("Initial selectedParafs:", selectedParafs);
console.log("Initial selectedSignature:", selectedSignature);
console.log("Initial selectedTanggalPelaksanaan:", selectedTanggalPelaksanaan);

// ==========================
// Document Ready - SINGLE rendering implementation
// ==========================
document.addEventListener('DOMContentLoaded', function () {
    console.log("DOM loaded - rendering data (SINGLE METHOD)");
    
    // Clear any existing rendered items first to prevent duplicates
    document.getElementById('selected-paraf-list').innerHTML = '';
    document.getElementById('paraf-inputs').innerHTML = '';
    document.getElementById('selected-signature-list').innerHTML = '';
    document.getElementById('signature-inputs').innerHTML = '';
    
    // Render parafs with error handling
    try {
        if (Array.isArray(selectedParafs) && selectedParafs.length > 0) {
            console.log("Rendering parafs:", selectedParafs);
            renderParafList(selectedParafs);
        }
    } catch (e) {
        console.error("Error rendering parafs:", e);
    }
    
    // Render signature with error handling
    try {
        if (selectedSignature) {
            console.log("Rendering signature:", selectedSignature);
            renderSignatureSelection(selectedSignature);
        }
    } catch (e) {
        console.error("Error rendering signature:", e);
    }
    
    // Render tanggal pelaksanaan
    try {
        renderTanggalPelaksanaan();
    } catch (e) {
        console.error("Error rendering tanggal pelaksanaan:", e);
    }
    
    // Add reference styling
    addReferenceTooltips();
    
    // Setup search filters
    const searchInputs = [
        { inputId: 'paraf-search', tableSelector: '#parafModal table tbody tr' },
        { inputId: 'signature-search', tableSelector: '#signatureModal table tbody tr' }
    ];

    searchInputs.forEach(({ inputId, tableSelector }) => {
        const input = document.getElementById(inputId);
        if (!input) return;
        
        const rows = document.querySelectorAll(tableSelector);
        input.addEventListener('input', function () {
            const keyword = input.value.toLowerCase();
            rows.forEach(row => {
                const name = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                const regid = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const jabatan = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const dept = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                const golongan = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
                row.style.display = (name.includes(keyword) || regid.includes(keyword) || jabatan.includes(keyword) || dept.includes(keyword) || golongan.includes(keyword)) ? '' : 'none';
            });
        });
    });
});

// Functions for rendering parafs
function renderParafList(parafUsers) {
    const container = document.getElementById('selected-paraf-list');
    const inputContainer = document.getElementById('paraf-inputs');
    // Don't clear here - we already clear in DOMContentLoaded
    
    parafUsers.forEach(user => {
        if (!user || !user.id) return;
        
        let tag = document.createElement('div');
        tag.className = 'badge bg-warning text-dark me-1 mb-1 d-inline-flex align-items-center pre-filled';
        tag.innerHTML = `${user.name} (${user.registration_id}) - ${user.jabatan_full || '-'}
            <button type="button" class="btn-close btn-sm ms-2" onclick="removeParaf(${user.id})"></button>`;
        container.appendChild(tag);

        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'paraf_users[]';
        input.value = user.id;
        inputContainer.appendChild(input);
    });
}

// Function to render signature
function renderSignatureSelection(user) {
    const container = document.getElementById('selected-signature-list');
    const inputContainer = document.getElementById('signature-inputs');
    // Don't clear here - we already clear in DOMContentLoaded
    
    if (!user || !user.id) return;
    
    let tag = document.createElement('div');
    tag.className = 'badge bg-success text-white me-1 mb-1 d-inline-flex align-items-center pre-filled';
    tag.innerHTML = `${user.name} (${user.registration_id}) - ${user.jabatan_full || '-'}
        <button type="button" class="btn-close btn-close-white btn-sm ms-2" onclick="removeSignature()"></button>`;
    container.appendChild(tag);

    let input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'signature_user';
    input.value = user.id;
    inputContainer.appendChild(input);
}

function addToList(user, listType) {
    if (listType === 'paraf') {
        if (selectedParafs.length >= 3) { 
            alert('Maksimal 3 orang boleh dipilih sebagai paraf.'); 
            return; 
        }

        // Check if user already exists in the array
        if (!selectedParafs.some(p => p.id == user.id)) {
            selectedParafs.push(user);
        }
        // Clear first, then render
        document.getElementById('selected-paraf-list').innerHTML = '';
        document.getElementById('paraf-inputs').innerHTML = '';
        renderParafList(selectedParafs);
        bootstrap.Modal.getInstance(document.getElementById('parafModal')).hide();
    } 
    else if (listType === 'signature') {
        selectedSignature = user;
        // Clear first, then render
        document.getElementById('selected-signature-list').innerHTML = '';
        document.getElementById('signature-inputs').innerHTML = '';
        renderSignatureSelection(user);
        bootstrap.Modal.getInstance(document.getElementById('signatureModal')).hide();
    }
}

function removeParaf(userId) {
    selectedParafs = selectedParafs.filter(p => p.id != userId);
    // Clear first, then render
    document.getElementById('selected-paraf-list').innerHTML = '';
    document.getElementById('paraf-inputs').innerHTML = '';
    renderParafList(selectedParafs);
}

function removeSignature() {
    selectedSignature = null;
    document.getElementById('selected-signature-list').innerHTML = '';
    document.getElementById('signature-inputs').innerHTML = '';
}

// ==========================
// Tanggal Pelaksanaan Functions
// ==========================
function renderTanggalPelaksanaan() {
    const container = document.getElementById('tanggal-pelaksanaan-list');
    const inputContainer = document.getElementById('tanggal-pelaksanaan-inputs');
    container.innerHTML = '';
    inputContainer.innerHTML = '';

    selectedTanggalPelaksanaan.forEach((tgl, idx) => {
        // Create badge display
        const tag = document.createElement('div');
        tag.className = 'badge bg-dark me-1 mb-1 d-inline-flex align-items-center pre-filled';
        tag.innerHTML = `${formatDate(tgl)}
            <button type="button" class="btn-close btn-close-white btn-sm ms-2" 
                onclick="removeTanggalPelaksanaan(${idx})" aria-label="Remove"></button>`;
        container.appendChild(tag);

        // Create hidden input for form submission
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'tanggal_pelaksanaan[]';
        input.value = tgl;
        inputContainer.appendChild(input);
    });

    if (selectedTanggalPelaksanaan.length > 0) {
        const countInfo = document.createElement('small');
        countInfo.className = 'text-muted d-block mt-1 pre-filled';
        countInfo.innerHTML = `<i class="bx bx-info-circle"></i> ${selectedTanggalPelaksanaan.length} tanggal dari pengisian sebelumnya`;
        container.appendChild(countInfo);
    }
}

function addTanggalPelaksanaan() {
    const picker = document.getElementById('tanggal-pelaksanaan-picker');
    const tgl = picker.value;

    if (!tgl) { 
        alert('Silakan pilih tanggal terlebih dahulu'); 
        return; 
    }
    if (selectedTanggalPelaksanaan.includes(tgl)) { 
        alert('Tanggal sudah dipilih sebelumnya'); 
        picker.value = ''; 
        return; 
    }

    selectedTanggalPelaksanaan.push(tgl);
    selectedTanggalPelaksanaan.sort();
    renderTanggalPelaksanaan();
    picker.value = '';
}

function removeTanggalPelaksanaan(index) {
    if (confirm('Hapus tanggal ini?')) {
        selectedTanggalPelaksanaan.splice(index, 1);
        renderTanggalPelaksanaan();
    }
}

// Helper function to format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        weekday: 'short',
        year: 'numeric',
        month: 'short', 
        day: 'numeric'
    });
}

// Add reference tooltips
function addReferenceTooltips() {
    const preFilled = document.querySelectorAll('.pre-filled');
    preFilled.forEach(el => {
        el.setAttribute('title', 'Data dari pengisian sebelumnya');
        if (el.tagName !== 'SMALL') { // Don't add border to small elements
            el.style.borderLeft = '3px solid #fd7e14'; // Orange border to indicate reference
        }
    });
}
</script>

@endpush

@php
    // Update the controller to properly handle both scenarios:
    // 1. New assignment (from SuratPengajuan)
    // 2. Re-assignment (from SuratTugas)

    // Ensure the form action is set correctly
    $formAction = $existingSuratTugas 
        ? route('training.surattugas.assign.submit', ['id' => $suratPengajuan->id])
        : route('training.surattugas.assign.submit', ['id' => $suratPengajuan->id]);
        
    $pageTitle = $existingSuratTugas ? 'Re-assign Paraf & Signature' : 'Assign Paraf & Signature';
@endphp

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">{{ $pageTitle }}</h4>

    @if($latestRejection)
        <div class="alert alert-danger">
            <strong>❗ Surat Tugas Ditolak!</strong><br>
            Round {{ $latestRejection->round }}, Seq {{ $latestRejection->sequence }} – 
            "{{ $latestRejection->rejection_reason }}"
        </div>
    @endif

    {{-- Debug information (can be kept or removed as needed) --}}
    @if(app()->environment('local', 'development'))
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Debug Information</h5>
            </div>
            <div class="card-body">
                <h6>Existing Surat Tugas:</h6>
                <pre>{{ $existingSuratTugas ? json_encode([
                    'id' => $existingSuratTugas->id,
                    'kode_pelatihan' => $existingSuratTugas->kode_pelatihan,
                    'created_at' => $existingSuratTugas->created_at,
                ], JSON_PRETTY_PRINT) : 'null' }}</pre>
                
                <h6>Signatures & Parafs (if any):</h6>
                @if($existingSuratTugas && $existingSuratTugas->signaturesAndParafs->count() > 0)
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Round</th>
                                <th>Sequence</th>
                                <th>Status</th>
                                <th>User ID</th>
                                <th>User Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($existingSuratTugas->signaturesAndParafs as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->type }}</td>
                                    <td>{{ $item->round }}</td>
                                    <td>{{ $item->sequence }}</td>
                                    <td>{{ $item->status }}</td>
                                    <td>{{ $item->user_id }}</td>
                                    <td>{{ $item->user->name ?? 'User not found' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No signature or paraf records found</p>
                @endif
            </div>
        </div>
    @endif

    {{-- Training Info --}}
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="fw-bold mb-3">Informasi Pelatihan (dari Pengajuan)</h4>
            <h5>Surat Pengajuan: <strong>{{ $suratPengajuan->kode_pelatihan ?? '-' }}</strong></h5>
            <p><strong>Judul:</strong> {{ $suratPengajuan->judul ?? '-' }}</p>
            <p><strong>Penyelenggara:</strong> {{ $suratPengajuan->penyelenggara ?? '-' }}</p>
            <p><strong>Tanggal Pelatihan:</strong>
                {{ optional($suratPengajuan->tanggal_mulai)->format('d M Y') ?? '-' }} s.d.
                {{ optional($suratPengajuan->tanggal_selesai)->format('d M Y') ?? '-' }}
            </p>
        </div>
    </div>

    {{-- Training Participants --}}
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="fw-bold mb-3">Peserta Pelatihan</h4>
            <ul>
                @foreach($suratPengajuan->participants as $participant)
                    <li>{{ $participant->user->name }} ({{ $participant->user->registration_id }}) - {{ $participant->user->jabatan_full ?? '-' }}</li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Assign Form --}}
    <div class="card">
        <div class="card-body">
                <form action="{{ $formAction }}" method="POST">
                @csrf
                <!-- Detail Surat Tugas -->
                <h4 class="fw-bold mb-3">Detail Surat Tugas</h4>

                <div class="mb-3">
                    <label>Tempat <span class="text-danger">*</span></label>
                    <input type="text" name="tempat" class="form-control {{ $existingSuratTugas?->tempat ? 'pre-filled' : '' }}" 
                        value="{{ old('tempat', $existingSuratTugas?->tempat) }}" required>
                    @if($existingSuratTugas?->tempat)
                        <span class="reference-label">Data dari pengisian sebelumnya</span>
                    @endif
                </div>

                <div class="mb-3">
                    <label>Tanggal Mulai <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_mulai" class="form-control {{ $existingSuratTugas?->tanggal_mulai ? 'pre-filled' : '' }}" 
                        value="{{ old('tanggal_mulai', $existingSuratTugas?->tanggal_mulai ?? $suratPengajuan->tanggal_mulai?->format('Y-m-d')) }}" required>
                    @if($existingSuratTugas?->tanggal_mulai)
                        <span class="reference-label">Data dari pengisian sebelumnya</span>
                    @endif
                </div>

                <div class="mb-3">
                    <label>Tanggal Selesai <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_selesai" class="form-control {{ $existingSuratTugas?->tanggal_selesai ? 'pre-filled' : '' }}"
                        value="{{ old('tanggal_selesai', $existingSuratTugas?->tanggal_selesai ?? $suratPengajuan->tanggal_selesai?->format('Y-m-d')) }}" required>
                    @if($existingSuratTugas?->tanggal_selesai)
                        <span class="reference-label">Data dari pengisian sebelumnya</span>
                    @endif
                </div>

                <div class="col-md-12 mb-3">
                    <label>Tanggal Pelaksanaan</label>
                    <div id="tanggal-pelaksanaan-list" class="mb-2"></div>
                    <div id="tanggal-pelaksanaan-inputs"></div>
                    <div class="input-group">
                        <input type="date" id="tanggal-pelaksanaan-picker" class="form-control">
                        <button type="button" class="btn btn-outline-primary" onclick="addTanggalPelaksanaan()">+ Tambah</button>
                    </div>
                </div>

                <div class="mb-3">
                    <label>Durasi (hari/jam) <span class="text-danger">*</span></label>
                    <input type="text" name="durasi" class="form-control {{ $existingSuratTugas?->durasi ? 'pre-filled' : '' }}"
                        value="{{ old('durasi', $existingSuratTugas?->durasi ?? $suratPengajuan->durasi) }}" required>
                    @if($existingSuratTugas?->durasi)
                        <span class="reference-label">Data dari pengisian sebelumnya</span>
                    @endif
                </div>

                <div class="mb-3">
                    <label>Tujuan <span class="text-danger">*</span></label>
                    <textarea name="tujuan" class="form-control {{ $existingSuratTugas?->tujuan ? 'pre-filled' : '' }}" rows="3" required>{{ old('tujuan', $existingSuratTugas?->tujuan ?? 'Meningkatkan kompetensi dan keterampilan karyawan dalam bidang ' . $suratPengajuan->kompetensi) }}</textarea>
                    @if($existingSuratTugas?->tujuan)
                        <span class="reference-label">Data dari pengisian sebelumnya</span>
                    @endif
                </div>

                <div class="mb-3">
                    <label>Waktu <span class="text-danger">*</span></label>
                    <input type="text" name="waktu" class="form-control {{ $existingSuratTugas?->waktu ? 'pre-filled' : '' }}"
                        value="{{ old('waktu', $existingSuratTugas?->waktu ?? '08:00 - 17:00 WIB') }}" required>
                    @if($existingSuratTugas?->waktu)
                        <span class="reference-label">Data dari pengisian sebelumnya</span>
                    @endif
                </div>

                <div class="mb-3">
                    <label>Instruksi <span class="text-danger">*</span></label>
                    <textarea name="instruksi" class="form-control {{ $existingSuratTugas?->instruksi ? 'pre-filled' : '' }}" rows="4" required>{{ old('instruksi', $existingSuratTugas?->instruksi ?? 'Melaksanakan pelatihan sesuai jadwal dan instruksi yang diberikan.') }}</textarea>
                    @if($existingSuratTugas?->instruksi)
                        <span class="reference-label">Data dari pengisian sebelumnya</span>
                    @endif
                </div>

                <div class="mb-3">
                    <label>Hal-hal yang perlu diperhatikan <span class="text-danger">*</span></label>
                    <textarea name="hal_perhatian" class="form-control {{ $existingSuratTugas?->hal_perhatian ? 'pre-filled' : '' }}" rows="4" required>{{ old('hal_perhatian', $existingSuratTugas?->hal_perhatian ?? '1. Hadir tepat waktu
2. Berpakaian rapi
3. Membawa perlengkapan
4. Menjaga ketertiban') }}</textarea>
                    @if($existingSuratTugas?->hal_perhatian)
                        <span class="reference-label">Data dari pengisian sebelumnya</span>
                    @endif
                </div>

                <div class="mb-3">
                    <label>Catatan <span class="text-danger">*</span></label>
                    <textarea name="catatan" class="form-control {{ $existingSuratTugas?->catatan ? 'pre-filled' : '' }}" rows="3" required>{{ old('catatan', $existingSuratTugas?->catatan ?? 'Surat tugas berlaku selama pelatihan. Biaya ditanggung sesuai ketentuan.') }}</textarea>
                    @if($existingSuratTugas?->catatan)
                        <span class="reference-label">Data dari pengisian sebelumnya</span>
                    @endif
                </div>

                <!-- Paraf -->
                <h4 class="fw-bold mb-3">Paraf (Maksimal 3)</h4>
                <button type="button" class="btn btn-sm btn-outline-warning mb-2" data-bs-toggle="modal" data-bs-target="#parafModal">+ Tambah Paraf</button>
                <div id="selected-paraf-list" class="mb-2"></div>
                <div id="paraf-inputs"></div>

                <!-- Signature -->
                <h4 class="fw-bold mb-3">Penandatangan</h4>
                <p class="text-muted">Pilih <strong>Penandatangan Surat Tugas</strong> (hanya 1 orang).</p>
                <button type="button" class="btn btn-sm btn-outline-success mb-2" data-bs-toggle="modal" data-bs-target="#signatureModal">+ Tambah Penandatangan</button>
                <div id="selected-signature-list" class="mb-2"></div>
                <div id="signature-inputs"></div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('training.surattugas.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">{{ $existingSuratTugas ? 'Update Assignment' : 'Simpan Assign' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Paraf Modal -->
<div class="modal fade" id="parafModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pilih Paraf (Maksimal 3)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" id="paraf-search" class="form-control mb-3" placeholder="Cari nama / jabatan / departemen">
        <table class="table table-sm">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Registration ID</th>
              <th>Jabatan Lengkap</th>
              <th>Departemen</th>
              <th>Golongan</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($users as $user)
            <tr data-name="{{ $user->name }}" data-department="{{ $user->department->name ?? '' }}" data-jabatan="{{ $user->jabatan->name ?? '' }}">
              <td>{{ $user->name }}</td>
              <td>{{ $user->registration_id }}</td>
              <td>{{ $user->jabatan_full }}</td>
              <td>{{ $user->department->name ?? '-' }}</td>
              <td>{{ $user->golongan ?? '-' }}</td>
              <td>
                <button type="button" class="btn btn-sm btn-outline-warning text-dark" 
                    onclick='addToList({
                        "id": {{ $user->id }},
                        "registration_id": "{{ $user->registration_id }}",
                        "name": "{{ $user->name }}",
                        "jabatan_full": "{{ $user->jabatan_full ?? ($user->jabatan->name ?? "-") }}"
                    }, "paraf")'>
                    Tambah
                </button>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Signature Modal -->
<div class="modal fade" id="signatureModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pilih Penandatangan (Signature)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" id="signature-search" class="form-control mb-3" placeholder="Cari nama / jabatan / departemen">
        <table class="table table-sm">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Registration ID</th>
              <th>Jabatan Lengkap</th>
              <th>Departemen</th>
              <th>Golongan</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($users as $user)
            <tr data-name="{{ $user->name }}" data-department="{{ $user->department->name ?? '' }}" data-jabatan="{{ $user->jabatan->name ?? '' }}">
              <td>{{ $user->name }}</td>
              <td>{{ $user->registration_id }}</td>
              <td>{{ $user->jabatan_full ?? ($user->jabatan->name ?? '-') }}</td>
              <td>{{ $user->department->name ?? '-' }}</td>
              <td>{{ $user->golongan ?? '-' }}</td>
              <td>
                <button type="button" class="btn btn-sm btn-outline-success" 
                    onclick='addToList({
                        "id": {{ $user->id }},
                        "registration_id": "{{ $user->registration_id }}",
                        "name": "{{ $user->name }}",
                        "jabatan_full": "{{ $user->jabatan_full ?? ($user->jabatan->name ?? "-") }}"
                    }, "signature")'>
                    Tambah
                </button>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@endsection