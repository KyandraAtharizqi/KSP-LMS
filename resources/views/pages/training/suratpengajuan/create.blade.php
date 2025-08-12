@extends('layout.main')

@push('script')
<script>
    let selectedParticipants = [];
    let selectedParafs = [];
    let selectedSignature1 = [];
    let selectedSignature2 = [];
    let selectedSignature3 = [];

    function renderList(arr, containerId, inputName, inputContainerId, color) {
        const container = document.getElementById(containerId);
        const inputContainer = document.getElementById(inputContainerId);
        container.innerHTML = '';
        inputContainer.innerHTML = '';

        arr.forEach(user => {
            const tag = document.createElement('div');
            tag.className = `badge bg-${color} me-1 mb-1 d-inline-flex align-items-center`;
            tag.innerHTML = `${user.name} (${user.registration_id})
                <button type="button" class="btn-close btn-sm ms-2" onclick="removeFromList('${user.registration_id}', '${containerId}')"></button>`;
            container.appendChild(tag);

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = inputName + '[]';
            input.value = user.registration_id;
            inputContainer.appendChild(input);
        });
    }

    function removeFromList(registrationId, listType) {
        if (listType === 'selected-participant-list') {
            selectedParticipants = selectedParticipants.filter(p => p.registration_id !== registrationId);
            renderList(selectedParticipants, 'selected-participant-list', 'participants', 'participant-inputs', 'primary');
        } else if (listType === 'selected-paraf-list') {
            selectedParafs = selectedParafs.filter(p => p.registration_id !== registrationId);
            renderList(selectedParafs, 'selected-paraf-list', 'parafs', 'paraf-inputs', 'warning text-dark');
        } else if (listType === 'selected-signature1-list') {
            selectedSignature1 = selectedSignature1.filter(p => p.registration_id !== registrationId);
            renderList(selectedSignature1, 'selected-signature1-list', 'signatures', 'signature1-inputs', 'success');
        } else if (listType === 'selected-signature2-list') {
            selectedSignature2 = [];
            renderList([], 'selected-signature2-list', 'signature2', 'signature2-inputs', 'info');
        } else if (listType === 'selected-signature3-list') {
            selectedSignature3 = [];
            renderList([], 'selected-signature3-list', 'signature3', 'signature3-inputs', 'info');
        }
    }

    function addToList(user, listType) {
        if (listType === 'paraf' && selectedParafs.length >= 3) {
            alert('Maksimal 3 orang boleh dipilih sebagai paraf.');
            return;
        }

        if (listType === 'participant') {
            if (!selectedParticipants.find(p => p.registration_id === user.registration_id)) {
                selectedParticipants.push(user);
            }
            renderList(selectedParticipants, 'selected-participant-list', 'participants', 'participant-inputs', 'primary');
            bootstrap.Modal.getInstance(document.getElementById('participantModal')).hide();
        } else if (listType === 'paraf') {
            if (!selectedParafs.find(p => p.registration_id === user.registration_id)) {
                selectedParafs.push(user);
            }
            renderList(selectedParafs, 'selected-paraf-list', 'parafs', 'paraf-inputs', 'warning text-dark');
            bootstrap.Modal.getInstance(document.getElementById('parafModal')).hide();
        } else if (listType === 'signature') {
            if (!selectedSignature1.find(p => p.registration_id === user.registration_id)) {
                selectedSignature1 = [user]; // Only one allowed
            }
            renderList(selectedSignature1, 'selected-signature1-list', 'signatures', 'signature1-inputs', 'success');
            bootstrap.Modal.getInstance(document.getElementById('signatureModal')).hide();
        } else if (listType === 'signature2') {
            selectedSignature2 = [user];
            renderList(selectedSignature2, 'selected-signature2-list', 'signature2', 'signature2-inputs', 'info');
            bootstrap.Modal.getInstance(document.getElementById('signature2Modal')).hide();
        } else if (listType === 'signature3') {
            selectedSignature3 = [user];
            renderList(selectedSignature3, 'selected-signature3-list', 'signature3', 'signature3-inputs', 'info');
            bootstrap.Modal.getInstance(document.getElementById('signature3Modal')).hide();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const searchInputs = [
            { inputId: 'participant-search', tableSelector: '#participant-table tbody tr' },
            { inputId: 'paraf-search', tableSelector: '#parafModal table tbody tr' },
            { inputId: 'signature-search', tableSelector: '#signatureModal table tbody tr' },
            { inputId: 'signature2-search', tableSelector: '#signature2Modal table tbody tr' },
            { inputId: 'signature3-search', tableSelector: '#signature3Modal table tbody tr' }
        ];

        searchInputs.forEach(({ inputId, tableSelector }) => {
            const input = document.getElementById(inputId);
            const rows = document.querySelectorAll(tableSelector);
            input.addEventListener('input', function () {
                const keyword = input.value.toLowerCase();
                rows.forEach(row => {
                    const name = row.dataset.name.toLowerCase();
                    const dept = row.dataset.department.toLowerCase();
                    const jabatan = row.dataset.jabatan.toLowerCase();
                    row.style.display = (name.includes(keyword) || dept.includes(keyword) || jabatan.includes(keyword)) ? '' : 'none';
                });
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
@endpush



@section('content')
<div class="page-heading"><h3>Form Pengajuan Pelatihan</h3></div>
<div class="page-content">
    <form action="{{ route('training.suratpengajuan.store') }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-body row">
                <div class="col-md-6 mb-3">
                    <label>Kode Pelatihan</label>
                    <input type="text" name="kode_pelatihan" class="form-control" required>
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
                    <label>Kompetensi Wajib/Tidak Wajib</label>
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

                <!-- Participant -->
                <hr><h5>Peserta</h5>
                <div class="col-12 mb-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#participantModal">+ Tambah Peserta</button>
                </div>
                <div class="col-12 mb-2" id="selected-participant-list"></div>
                <div id="participant-inputs"></div>

                <!-- Paraf -->
                <hr><h5>Paraf</h5>
                <div class="col-12 mb-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#parafModal">+ Tambah Paraf</button>
                </div>
                <div class="col-12 mb-2" id="selected-paraf-list"></div>
                <div id="paraf-inputs"></div>

                <!-- Signatures (1, 2, 3 in one list but shown separately) -->
                <hr><h5>Penandatangan</h5>

                <!-- Signature 1 -->
                <h6>Signature 1</h6>
                <div class="col-12 mb-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#signatureModal">+ Tambah Penandatangan</button>
                </div>
                <div class="col-12 mb-2" id="selected-signature1-list"></div>
                <div id="signature1-inputs"></div>

                <!-- Signature 2 -->
                <h6 class="mt-3">Signature 2</h6>
                <p class="text-muted">Pilih <strong>Human Capital Manager</strong> sebagai penandatangan kedua.</p>
                <div class="col-12 mb-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#signature2Modal">+ Tambah Signature 2</button>
                </div>
                <div class="col-12 mb-2" id="selected-signature2-list"></div>
                <div id="signature2-inputs"></div>

                <!-- Signature 3 -->
                <h6 class="mt-3">Signature 3</h6>
                <p class="text-muted">Pilih <strong>Director HC & Finance</strong> sebagai penandatangan ketiga.</p>
                <div class="col-12 mb-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#signature3Modal">+ Tambah Signature 3</button>
                </div>
                <div class="col-12 mb-2" id="selected-signature3-list"></div>
                <div id="signature3-inputs"></div>

                <!-- Submit Button -->
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">Ajukan Surat</button>
                </div>


            </div>
        </div>
    </form>
</div>

<!-- Participant Modal -->
<div class="modal fade" id="participantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Peserta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="participant-search" class="form-control mb-3" placeholder="Cari nama / jabatan / departemen">
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
                                        onclick='addToList(@json(["registration_id" => $user->registration_id, "name" => $user->name]), "participant")'>
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
                  onclick='addToList(@json(["registration_id" => $user->registration_id, "name" => $user->name]), "paraf")'>
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


<!-- Signature 1 Modal -->
<div class="modal fade" id="signatureModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pilih Penandatangan (Signature 1)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" id="signature-search" class="form-control mb-3" placeholder="Cari nama / jabatan / departemen">
        <table class="table table-sm">
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
                  onclick='addToList(@json(["registration_id" => $user->registration_id, "name" => $user->name]), "signature")'>
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

<!-- Signature 2 Modal -->
<div class="modal fade" id="signature2Modal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pilih Signature 2 (Human Capital Manager)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" id="signature2-search" class="form-control mb-3" placeholder="Cari nama / jabatan / departemen">
        <table class="table table-sm">
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
                  onclick='addToList(@json(["registration_id" => $user->registration_id, "name" => $user->name]), "signature2")'>
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

<!-- Signature 3 Modal -->
<div class="modal fade" id="signature3Modal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pilih Signature 3 (Director HC & Finance)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" id="signature3-search" class="form-control mb-3" placeholder="Cari nama / jabatan / departemen">
        <table class="table table-sm">
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
                  onclick='addToList(@json(["registration_id" => $user->registration_id, "name" => $user->name]), "signature3")'>
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
