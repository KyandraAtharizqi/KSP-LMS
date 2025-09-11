@extends('layout.main')

@section('title', 'Assign Paraf & Signature - Surat Tugas Pelatihan')

@push('script')
<style>
    .modal-lg { max-width: 1100px !important; }
    .table th, .table td { vertical-align: middle; text-align: center; min-width: 140px; font-size: 15px; }
    .table th { background: #f8f9fa; }
</style>

<script>
let selectedParafs = @json($existingParafs ?? []);
let selectedSignature = @json($existingSignature ?? null);
let selectedTanggalPelaksanaan = @json($existingSuratTugas?->tanggal_pelaksanaan ?? []);

// ==========================
// Paraf & Signature Functions
// ==========================
function renderList(arr, containerId, inputName, inputContainerId, type) {
    const container = document.getElementById(containerId);
    const inputContainer = document.getElementById(inputContainerId);
    container.innerHTML = '';
    inputContainer.innerHTML = '';

    if (type === 'signature' && selectedSignature) {
        let tag = document.createElement('div');
        tag.className = 'badge bg-success me-1 mb-1 d-inline-flex align-items-center';
        tag.innerHTML = `${selectedSignature.name} (${selectedSignature.registration_id}) - ${selectedSignature.jabatan_full || '-' }
            <button type="button" class="btn-close btn-sm ms-2" onclick="removeFromList('${selectedSignature.id}', '${containerId}')"></button>`;
        container.appendChild(tag);

        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = inputName;
        input.value = selectedSignature.id;
        inputContainer.appendChild(input);

    } else if (type === 'paraf') {
        arr.forEach(user => {
            let tag = document.createElement('div');
            tag.className = 'badge bg-warning text-dark me-1 mb-1 d-inline-flex align-items-center';
            tag.innerHTML = `${user.name} (${user.registration_id}) - ${user.jabatan_full || '-'}
                <button type="button" class="btn-close btn-sm ms-2" onclick="removeFromList('${user.id}', '${containerId}')"></button>`;
            container.appendChild(tag);

            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = inputName;
            input.value = user.id;
            inputContainer.appendChild(input);
        });
    }
}

function removeFromList(userId, listType) {
    if (listType === 'selected-paraf-list') {
        selectedParafs = selectedParafs.filter(p => p.id != userId);
        renderList(selectedParafs, 'selected-paraf-list', 'paraf_users[]', 'paraf-inputs', 'paraf');
    } else if (listType === 'selected-signature-list') {
        selectedSignature = null;
        renderList([], 'selected-signature-list', 'signature_user', 'signature-inputs', 'signature');
    }
}

function addToList(user, listType) {
    if(listType === 'paraf' && selectedParafs.length >= 3) { 
        alert('Maksimal 3 orang boleh dipilih sebagai paraf.'); 
        return; 
    }

    if(listType === 'paraf') {
        if(!selectedParafs.find(p => p.id == user.id)) selectedParafs.push(user);
        renderList(selectedParafs, 'selected-paraf-list', 'paraf_users[]', 'paraf-inputs', 'paraf');
        bootstrap.Modal.getInstance(document.getElementById('parafModal')).hide();
    } else if(listType === 'signature') {
        selectedSignature = user;
        renderList([], 'selected-signature-list', 'signature_user', 'signature-inputs', 'signature');
        bootstrap.Modal.getInstance(document.getElementById('signatureModal')).hide();
    }
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
        // Badge display
        const tag = document.createElement('div');
        tag.className = 'badge bg-dark me-1 mb-1 d-inline-flex align-items-center';
        tag.innerHTML = `${formatDate(tgl)}
            <button type="button" class="btn-close btn-close-white btn-sm ms-2" onclick="removeTanggalPelaksanaan(${idx})" aria-label="Remove"></button>`;
        container.appendChild(tag);

        // Hidden input for form submission
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'tanggal_pelaksanaan[]';
        input.value = tgl;
        inputContainer.appendChild(input);
    });

    // Optional: count info
    if (selectedTanggalPelaksanaan.length > 0) {
        const countInfo = document.createElement('small');
        countInfo.className = 'text-muted d-block mt-1';
        countInfo.textContent = `${selectedTanggalPelaksanaan.length} tanggal dipilih`;
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

// Helper: format date for badge
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        weekday: 'short',
        year: 'numeric',
        month: 'short', 
        day: 'numeric'
    });
}

