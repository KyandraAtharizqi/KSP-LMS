@extends('layout.main')

@section('title', 'Daftar Hadir - ' . $pelatihan->judul)

@section('content')
<div class="page-heading">
    <h3>Daftar Hadir Pelatihan - {{ $pelatihan->judul }}</h3>
</div>

<div class="page-content">
    <!-- Pelatihan Info -->
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0">Informasi Pelatihan</h5>
        </div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Kode Pelatihan</dt>
                <dd class="col-sm-9">{{ $pelatihan->kode_pelatihan }}</dd>

                <dt class="col-sm-3">Tanggal</dt>
                <dd class="col-sm-9">
                    {{ $pelatihan->tanggal_mulai->format('d M Y') }} 
                    - 
                    {{ $pelatihan->tanggal_selesai->format('d M Y') }}
                </dd>

                <dt class="col-sm-3">Tempat</dt>
                <dd class="col-sm-9">{{ $pelatihan->tempat }}</dd>

                <dt class="col-sm-3">Penyelenggara</dt>
                <dd class="col-sm-9">{{ $pelatihan->penyelenggara }}</dd>
            </dl>
        </div>
    </div>

    <!-- Attendance Days -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Hadir Per Hari</h5>
            <a href="{{ route('training.daftarhadirpelatihan.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Presenter</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pelatihan->daftarHadirStatus as $status)
                        <tr>
                            <td>{{ $status->formattedDate() }}</td>
                            <td>
                                @if($status->presenter)
                                    <span>{{ $status->presenter }}</span>
                                @else
                                    <form action="{{ route('training.daftarhadirpelatihan.set_presenter', [$pelatihan->id, $status->id]) }}" 
                                          method="POST" class="d-flex">
                                        @csrf
                                        <input type="text" name="presenter" class="form-control form-control-sm me-1" placeholder="Nama Presenter" required>
                                        <button type="submit" class="btn btn-sm btn-success">Simpan</button>
                                    </form>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $status->is_submitted ? 'success' : 'warning' }}">
                                    {{ $status->submittedLabel() }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('training.daftarhadirpelatihan.day', [$pelatihan->id, $status->date->toDateString()]) }}" 
                                   class="btn btn-sm btn-primary">
                                    Kelola Kehadiran
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada daftar hadir.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
