@extends('layout.main')

@push('script')
<script>
    let selectedParticipants = [];

    function renderParticipantList() {
        const listContainer = document.getElementById('selected-participant-list');
        const inputContainer = document.getElementById('participant-inputs');

        listContainer.innerHTML = '';
        inputContainer.innerHTML = '';

        selectedParticipants.forEach(user => {
            const tag = document.createElement('div');
            tag.className = 'badge bg-primary me-1 mb-1 d-inline-flex align-items-center';
            tag.innerHTML = `${user.name} (${user.registration_id}) 
                <button type="button" class="btn-close btn-sm ms-2" onclick="removeParticipant('${user.registration_id}')"></button>`;
            listContainer.appendChild(tag);

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'participants[]';
            input.value = user.registration_id;
            inputContainer.appendChild(input);
        });
    }

    function removeParticipant(registrationId) {
        selectedParticipants = selectedParticipants.filter(p => p.registration_id !== registrationId);
        renderParticipantList();
    }

    function addParticipant(user) {
        if (!selectedParticipants.find(p => p.registration_id === user.registration_id)) {
            selectedParticipants.push(user);
            renderParticipantList();
        }
        const modal = bootstrap.Modal.getInstance(document.getElementById('participantModal'));
        modal.hide();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('participant-search');
        const tableRows = document.querySelectorAll('#participant-table tbody tr');

        searchInput.addEventListener('input', function () {
            const keyword = searchInput.value.toLowerCase();
            tableRows.forEach(row => {
                const name = row.dataset.name.toLowerCase();
                const dept = row.dataset.department.toLowerCase();
                const jabatan = row.dataset.jabatan.toLowerCase();
                row.style.display = (name.includes(keyword) || dept.includes(keyword) || jabatan.includes(keyword)) 
                    ? '' : 'none';
            });
        });

        const start = document.querySelector('[name="tanggal_mulai"]');
        const end = document.querySelector('[name="tanggal_selesai"]');
        const durasi = document.querySelector('#durasi');

        function updateDurasi() {
            const startDate = new Date(start.value);
            const endDate = new Date(end.value);
            if (startDate && endDate && endDate >= startDate) {
                const diff = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
                durasi.value = diff;
                document.getElementById('durasi-hidden').value = diff;
            } else {
                durasi.value = '';
                document.getElementById('durasi-hidden').value = '';
            }
        }

        start.addEventListener('change', updateDurasi);
        end.addEventListener('change', updateDurasi);
    });
</script>
<script>
    let selectedParafs = [];
    let selectedSignatures = [];

    function renderParafList() {
        const container = document.getElementById('selected-paraf-list');
        const inputContainer = document.getElementById('paraf-inputs');
        container.innerHTML = '';
        inputContainer.innerHTML = '';

        selectedParafs.forEach(user => {
            const tag = document.createElement('div');
            tag.className = 'badge bg-warning text-dark me-1 mb-1 d-inline-flex align-items-center';
            tag.innerHTML = `${user.name} (${user.registration_id})
                <button type="button" class="btn-close btn-sm ms-2" onclick="removeParaf('${user.registration_id}')"></button>`;
            container.appendChild(tag);

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'parafs[]';
            input.value = user.registration_id;
            inputContainer.appendChild(input);
        });
    }

    function addParaf(user) {
        if (selectedParafs.length >= 3) {
            alert('Maksimal 3 orang boleh dipilih sebagai paraf.');
            return;
        }
        if (!selectedParafs.find(u => u.registration_id === user.registration_id)) {
            selectedParafs.push(user);
            renderParafList();
        }
        bootstrap.Modal.getInstance(document.getElementById('parafModal')).hide();
    }

    function removeParaf(regId) {
        selectedParafs = selectedParafs.filter(u => u.registration_id !== regId);
        renderParafList();
    }

    function renderSignatureList() {
        const container = document.getElementById('selected-signature-list');
        const inputContainer = document.getElementById('signature-inputs');
        container.innerHTML = '';
        inputContainer.innerHTML = '';

        selectedSignatures.forEach(user => {
            const tag = document.createElement('div');
            tag.className = 'badge bg-success me-1 mb-1 d-inline-flex align-items-center';
            tag.innerHTML = `${user.name} (${user.registration_id})
                <button type="button" class="btn-close btn-sm ms-2" onclick="removeSignature('${user.registration_id}')"></button>`;
            container.appendChild(tag);

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'signatures[]';
            input.value = user.registration_id;
            inputContainer.appendChild(input);
        });
    }

    function addSignature(user) {
        if (!selectedSignatures.find(u => u.registration_id === user.registration_id)) {
            selectedSignatures.push(user);
            renderSignatureList();
        }
        bootstrap.Modal.getInstance(document.getElementById('signatureModal')).hide();
    }

    function removeSignature(regId) {
        selectedSignatures = selectedSignatures.filter(u => u.registration_id !== regId);
        renderSignatureList();
    }

    // Add search filter for paraf & signature
    document.addEventListener('DOMContentLoaded', function () {
        const searchConfigs = [
            { inputId: 'paraf-search', tableSelector: '#parafModal table tbody tr' },
            { inputId: 'signature-search', tableSelector: '#signatureModal table tbody tr' }
        ];

        searchConfigs.forEach(({ inputId, tableSelector }) => {
            const input = document.getElementById(inputId);
            const rows = document.querySelectorAll(tableSelector);
            input.addEventListener('input', function () {
                const keyword = input.value.toLowerCase();
                rows.forEach(row => {
                    const name = row.dataset.name.toLowerCase();
                    const dept = row.dataset.department.toLowerCase();
                    const jabatan = row.dataset.jabatan.toLowerCase();
                    row.style.display = (name.includes(keyword) || dept.includes(keyword) || jabatan.includes(keyword)) 
                        ? '' : 'none';
                });
            });
        });
    });
