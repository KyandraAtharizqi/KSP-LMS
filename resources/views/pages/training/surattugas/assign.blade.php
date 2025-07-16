@extends('layout.main')

@section('title', 'Assign Paraf & Signature - Surat Tugas Pelatihan')

@push('script')
<script>
    let selectedParafs = [];
    let selectedSignature = [];

    function renderList(arr, containerId, inputName, inputContainerId, color) {
        const container = document.getElementById(containerId);
        const inputContainer = document.getElementById(inputContainerId);
        container.innerHTML = '';
        inputContainer.innerHTML = '';

        arr.forEach(user => {
            const tag = document.createElement('div');
            tag.className = `badge bg-${color} me-1 mb-1 d-inline-flex align-items-center`;
            tag.innerHTML = `${user.name} (${user.jabatan_full || '-'})
                <button type="button" class="btn-close btn-sm ms-2" onclick="removeFromList('${user.id}', '${containerId}')"></button>`;
            container.appendChild(tag);

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = inputName + (inputName.includes('[]') ? '' : '[]');
            input.value = user.id;
            inputContainer.appendChild(input);
        });
    }

    function removeFromList(userId, listType) {
        if (listType === 'selected-paraf-list') {
            selectedParafs = selectedParafs.filter(p => p.id !== userId);
            renderList(selectedParafs, 'selected-paraf-list', 'paraf_users[]', 'paraf-inputs', 'warning text-dark');
        } else if (listType === 'selected-signature-list') {
            selectedSignature = [];
            renderList([], 'selected-signature-list', 'signature_user', 'signature-inputs', 'success');
        }
    }

    function addToList(user, listType) {
        if (listType === 'paraf' && selectedParafs.length >= 3) {
            alert('Maksimal 3 orang boleh dipilih sebagai paraf.');
            return;
        }

        if (listType === 'paraf') {
            if (!selectedParafs.find(p => p.id === user.id)) {
                selectedParafs.push(user);
            }
            renderList(selectedParafs, 'selected-paraf-list', 'paraf_users[]', 'paraf-inputs', 'warning text-dark');
            bootstrap.Modal.getInstance(document.getElementById('parafModal')).hide();
        } else if (listType === 'signature') {
            selectedSignature = [user]; // Only one allowed
            renderList(selectedSignature, 'selected-signature-list', 'signature_user', 'signature-inputs', 'success');
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
                    const name = row.dataset.name.toLowerCase();
                    const jabatan = row.dataset.jabatan.toLowerCase();
                    const department = row.dataset.department.toLowerCase();
                    const division = row.dataset.division.toLowerCase();
                    const directorate = row.dataset.directorate.toLowerCase();

                    const isMatch = name.includes(keyword) ||
                                  jabatan.includes(keyword) ||
                                  department.includes(keyword) ||
                                  division.includes(keyword) ||
                                  directorate.includes(keyword);

                    row.style.display = isMatch ? '' : 'none';
                });
            });
        });
    });
</script>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Assign Paraf & Signature</h4>

    <div class="card">
        <div class="card-body">
            <h5>Surat Tugas: <strong>{{ $suratTugas->kode_tugas ?? '-' }}</strong></h5>
            <p><strong>Judul:</strong> {{ $suratTugas->judul }}</p>
            <p><strong>Tanggal Pelatihan:</strong>
                {{ optional($suratTugas->pelatihan->tanggal_mulai)->format('d M Y') ?? '-' }} s.d.
                {{ optional($suratTugas->pelatihan->tanggal_selesai)->format('d M Y') ?? '-' }}
            </p>
            <p><strong>Tempat:</strong> {{ $suratTugas->tempat }}</p>

            <form action="{{ route('training.surattugas.assign.submit') }}" method="POST">
                @csrf
                <input type="hidden" name="surat_tugas_id" value="{{ $suratTugas->id }}">

                <!-- Paraf -->
                <hr><h5>Paraf (Maksimal 3)</h5>
                <div class="col-12 mb-2">
                    <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#parafModal">+ Tambah Paraf</button>
                </div>
                <div class="col-12 mb-2" id="selected-paraf-list"></div>
                <div id="paraf-inputs"></div>

                <!-- Signature -->
                <hr><h5>Penandatangan (Hanya 1)</h5>
                <div class="col-12 mb-2">
                    <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#signatureModal">+ Tambah Penandatangan</button>
                </div>
                <div class="col-12 mb-2" id="selected-signature-list"></div>
                <div id="signature-inputs"></div>

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
        <input type="text" id="paraf-search" class="form-control mb-3" placeholder="Cari nama / jabatan lengkap / departemen / divisi / direktorat">
        <div class="table-responsive">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Nama</th>
                <th style="min-width: 200px;">Jabatan Lengkap</th>
                <th>Departemen</th>
                <th>Divisi</th>
                <th>Direktorat</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($users as $user)
              <tr data-name="{{ $user->name }}" 
                  data-jabatan="{{ $user->jabatan_full ?? '' }}" 
                  data-department="{{ $user->department->name ?? '' }}" 
                  data-division="{{ $user->division->name ?? '' }}" 
                  data-directorate="{{ $user->directorate->name ?? '' }}">
                <td>{{ $user->name }}</td>
                <td style="min-width: 200px;">{{ $user->jabatan_full ?? '-' }}</td>
                <td>{{ $user->department->name ?? '-' }}</td>
                <td>{{ $user->division->name ?? '-' }}</td>
                <td>{{ $user->directorate->name ?? '-' }}</td>
                <td>
                  <button type="button" class="btn btn-sm btn-warning" 
                    onclick='addToList(@json([
                      "id" => $user->id, 
                      "name" => $user->name, 
                      "jabatan_full" => $user->jabatan_full ?? "-"
                    ]), "paraf")'>
                    Tambah
                  </button>
                </td>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Signature Modal -->
<div class="modal fade" id="signatureModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pilih Penandatangan (Hanya 1)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" id="signature-search" class="form-control mb-3" placeholder="Cari nama / jabatan lengkap / departemen / divisi / direktorat">
        <div class="table-responsive">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Nama</th>
                <th style="min-width: 200px;">Jabatan Lengkap</th>
                <th>Departemen</th>
                <th>Divisi</th>
                <th>Direktorat</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($users as $user)
              <tr data-name="{{ $user->name }}" 
                  data-jabatan="{{ $user->jabatan_full ?? '' }}" 
                  data-department="{{ $user->department->name ?? '' }}" 
                  data-division="{{ $user->division->name ?? '' }}" 
                  data-directorate="{{ $user->directorate->name ?? '' }}">
                <td>{{ $user->name }}</td>
                <td style="min-width: 200px;">{{ $user->jabatan_full ?? '-' }}</td>
                <td>{{ $user->department->name ?? '-' }}</td>
                <td>{{ $user->division->name ?? '-' }}</td>
                <td>{{ $user->directorate->name ?? '-' }}</td>
                <td>
                  <button type="button" class="btn btn-sm btn-success" 
                    onclick='addToList(@json([
                      "id" => $user->id, 
                      "name" => $user->name, 
                      "jabatan_full" => $user->jabatan_full ?? "-"
                    ]), "signature")'>
                    Tambah
                  </button>
                </td>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