// ==========================
// Search Filter Functions
// ==========================
document.addEventListener('DOMContentLoaded', function () {
    renderList(selectedParafs, 'selected-paraf-list', 'paraf_users[]', 'paraf-inputs', 'paraf');
    renderList([], 'selected-signature-list', 'signature_user', 'signature-inputs', 'signature');
    renderTanggalPelaksanaan();

    const searchInputs = [
        { inputId: 'paraf-search', tableSelector: '#parafModal table tbody tr' },
        { inputId: 'signature-search', tableSelector: '#signatureModal table tbody tr' }
    ];

    searchInputs.forEach(({ inputId, tableSelector }) => {
        const input = document.getElementById(inputId);
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
</script>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">{{ $existingSuratTugas ? 'Re-assign' : 'Assign' }} Paraf & Signature</h4>

    {{-- Show rejection reason if exists --}}
    @if ($latestRejection)
        <div class="alert alert-danger">
            <strong>❗ Surat Tugas Ditolak!</strong><br>
            Round {{ $latestRejection->round }}, Seq {{ $latestRejection->sequence }} – 
            "{{ $latestRejection->rejection_reason }}"
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
                <form action="{{ route('training.surattugas.assign.submit', ['id' => $suratPengajuan->id]) }}" method="POST">
                @csrf
                <!-- Detail Surat Tugas -->
                <h4 class="fw-bold mb-3">Detail Surat Tugas</h4>

                <div class="mb-3">
                    <label>Tempat <span class="text-danger">*</span></label>
                    <input type="text" name="tempat" class="form-control" value="{{ old('tempat', $existingSuratTugas?->tempat) }}" required>
                </div>

                <div class="mb-3">
                    <label>Tanggal Mulai <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_mulai" class="form-control" 
                        value="{{ old('tanggal_mulai', $existingSuratTugas?->tanggal_mulai ?? $suratPengajuan->tanggal_mulai?->format('Y-m-d')) }}" required>
                </div>

                <div class="mb-3">
                    <label>Tanggal Selesai <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_selesai" class="form-control" 
                        value="{{ old('tanggal_selesai', $existingSuratTugas?->tanggal_selesai ?? $suratPengajuan->tanggal_selesai?->format('Y-m-d')) }}" required>
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
                    <input type="text" name="durasi" class="form-control" value="{{ old('durasi', $existingSuratTugas?->durasi ?? $suratPengajuan->durasi) }}" required>
                </div>

                <div class="mb-3">
                    <label>Tujuan <span class="text-danger">*</span></label>
                    <textarea name="tujuan" class="form-control" rows="3" required>{{ old('tujuan', $existingSuratTugas?->tujuan ?? 'Meningkatkan kompetensi dan keterampilan karyawan dalam bidang ' . $suratPengajuan->kompetensi) }}</textarea>
                </div>

                <div class="mb-3">
                    <label>Waktu <span class="text-danger">*</span></label>
                    <input type="text" name="waktu" class="form-control" value="{{ old('waktu', $existingSuratTugas?->waktu ?? '08:00 - 17:00 WIB') }}" required>
                </div>

                <div class="mb-3">
                    <label>Instruksi <span class="text-danger">*</span></label>
                    <textarea name="instruksi" class="form-control" rows="4" required>{{ old('instruksi', $existingSuratTugas?->instruksi ?? 'Melaksanakan pelatihan sesuai jadwal dan instruksi yang diberikan.') }}</textarea>
                </div>

                <div class="mb-3">
                    <label>Hal-hal yang perlu diperhatikan <span class="text-danger">*</span></label>
                    <textarea name="hal_perhatian" class="form-control" rows="4" required>{{ old('hal_perhatian', $existingSuratTugas?->hal_perhatian ?? '1. Hadir tepat waktu\n2. Berpakaian rapi\n3. Membawa perlengkapan\n4. Menjaga ketertiban') }}</textarea>
                </div>

                <div class="mb-3">
                    <label>Catatan <span class="text-danger">*</span></label>
                    <textarea name="catatan" class="form-control" rows="3" required>{{ old('catatan', $existingSuratTugas?->catatan ?? 'Surat tugas berlaku selama pelatihan. Biaya ditanggung sesuai ketentuan.') }}</textarea>
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