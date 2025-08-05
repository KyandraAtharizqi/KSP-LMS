@extends('layout.main')

@section('title', 'Kelola Presenter')

@push('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
<div class="container">
    <h2 class="mb-4">Kelola Presenter - {{ $pelatihan->judul }}</h2>

    @foreach ($pelatihan->daftarHadirStatus as $status)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Tanggal: {{ $status->formattedDate() }}</strong>
                <button class="btn btn-sm btn-primary" 
                        data-bs-toggle="modal" 
                        data-bs-target="#assignPresenterModal"
                        data-date="{{ $status->date->format('Y-m-d') }}">
                    + Tambah Presenter
                </button>
            </div>
            <div class="card-body">
                @php
                    $assigned = $pelatihan->presenters
                        ->where('date', $status->date->format('Y-m-d'));
                @endphp

                @if ($assigned->count())
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Tipe</th>
                                <th>Instansi</th>
                                <th>Kontak</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($assigned as $item)
                                <tr>
                                    <td>{{ $item->type === 'internal' ? $item->user->name : $item->presenter->name }}</td>
                                    <td><span class="badge bg-info text-dark">{{ ucfirst($item->type) }}</span></td>
                                    <td>
                                        {{ $item->type === 'external' ? $item->presenter->institution : '-' }}
                                    </td>
                                    <td>
                                        {{ $item->type === 'internal' ? $item->user->email : $item->presenter->email }}
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('daftar-hadir.presenter.destroy', $item->id) }}" onsubmit="return confirm('Yakin ingin menghapus presenter ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">Belum ada presenter ditambahkan.</p>
                @endif
            </div>
        </div>
    @endforeach
</div>

<!-- Modal -->
<div class="modal fade" id="assignPresenterModal" tabindex="-1" aria-labelledby="assignPresenterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('daftar-hadir.presenter.store', $pelatihan->id) }}" method="POST">
            @csrf
            <input type="hidden" name="date" id="assign-date">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Presenter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="type" class="form-label">Tipe Presenter</label>
                        <select name="type" id="presenter-type" class="form-select">
                            <option value="internal">Internal</option>
                            <option value="external">Eksternal</option>
                        </select>
                    </div>

                    <div class="mb-3 presenter-select presenter-internal">
                        <label>Pilih User Internal</label>
                        <select class="form-select select2" name="user_ids[]" multiple>
                            @foreach ($internalUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3 presenter-select presenter-external" style="display: none;">
                        <label>Pilih Presenter Eksternal</label>
                        <select class="form-select select2" name="presenter_ids[]" multiple>
                            @foreach ($externalPresenters as $presenter)
                                <option value="{{ $presenter->id }}">{{ $presenter->name }} ({{ $presenter->institution }})</option>
                            @endforeach
                        </select>
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
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.select2').select2({
            width: '100%'
        });

        // Toggle between internal & external input
        $('#presenter-type').on('change', function () {
            const type = $(this).val();
            if (type === 'internal') {
                $('.presenter-internal').show();
                $('.presenter-external').hide();
            } else {
                $('.presenter-internal').hide();
                $('.presenter-external').show();
            }
        });

        // Assign date into hidden input when opening modal
        $('#assignPresenterModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const date = button.data('date');
            $('#assign-date').val(date);
        });
    });
</script>
@endpush
