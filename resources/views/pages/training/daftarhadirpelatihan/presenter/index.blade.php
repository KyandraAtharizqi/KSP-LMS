@extends('layout.main')

@section('title', 'Kelola Presenter')

@push('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container {
        z-index: 9999 !important;
    }
</style>
@endpush

@section('content')
<div class="container">
    <h2 class="mb-4">Kelola Presenter - {{ $pelatihan->judul }}</h2>

    @foreach ($pelatihan->daftarHadirStatus as $status)
        @php
            $dateKey = $status->date->format('Y-m-d');
            $assignedForDate = $assigned[$dateKey] ?? collect();
            $isDateLocked = $assignedForDate->isNotEmpty() && $assignedForDate->every(fn ($p) => $p->is_submitted);
        @endphp

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Tanggal: {{ $status->formattedDate() }}</strong>
                @if (!$isDateLocked)
                    <button class="btn btn-sm btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#assignPresenterModal"
                        data-date="{{ $dateKey }}">
                        + Tambah Presenter
                    </button>
                @endif
            </div>

            @if ($assignedForDate->count())
            <form action="{{ route('training.daftarhadirpelatihan.presenter.submit', [$pelatihan->id, $dateKey]) }}" method="POST">
                @csrf
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Tipe</th>
                                <th>Instansi</th>
                                <th>ID Registrasi</th>
                                <th>Kontak</th>
                                <th>Check-In</th>
                                <th>Check-Out</th>
                                @if (!$isDateLocked)
                                    <th>Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($assignedForDate as $item)
                            <tr>
                                <td>{{ $item->type === 'internal' ? $item->user->name : $item->presenter->name }}</td>
                                <td><span class="badge bg-info text-dark">{{ ucfirst($item->type) }}</span></td>
                                <td>{{ $item->type === 'internal' ? '-' : $item->presenter->institution ?? '-' }}</td>
                                <td>{{ $item->type === 'internal' ? $item->user->registration_id : '-' }}</td>
                                <td>{{ $item->type === 'internal' ? $item->user->email : $item->presenter->email }}</td>
                                <td>
                                    <input type="time" name="check_in_time[{{ $item->id }}]" class="form-control"
                                        value="{{ $item->check_in_time }}" @if($isDateLocked) disabled @endif>
                                </td>
                                <td>
                                    <input type="time" name="check_out_time[{{ $item->id }}]" class="form-control"
                                        value="{{ $item->check_out_time }}" @if($isDateLocked) disabled @endif>
                                </td>
                                @if (!$isDateLocked)
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger delete-presenter"
                                        data-date="{{ $dateKey }}"
                                        data-type="{{ $item->type }}"
                                        data-id="{{ $item->type === 'internal' ? $item->user_id : $item->presenter_id }}">
                                        Hapus
                                    </button>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if (!$isDateLocked)
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-success">Submit Final</button>
                </div>
                @else
                <div class="card-footer text-muted">
                    Terkunci pada {{ optional($assignedForDate->first()->submitted_at)->translatedFormat('d M Y H:i') ?? '-' }}
                </div>
                @endif
            </form>
            @else
            <div class="card-body text-muted">Belum ada presenter ditambahkan.</div>
            @endif
        </div>
    @endforeach
</div>

{{-- Modal Tambah Presenter --}}
<div class="modal fade" id="assignPresenterModal" tabindex="-1" aria-labelledby="assignPresenterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('training.daftarhadirpelatihan.presenter.update', [$pelatihan->id, '__DATE__']) }}" method="POST">
            @csrf
            <input type="hidden" name="date" id="assign-date">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Presenter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Pilih User Internal</label>
                        <select class="form-select select2" name="user_ids[]" multiple>
                            @foreach ($internalUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->jabatan_full }} - {{ $user->registration_id }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Pilih Presenter Eksternal</label>
                        <select class="form-select select2" name="presenter_ids[]" multiple>
                            @foreach ($externalPresenters as $presenter)
                                <option value="{{ $presenter->id }}">{{ $presenter->name }} ({{ $presenter->institution }})</option>
                            @endforeach
                        </select>
                        <div class="mt-2 text-end">
                            <a href="#" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#createExternalPresenterModal" data-bs-dismiss="modal">+ Tambah Presenter Baru</a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal Tambah Presenter Eksternal --}}
<div class="modal fade" id="createExternalPresenterModal" tabindex="-1" aria-labelledby="createExternalPresenterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('training.daftarhadirpelatihan.presenter.storeExternal') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Presenter Eksternal Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label>Instansi</label>
                        <input type="text" class="form-control" name="institution">
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email">
                    </div>
                    <div class="mb-3">
                        <label>No. HP</label>
                        <input type="text" class="form-control" name="phone">
                    </div>
                    <div class="mb-3">
                        <label>Catatan</label>
                        <textarea class="form-control" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.select2').select2({ width: '100%' });

        $('#assignPresenterModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const date = button.data('date');
            $('#assign-date').val(date);

            const form = $(this).find('form');
            const baseAction = "{{ route('training.daftarhadirpelatihan.presenter.update', [$pelatihan->id, '__DATE__']) }}";
            const updatedAction = baseAction.replace('__DATE__', date);
            form.attr('action', updatedAction);
        });

        $('.delete-presenter').on('click', function() {
            if (confirm('Yakin ingin menghapus presenter ini?')) {
                const date = $(this).data('date');
                const type = $(this).data('type');
                const id = $(this).data('id');

                $.ajax({
                    url: "{{ route('training.daftarhadirpelatihan.presenter.destroy', $pelatihan->id) }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: 'DELETE',
                        date: date,
                        type: type,
                        id: id
                    },
                    success: function(response) {
                        if (response.success) location.reload();
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat menghapus presenter.');
                    }
                });
            }
        });
    });
</script>
@endpush
