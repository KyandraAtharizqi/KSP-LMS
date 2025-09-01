@extends('layout.main')

@section('title', 'Assign Paraf & Signature - Surat Tugas Pelatihan')

@push('script')

<style>
    .modal-lg {
        max-width: 1100px !important;
    }
    .table th, .table td {
        vertical-align: middle;
        text-align: center;
        min-width: 140px;
        font-size: 15px;
    }
    .table th {
        background: #f8f9fa;
    }
</style>

<script>
    let selectedParafs = [];
    let selectedSignature = null;

    function renderList(arr, containerId, inputName, inputContainerId, type) {
        const container = document.getElementById(containerId);
        const inputContainer = document.getElementById(inputContainerId);
        container.innerHTML = '';
        inputContainer.innerHTML = '';

        if (type === 'signature' && selectedSignature) {
            // Handle single signature
            let colorClass = 'bg-success';
            const tag = document.createElement('div');
            tag.className = `badge ${colorClass} me-1 mb-1 d-inline-flex align-items-center`;
            tag.innerHTML = `${selectedSignature.name} (${selectedSignature.registration_id}) - ${selectedSignature.jabatan_full || '-'}
                <button type="button" class="btn-close btn-sm ms-2" onclick="removeFromList('${selectedSignature.id}', '${containerId}')"></button>`;
            container.appendChild(tag);

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = inputName;
            input.value = selectedSignature.id;
            inputContainer.appendChild(input);
        } else if (type === 'paraf') {
            // Handle multiple parafs
            arr.forEach(user => {
                let colorClass = 'bg-warning text-dark';
                const tag = document.createElement('div');
                tag.className = `badge ${colorClass} me-1 mb-1 d-inline-flex align-items-center`;
                tag.innerHTML = `${user.name} (${user.registration_id}) - ${user.jabatan_full || '-'}
                    <button type="button" class="btn-close btn-sm ms-2" onclick="removeFromList('${user.id}', '${containerId}')"></button>`;
                container.appendChild(tag);

                const input = document.createElement('input');
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
            // Check if user is already in paraf list
            if(!selectedParafs.find(p => p.id == user.id)) {
                selectedParafs.push(user);
            }
            renderList(selectedParafs, 'selected-paraf-list', 'paraf_users[]', 'paraf-inputs', 'paraf');
            bootstrap.Modal.getInstance(document.getElementById('parafModal')).hide();
        } else if(listType === 'signature') {
            selectedSignature = user;
            renderList([], 'selected-signature-list', 'signature_user', 'signature-inputs', 'signature');
            bootstrap.Modal.getInstance(document.getElementById('signatureModal')).hide();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
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

                      row.style.display = (
                          name.includes(keyword) ||
                          regid.includes(keyword) ||
                          jabatan.includes(keyword) ||
                          dept.includes(keyword) ||
                          golongan.includes(keyword)
                      ) ? '' : 'none';

                });
            });
        });
    });
</script>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Assign Paraf & Signature</h4>

    {{-- Show rejection reason if exists --}}
    @if ($latestRejection)
        <div class="alert alert-danger">
            <strong>❗ Surat Tugas Ditolak!</strong><br>
            Round {{ $latestRejection->round }}, Seq {{ $latestRejection->sequence }} – 
            "{{ $latestRejection->rejection_reason }}"
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <h5>Surat Tugas: <strong>{{ $suratTugas->kode_pelatihan ?? '-' }}</strong></h5>
            <p><strong>Judul:</strong> {{ $suratTugas->judul }}</p>
            <p><strong>Kode Pelatihan:</strong> {{ $suratTugas->pelatihan->kode_pelatihan ?? '-' }}</p>
            <p><strong>Kompetensi:</strong> {{ $suratTugas->pelatihan->kompetensi ?? '-' }}</p>
            <p><strong>Lokasi:</strong> {{ $suratTugas->pelatihan->lokasi ?? '-' }}</p>
            <p><strong>Instruktur:</strong> {{ $suratTugas->pelatihan->instruktur ?? '-' }}</p>
            <p><strong>Sifat:</strong> {{ $suratTugas->pelatihan->sifat ?? '-' }}</p>
            <p><strong>Tanggal Pelatihan:</strong>
                {{ optional($suratTugas->pelatihan->tanggal_mulai)->format('d M Y') ?? '-' }} s.d.
                {{ optional($suratTugas->pelatihan->tanggal_selesai)->format('d M Y') ?? '-' }}
            </p>
            <p><strong>Tempat:</strong> {{ $suratTugas->pelatihan->tempat ?? '-' }}</p>
            <p><strong>Penyelenggara:</strong> {{ $suratTugas->pelatihan->penyelenggara ?? '-' }}</p>
            <p><strong>Biaya:</strong> {{ $suratTugas->pelatihan->biaya ?? '-' }}</p>
            <p><strong>Keterangan:</strong> {{ $suratTugas->pelatihan->keterangan ?? '-' }}</p>

            <form action="{{ route('training.surattugas.assign.submit') }}" method="POST">
                @csrf
                <input type="hidden" name="surat_tugas_id" value="{{ $suratTugas->id }}">

                <!-- Paraf -->
                <hr><h5>Paraf (Maksimal 3)</h5>
                <div class="col-12 mb-2">
                    <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#parafModal">
                        + Tambah Paraf
                    </button>
                </div>
                <div class="col-12 mb-2" id="selected-paraf-list"></div>
                <div id="paraf-inputs"></div>

                <!-- Signature -->
                <hr><h5>Penandatangan</h5>
                <p class="text-muted">Pilih <strong>Penandatangan Surat Tugas</strong> (hanya 1 orang).</p>
                <div class="col-12 mb-2">
                    <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#signatureModal">
                        + Tambah Penandatangan
                    </button>
                </div>
                <div class="col-12 mb-2" id="selected-signature-list"></div>
                <div id="signature-inputs"></div>

                <div class="mb-3">
                    <label for="tujuan">Tujuan</label>
                    <textarea name="tujuan" class="form-control" required>{{ old('tujuan', $suratTugas->tujuan ?? '') }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="waktu">Waktu</label>
                    <input type="text" name="waktu" class="form-control" value="{{ old('waktu', $suratTugas->waktu ?? '') }}" required>
                </div>
                <div class="mb-3">
                    <label for="instruksi">Instruksi</label>
                    <textarea name="instruksi" class="form-control" required>{{ old('instruksi', $suratTugas->instruksi ?? '') }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="hal_perhatian">Hal-hal yang perlu diperhatikan</label>
                    <textarea name="hal_perhatian" class="form-control" required>{{ old('hal_perhatian', $suratTugas->hal_perhatian ?? '') }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="catatan">Catatan</label>
                    <textarea name="catatan" class="form-control" required>{{ old('catatan', $suratTugas->catatan ?? '') }}</textarea>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('training.surattugas.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Assign</button>
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