</script>
@endpush

@section('content')
<div class="page-heading"><h3>Form Pengajuan Pelatihan</h3></div>

<div class="page-content">
    <form action="{{ route('training.suratpengajuan.store') }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-body row">
                <div class="col-md-6 mb-3">
                    <label>ID Surat</label>
                    <input type="text" name="surat_id" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Kompetensi</label>
                    <input type="text" name="kompetensi" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Judul</label>
                    <input type="text" name="judul" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Lokasi</label>
                    <select name="lokasi" class="form-control" required>
                        <option value="Perusahaan">Perusahaan</option>
                        <option value="Didalam Kota">Didalam Kota</option>
                        <option value="Diluar Kota">Diluar Kota</option>
                        <option value="Diluar Negeri">Diluar Negeri</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Instruktur</label>
                    <select name="instruktur" class="form-control" required>
                        <option value="Internal">Internal</option>
                        <option value="Eksternal">Eksternal</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Sifat</label>
                    <select name="sifat" class="form-control" required>
                        <option value="Seminar">Seminar</option>
                        <option value="Kursus">Kursus</option>
                        <option value="Sertifikasi">Sertifikasi</option>
                        <option value="Workshop">Workshop</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Kompetensi Wajib</label>
                    <select name="kompetensi_wajib" class="form-control" required>
                        <option value="Wajib">Wajib</option>
                        <option value="Tidak Wajib">Tidak Wajib</option>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Materi Global</label>
                    <textarea name="materi_global" class="form-control" required></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Program Pelatihan KSP</label>
                    <select name="program_pelatihan_ksp" class="form-control" required>
                        <option value="Termasuk">Termasuk</option>
                        <option value="Tidak Termasuk">Tidak Termasuk</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Durasi (hari)</label>
                    <input type="text" id="durasi" class="form-control" readonly>
                    <input type="hidden" name="durasi" id="durasi-hidden">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Tempat</label>
                    <input type="text" name="tempat" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Penyelenggara</label>
                    <input type="text" name="penyelenggara" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Biaya</label>
                    <input type="text" name="biaya" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Per Paket/Orang</label>
                    <select name="per_paket_or_orang" class="form-control" required>
                        <option value="Paket">Paket</option>
                        <option value="Orang">Orang</option>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control"></textarea>
                </div>

                <hr>
                <h5>Peserta</h5>
                <div class="col-12 mb-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#participantModal">
                        + Tambah Peserta
                    </button>
                </div>
                <div class="col-12 mb-2" id="selected-participant-list"></div>
                <div id="participant-inputs"></div>

                <!-- Paraf Section -->
                <hr>
                <h5>Paraf</h5>
                <div class="col-12 mb-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#parafModal">
                        + Tambah Paraf
                    </button>
                </div>
                <div class="col-12 mb-2" id="selected-paraf-list"></div>
                <div id="paraf-inputs"></div>

                <!-- Signature Section -->
                <h5>Tanda Tangan</h5>
                <div class="col-12 mb-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#signatureModal">
                        + Tambah Penandatangan
                    </button>
                </div>
                <div class="col-12 mb-2" id="selected-signature-list"></div>
                <div id="signature-inputs"></div>


                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">Ajukan Surat</button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal -->
<div class="modal fade" id="participantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Peserta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="participant-search" class="form-control mb-3" placeholder="Cari berdasarkan nama / jabatan / departemen">
                <div class="table-responsive">
                    <table class="table table-sm" id="participant-table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Departemen</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr data-name="{{ $user->name }}" data-department="{{ $user->department->name ?? '' }}" data-jabatan="{{ $user->jabatan->name ?? '' }}">
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->jabatan->name ?? '-' }}</td>
                                <td>{{ $user->department->name ?? '-' }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-success"
                                        onclick='addParticipant(@json(["registration_id" => $user->registration_id, "name" => $user->name]))'>
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
          <tbody>
            @foreach($users as $user)
            <tr data-name="{{ $user->name }}" data-department="{{ $user->department->name ?? '' }}" data-jabatan="{{ $user->jabatan->name ?? '' }}">
              <td>{{ $user->name }}</td>
              <td>{{ $user->jabatan->name ?? '-' }}</td>
              <td>{{ $user->department->name ?? '-' }}</td>
              <td>
                <button type="button" class="btn btn-sm btn-success" 
                  onclick='addParaf(@json(["registration_id" => $user->registration_id, "name" => $user->name]))'>
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
        <h5 class="modal-title">Pilih Penandatangan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" id="signature-search" class="form-control mb-3" placeholder="Cari nama / jabatan / departemen">
        <table class="table table-sm">
          <tbody>
            @foreach($users as $user)
            <tr data-name="{{ $user->name }}" data-department="{{ $user->department->name ?? '' }}" data-jabatan="{{ $user->jabatan->name ?? '' }}">
              <td>{{ $user->name }}</td>
              <td>{{ $user->jabatan->name ?? '-' }}</td>
              <td>{{ $user->department->name ?? '-' }}</td>
              <td>
                <button type="button" class="btn btn-sm btn-success"
                  onclick='addSignature(@json(["registration_id" => $user->registration_id, "name" => $user->name]))'>
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
