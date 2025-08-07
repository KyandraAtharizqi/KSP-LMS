@extends('layout.main')

@section('title', 'Tambah Nota Dinas')

@section('content')
<div class="page-heading">
    <h3>Tambah Nota Dinas</h3>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('knowledge.notadinas.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="kode" class="form-label">No. Nota</label>
                    <input type="text" class="form-control" id="kode" name="kode"
                        value="{{ old('kode') }}" placeholder="Contoh: IF/1/2025" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kepada Yth</label>
                    
                    <div class="input-group">
                        <input type="text" class="form-control" id="kepada_display" placeholder="Pilih penerima..." readonly required>
                        <input type="hidden" name="kepada" id="kepada">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalPilihPenerima">
                            Pilih Penerima
                        </button>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="modalPilihPenerima" tabindex="-1" aria-labelledby="modalPilihPenerimaLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalPilihPenerimaLabel">Pilih Peserta</h5>
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
                    <label for="lampiran" class="form-label">Lampiran (PDF)</label>
                    <input type="file" class="form-control" id="lampiran" name="lampiran" accept="application/pdf">
                </div>

                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ old('tanggal') }}" required>
                </div>

                <button type="submit" class="btn btn-success">Simpan</button>
                <a href="{{ route('knowledge.notadinas.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('script')
<script>
    $(document).ready(function () {
        // Cari realtime
        $('#searchUser').on('keyup', function () {
            var value = $(this).val().toLowerCase();
            $('#userList tr').filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // Pilih penerima
        $('.btn-pilih').on('click', function () {
        let nama = $(this).data('nama');
        $('#kepada_display').val(nama);  // terlihat
        $('#kepada').val(nama);          // untuk dikirim ke backend
        $('#modalPilihPenerima').modal('hide');
        });
    });
</script>
@endpush




