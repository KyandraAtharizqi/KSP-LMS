@extends('layout.main')

@section('title', 'Daftar Hadir - ' . $pelatihan->judul)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Daftar Hadir Pelatihan - {{ $pelatihan->judul }}</h4>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Pelatihan Info -->
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="fw-bold mb-0">Informasi Pelatihan</h5>
        </div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3 fw-bold">Kode Pelatihan</dt>
                <dd class="col-sm-9">{{ $pelatihan->kode_pelatihan }}</dd>

                <dt class="col-sm-3 fw-bold">Tanggal</dt>
                <dd class="col-sm-9">
                    {{ $pelatihan->tanggal_mulai->format('d M Y') }} - {{ $pelatihan->tanggal_selesai->format('d M Y') }}
                </dd>

                <dt class="col-sm-3 fw-bold">Tempat</dt>
                <dd class="col-sm-9">{{ $pelatihan->tempat }}</dd>

                <dt class="col-sm-3 fw-bold">Penyelenggara</dt>
                <dd class="col-sm-9">{{ $pelatihan->penyelenggara }}</dd>
            </dl>
        </div>
    </div>

    <!-- Add Date Form -->
    @if(auth()->user()->role === 'admin' || (auth()->user()->role === 'department_admin' && optional(auth()->user()->department)->name === 'Human Capital'))
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Tambah Tanggal Daftar Hadir</h5>
            <form method="POST" action="{{ route('training.daftarhadirpelatihan.addDay', $pelatihan->id) }}" class="d-flex align-items-center gap-2 mb-0">
                @csrf
                <input type="date" 
                       class="form-control form-control-sm @error('date') is-invalid @enderror" 
                       id="date" 
                       name="date" 
                       value="{{ old('date') }}"
                       required>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bx bx-plus"></i> Tambah
                </button>
                @error('date')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </form>
        </div>
    </div>
    @endif

    <!-- Attendance Days -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Daftar Hadir Per Hari</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('training.daftarhadirpelatihan.presenter.index', $pelatihan->id) }}" class="btn btn-outline-dark btn-sm">
                    <i class="bx bx-user"></i> Kelola Presenter
                </a>
                <a href="{{ route('training.daftarhadirpelatihan.index') }}" class="btn btn-secondary btn-sm">
                    <i class="bx bx-arrow-back"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($pelatihan->daftarHadirStatus->isEmpty())
                <div class="text-center py-4">
                    <i class="bx bx-calendar-x display-1 text-muted"></i>
                    <h5 class="mt-3 text-muted">Belum ada tanggal daftar hadir</h5>
                    <p class="text-muted">Tambahkan tanggal menggunakan form di atas untuk mulai mengelola kehadiran.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="fw-bold">Tanggal</th>
                                <th class="fw-bold">Presenter Internal</th>
                                <th class="fw-bold">Presenter Eksternal</th>
                                <th class="fw-bold">Status</th>
                                <th class="fw-bold text-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($pelatihan->daftarHadirStatus->sortBy('date') as $day)
                            <tr>
                                <td>{{ $day->formattedDate() }}</td>

                                <td>
                                    @php
                                        $internal = $day->presenters()
                                            ->where('type', 'internal')
                                            ->whereDate('date', $day->date->toDateString())
                                            ->with('user')
                                            ->get();
                                    @endphp
                                    @if($internal->isNotEmpty())
                                        <ul class="mb-0 ps-3">
                                            @foreach($internal as $item)
                                                <li>{{ $item->user->name ?? 'N/A' }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <em class="text-muted">Tidak ada</em>
                                    @endif
                                </td>

                                <td>
                                    @php
                                        $external = $day->presenters()
                                            ->where('type', 'external')
                                            ->whereDate('date', $day->date->toDateString())
                                            ->with('presenter')
                                            ->get();
                                    @endphp
                                    @if($external->isNotEmpty())
                                        <ul class="mb-0 ps-3">
                                            @foreach($external as $item)
                                                <li>{{ $item->presenter->name ?? 'N/A' }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <em class="text-muted">Tidak ada</em>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-{{ $day->is_submitted ? 'success' : 'warning' }}">
                                        {{ $day->submittedLabel() }}
                                    </span>
                                </td>

                                <td class="text-nowrap">
                                    @if($day->is_submitted)
                                        <a href="{{ route('training.daftarhadirpelatihan.preview', [$pelatihan->id, $day->date->toDateString()]) }}" 
                                        class="btn btn-sm btn-info mb-1" target="_blank">
                                            <i class="bx bx-show"></i> Preview
                                        </a>
                                        <a href="{{ route('training.daftarhadirpelatihan.download', [$pelatihan->id, $day->date->toDateString()]) }}" 
                                        class="btn btn-sm btn-success mb-1" target="_blank">
                                            <i class="bx bx-download"></i> Download
                                        </a>
                                        <a href="{{ route('training.daftarhadirpelatihan.day', [$pelatihan->id, $day->date->toDateString()]) }}" 
                                        class="btn btn-sm btn-primary mb-1">
                                            <i class="bx bx-list-check"></i> Kelola Kehadiran
                                        </a>
                                    @else
                                        <a href="{{ route('training.daftarhadirpelatihan.day', [$pelatihan->id, $day->date->toDateString()]) }}" 
                                        class="btn btn-sm btn-primary mb-1">
                                            <i class="bx bx-list-check"></i> Kelola Kehadiran
                                        </a>
                                        
                                        @if(auth()->user()->role === 'admin' || (auth()->user()->role === 'department_admin' && optional(auth()->user()->department)->name === 'Human Capital'))
                                            <form method="POST" 
                                                  action="{{ route('training.daftarhadirpelatihan.removeDay', [$pelatihan->id, $day->id]) }}" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus tanggal {{ $day->formattedDate() }}? Data kehadiran pada tanggal ini akan hilang.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger mb-1">
                                                    <i class="bx bx-trash"></i> Hapus
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
