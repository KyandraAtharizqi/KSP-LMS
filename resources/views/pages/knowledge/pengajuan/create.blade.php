@extends('layout.main')

@section('title', 'Tambah Pengajuan Knowledge Sharing')

@section('content')
<div class="page-heading">
    <h3>Tambah Pengajuan Knowledge Sharing</h3>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('knowledge.pengajuan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="kode" class="form-label">No. Pengajuan</label>
                    <input type="text" class="form-control" id="kode" name="kode"
                        value="{{ old('kode') }}" placeholder="Contoh: IF/1/2025" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Penerima (Untuk Approve / Reject)</label>
                    
                    <div class="input-group">
                        <input type="text" class="form-control" id="kepada_display" placeholder="Pilih penerima..." readonly required>
                        <input type="hidden" name="kepada" id="kepada">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalPilihPenerima">
                            Pilih Penerima
                        </button>
                    </div>
                </div>

                <!-- Modal Pilih Penerima -->
                <div class="modal fade" id="modalPilihPenerima" tabindex="-1" aria-labelledby="modalPilihPenerimaLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalPilihPenerimaLabel">Pilih Penerima</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">

                                <input type="text" class="form-control mb-3" id="searchUser" placeholder="Cari nama / jabatan / departemen">

                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Jabatan</th>
                                            <th>Departemen</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="userList">
                                        @foreach ($users as $user)
                                            <tr>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->jabatan_full ?? '-' }}</td>
                                                <td>{{ $user->department->name ?? '-' }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-success btn-pilih" 
                                                        data-nama="{{ $user->name }}">
                                                        Pilih
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

                <div class="mb-3">
                    <label for="dari" class="form-label">Dari</label>
                    <input type="text" class="form-control" id="dari" name="dari" 
                        value="{{ auth()->user()->name }}" readonly>
                </div>

                <div class="mb-3">
                    <label for="perihal" class="form-label">Perihal</label>
                    <input type="text" class="form-control" id="perihal" name="perihal" value="{{ old('perihal') }}" required>
                </div>

                <div class="mb-3">
                    <label for="judul" class="form-label">Judul</label>
                    <input type="text" class="form-control" id="judul" name="judul" value="{{ old('judul') }}" required>
                </div>

                <div class="mb-3">
                    <label for="pemateri" class="form-label">Pemateri</label>
                    <input type="text" class="form-control" id="pemateri" name="pemateri"
                        value="{{ old('pemateri') }}" placeholder="Bpk/Ibu" required>
                </div>

                <div class="mb-3">
                    <label for="lampiran" class="form-label">Lampiran Materi (PDF)</label>
                    <input type="file" class="form-control" id="lampiran" name="lampiran" accept="application/pdf">
                </div>

                {{-- Tanggal dan Waktu --}}
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" 
                            value="{{ old('tanggal_mulai') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai"
                            value="{{ old('tanggal_selesai') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="waktu_mulai" class="form-label">Jam Mulai</label>
                        <input type="time" class="form-control" id="waktu_mulai" name="waktu_mulai"
                            value="{{ old('waktu_mulai', '09:00') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="waktu_selesai" class="form-label">Jam Selesai</label>
                        <input type="time" class="form-control" id="waktu_selesai" name="waktu_selesai"
                            value="{{ old('waktu_selesai', '16:00') }}" required>
                    </div>
                </div>

                <!-- Peserta Section -->
                <div class="mb-3">
                    <label class="form-label">Peserta</label>
                    <div class="col-12 mb-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#participantModal">
                            + Tambah Peserta
                        </button>
                    </div>
                    <div class="col-12 mb-2" id="selected-participant-list"></div>
                    <div id="participant-inputs"></div>
                </div>

                <button type="submit" class="btn btn-success">Simpan</button>
                <a href="{{ route('knowledge.pengajuan.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<!-- Modal Peserta -->
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
                                        onclick="addToList({
                                            name: '{{ $user->name }}',
                                            registration_id: '{{ $user->registration_id }}',
                                            jabatan: '{{ $user->jabatan->name ?? '-' }}',
                                            department: '{{ $user->department->name ?? '-' }}'
                                        }, 'participant')">
                                        Pilih
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
@endsection

@push('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('script')
<script>
    let selectedParticipants = [];

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
        }
    }

    function addToList(user, listType) {
        if (listType === 'participant') {
            if (!selectedParticipants.find(p => p.registration_id === user.registration_id)) {
                selectedParticipants.push(user);
            }
            renderList(selectedParticipants, 'selected-participant-list', 'participants', 'participant-inputs', 'primary');
            bootstrap.Modal.getInstance(document.getElementById('participantModal')).hide();
        }
    }

    $(document).ready(function () {
        // Cari realtime untuk penerima
        $('#searchUser').on('keyup', function () {
            var value = $(this).val().toLowerCase();
            $('#userList tr').filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // Pilih penerima
        $('.btn-pilih').on('click', function () {
            let nama = $(this).data('nama');
            $('#kepada_display').val(nama);  // terlihat di form
            $('#kepada').val(nama);          // dikirim ke backend
            $('#modalPilihPenerima').modal('hide');
        });

        // Cari realtime untuk peserta
        $('#participant-search').on('keyup', function () {
            var value = $(this).val().toLowerCase();
            $('#participant-table tbody tr').filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
@endpush
