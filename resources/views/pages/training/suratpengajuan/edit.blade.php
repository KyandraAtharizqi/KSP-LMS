@extends('layout.main')

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

@push('script')
@php
    $participantsData = $surat->participants->map(function($p) {
        return [
            'registration_id' => $p->registration_id,
            'name' => $p->user->name,
            'jabatan_full' => $p->user->jabatan_full ?? ($p->user->jabatan->name ?? '-'),
        ];
    })->values();

    $maxRound = $surat->approvals->max('round') ?? 1;
    
    $parafsData = $surat->approvals
        ->where('round', $maxRound)
        ->where('type', 'paraf')
        ->map(function($a) {
            return [
                'registration_id' => $a->registration_id,
                'name' => $a->user->name,
                'jabatan_full' => $a->user->jabatan_full ?? ($a->user->jabatan->name ?? '-'),
            ];
        })->values();

    $signature1Data = $surat->approvals
        ->where('round', $maxRound)
        ->where('type', 'signature')
        ->slice(0, 1)
        ->map(function($a) {
            return [
                'registration_id' => $a->registration_id,
                'name' => $a->user->name,
                'jabatan_full' => $a->user->jabatan_full ?? ($a->user->jabatan->name ?? '-'),
            ];
        })->values();

    $signature2Data = $surat->approvals
        ->where('round', $maxRound)
        ->where('type', 'signature')
        ->slice(1, 1)
        ->map(function($a) {
            return [
                'registration_id' => $a->registration_id,
                'name' => $a->user->name,
                'jabatan_full' => $a->user->jabatan_full ?? ($a->user->jabatan->name ?? '-'),
            ];
        })->values();

    $signature3Data = $surat->approvals
        ->where('round', $maxRound)
        ->where('type', 'signature')
        ->slice(2, 1)
        ->map(function($a) {
            return [
                'registration_id' => $a->registration_id,
                'name' => $a->user->name,
                'jabatan_full' => $a->user->jabatan_full ?? ($a->user->jabatan->name ?? '-'),
            ];
        })->values();
@endphp

<script>
    let selectedParticipants = [];
    let selectedParafs = [];
    let selectedSignature1 = [];
    let selectedSignature2 = [];
    let selectedSignature3 = [];

    function renderList(arr, containerId, inputName, inputContainerId, colorClass) {
        const container = document.getElementById(containerId);
        const inputContainer = document.getElementById(inputContainerId);
        container.innerHTML = '';
        inputContainer.innerHTML = '';

        arr.forEach(user => {
            const tag = document.createElement('div');
            tag.className = `badge ${colorClass} me-1 mb-1 d-inline-flex align-items-center`;
            tag.innerHTML = `${user.name} (${user.registration_id}) - ${user.jabatan_full}
                <button type="button" class="btn-close btn-sm ms-2" 
                        onclick="removeFromList('${user.registration_id}', '${containerId}')">
                </button>`;
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
            renderList(selectedParticipants, 'selected-participant-list', 'participants', 'participant-inputs', 'bg-primary');
        } else if (listType === 'selected-paraf-list') {
            selectedParafs = selectedParafs.filter(p => p.registration_id !== registrationId);
            renderList(selectedParafs, 'selected-paraf-list', 'parafs', 'paraf-inputs', 'bg-warning text-dark');
        } else if (listType === 'selected-signature1-list') {
            selectedSignature1 = selectedSignature1.filter(p => p.registration_id !== registrationId);
            renderList(selectedSignature1, 'selected-signature1-list', 'signatures', 'signature1-inputs', 'bg-success');
        } else if (listType === 'selected-signature2-list') {
            selectedSignature2 = [];
            renderList([], 'selected-signature2-list', 'signature2', 'signature2-inputs', 'bg-info');
        } else if (listType === 'selected-signature3-list') {
            selectedSignature3 = [];
            renderList([], 'selected-signature3-list', 'signature3', 'signature3-inputs', 'bg-secondary');
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
            renderList(selectedParticipants, 'selected-participant-list', 'participants', 'participant-inputs', 'bg-primary');
            bootstrap.Modal.getInstance(document.getElementById('participantModal')).hide();
        } else if (listType === 'paraf') {
            if (!selectedParafs.find(p => p.registration_id === user.registration_id)) {
                selectedParafs.push(user);
            }
            renderList(selectedParafs, 'selected-paraf-list', 'parafs', 'paraf-inputs', 'bg-warning text-dark');
            bootstrap.Modal.getInstance(document.getElementById('parafModal')).hide();
        } else if (listType === 'signature') {
            if (!selectedSignature1.find(p => p.registration_id === user.registration_id)) {
                selectedSignature1 = [user]; // Only one allowed
            }
            renderList(selectedSignature1, 'selected-signature1-list', 'signatures', 'signature1-inputs', 'bg-success');
            bootstrap.Modal.getInstance(document.getElementById('signatureModal')).hide();
        } else if (listType === 'signature2') {
            selectedSignature2 = [user];
            renderList(selectedSignature2, 'selected-signature2-list', 'signature2', 'signature2-inputs', 'bg-info');
            bootstrap.Modal.getInstance(document.getElementById('signature2Modal')).hide();
        } else if (listType === 'signature3') {
            selectedSignature3 = [user];
            renderList(selectedSignature3, 'selected-signature3-list', 'signature3', 'signature3-inputs', 'bg-secondary');
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
<div class="page-heading"><h3>Edit Surat Pengajuan Pelatihan</h3></div>

@if ($latestRejection)
    <div class="alert alert-danger">
        <strong>❗ Surat Ditolak!</strong><br>
        Round {{ $latestRejection->round }}, Seq {{ $latestRejection->sequence }} – "{{ $latestRejection->rejection_reason }}"
    </div>
@endif

<div class="page-content">
    <form action="{{ route('training.suratpengajuan.update', $surat->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="card">
            <div class="card-body row">
                {{-- Kode Pelatihan --}}
                <div class="col-md-6 mb-3">
                    <label>Kode Pelatihan</label>
                    <input type="text" name="kode_pelatihan" class="form-control" value="{{ $surat->kode_pelatihan }}" readonly>
                </div>

                {{-- Kompetensi --}}
                <div class="col-md-6 mb-3">
                    <label>Kompetensi</label>
                    <input type="text" name="kompetensi" value="{{ old('kompetensi', $surat->kompetensi) }}" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Judul</label>
                    <input type="text" name="judul" class="form-control" value="{{ old('judul', $surat->judul) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Lokasi</label>
                    <select name="lokasi" class="form-control" required>
                        <option value="Perusahaan" {{ old('lokasi', $surat->lokasi) == 'Perusahaan' ? 'selected' : '' }}>Perusahaan</option>
                        <option value="Didalam Kota" {{ old('lokasi', $surat->lokasi) == 'Didalam Kota' ? 'selected' : '' }}>Didalam Kota</option>
                        <option value="Diluar Kota" {{ old('lokasi', $surat->lokasi) == 'Diluar Kota' ? 'selected' : '' }}>Diluar Kota</option>
                        <option value="Diluar Negeri" {{ old('lokasi', $surat->lokasi) == 'Diluar Negeri' ? 'selected' : '' }}>Diluar Negeri</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Instruktur</label>
                    <select name="instruktur" class="form-control" required>
                        <option value="Internal" {{ old('instruktur', $surat->instruktur) == 'Internal' ? 'selected' : '' }}>Internal</option>
                        <option value="Eksternal" {{ old('instruktur', $surat->instruktur) == 'Eksternal' ? 'selected' : '' }}>Eksternal</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Sifat</label>
                    <select name="sifat" class="form-control" required>
                        <option value="Seminar" {{ old('sifat', $surat->sifat) == 'Seminar' ? 'selected' : '' }}>Seminar</option>
                        <option value="Kursus" {{ old('sifat', $surat->sifat) == 'Kursus' ? 'selected' : '' }}>Kursus</option>
                        <option value="Sertifikasi" {{ old('sifat', $surat->sifat) == 'Sertifikasi' ? 'selected' : '' }}>Sertifikasi</option>
                        <option value="Workshop" {{ old('sifat', $surat->sifat) == 'Workshop' ? 'selected' : '' }}>Workshop</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Kompetensi Wajib</label>
                    <select name="kompetensi_wajib" class="form-control" required>
                        <option value="Wajib" {{ old('kompetensi_wajib', $surat->kompetensi_wajib) == 'Wajib' ? 'selected' : '' }}>Wajib</option>
                        <option value="Tidak Wajib" {{ old('kompetensi_wajib', $surat->kompetensi_wajib) == 'Tidak Wajib' ? 'selected' : '' }}>Tidak Wajib</option>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Materi Global</label>
                    <textarea name="materi_global" class="form-control" required>{{ old('materi_global', $surat->materi_global) }}</textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Program Pelatihan KSP</label>
                    <select name="program_pelatihan_ksp" class="form-control" required>
                        <option value="Termasuk" {{ old('program_pelatihan_ksp', $surat->program_pelatihan_ksp) == 'Termasuk' ? 'selected' : '' }}>Termasuk</option>
                        <option value="Tidak Termasuk" {{ old('program_pelatihan_ksp', $surat->program_pelatihan_ksp) == 'Tidak Termasuk' ? 'selected' : '' }}>Tidak Termasuk</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai', $surat->tanggal_mulai ? $surat->tanggal_mulai->format('Y-m-d') : '') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai', $surat->tanggal_selesai ? $surat->tanggal_selesai->format('Y-m-d') : '') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Durasi (hari)</label>
                    <input type="text" id="durasi" class="form-control" value="{{ old('durasi', $surat->durasi) }}" readonly>
                    <input type="hidden" name="durasi" id="durasi-hidden" value="{{ old('durasi', $surat->durasi) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Tempat</label>
                    <input type="text" name="tempat" class="form-control" value="{{ old('tempat', $surat->tempat) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Penyelenggara</label>
                    <input type="text" name="penyelenggara" class="form-control" value="{{ old('penyelenggara', $surat->penyelenggara) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Biaya</label>
                    <input type="text" name="biaya" class="form-control" value="{{ old('biaya', $surat->biaya) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Per Paket/Orang</label>
                    <select name="per_paket_or_orang" class="form-control" required>
                        <option value="Paket" {{ old('per_paket_or_orang', $surat->per_paket_or_orang) == 'Paket' ? 'selected' : '' }}>Paket</option>
                        <option value="Orang" {{ old('per_paket_or_orang', $surat->per_paket_or_orang) == 'Orang' ? 'selected' : '' }}>Orang</option>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control">{{ old('keterangan', $surat->keterangan) }}</textarea>
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
                    <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#parafModal">+ Tambah Paraf</button>
                </div>
                <div class="col-12 mb-2" id="selected-paraf-list"></div>
                <div id="paraf-inputs"></div>

                <!-- Signatures (1, 2, 3 in one list but shown separately) -->
                <hr><h5>Penandatangan</h5>

                <!-- Signature 1 -->
                <h6>Signature 1</h6>
                <div class="col-12 mb-2">
                    <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#signatureModal">+ Tambah Signature 1</button>
                </div>
                <div class="col-12 mb-2" id="selected-signature1-list"></div>
                <div id="signature1-inputs"></div>

                <!-- Signature 2 -->
                <h6 class="mt-3">Signature 2</h6>
                <p class="text-muted">Pilih <strong>Human Capital Manager</strong> sebagai penandatangan kedua.</p>
                <div class="col-12 mb-2">
                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#signature2Modal">+ Tambah Signature 2</button>
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

                {{-- Submit --}}
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">Submit Perubahan & Ajukan Ulang</button>
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
                                <th>Registration ID</th>
                                <th>Jabatan Lengkap</th>
                                <th>Departemen</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr data-name="{{ $user->name }}" 
                                data-department="{{ $user->department->name ?? '' }}" 
                                data-jabatan="{{ $user->jabatan->name ?? '' }}">
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->registration_id }}</td>
                                <td>{{ $user->jabatan_full ?? ($user->jabatan->name ?? '-') }}</td>
                                <td>{{ $user->department->name ?? '-' }}</td>
                                <td>
                                  <button type="button" class="btn btn-sm btn-success"
                                      onclick='addToList(@json([
                                          "registration_id" => $user->registration_id,
                                          "name" => $user->name,
                                          "jabatan_full" => $user->jabatan_full ?? ($user->jabatan->name ?? "-")
                                      ]), "participant")'>
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
                <button type="button" class="btn btn-sm btn-success"
                    onclick='addToList(@json([
                        "registration_id" => $user->registration_id,
                        "name" => $user->name,
                        "jabatan_full" => $user->jabatan_full ?? ($user->jabatan->name ?? "-")
                    ]), "paraf")'>
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
                <button type="button" class="btn btn-sm btn-success"
                    onclick='addToList(@json([
                        "registration_id" => $user->registration_id,
                        "name" => $user->name,
                        "jabatan_full" => $user->jabatan_full ?? ($user->jabatan->name ?? "-")
                    ]), "signature")'>
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
                <button type="button" class="btn btn-sm btn-success"
                    onclick='addToList(@json([
                        "registration_id" => $user->registration_id,
                        "name" => $user->name,
                        "jabatan_full" => $user->jabatan_full ?? ($user->jabatan->name ?? "-")
                    ]), "signature2")'>
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
                <button type="button" class="btn btn-sm btn-success"
                    onclick='addToList(@json([
                        "registration_id" => $user->registration_id,
                        "name" => $user->name,
                        "jabatan_full" => $user->jabatan_full ?? ($user->jabatan->name ?? "-")
                    ]), "signature3")'>
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