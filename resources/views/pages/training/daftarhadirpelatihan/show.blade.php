@extends('layout.main')

@section('title', 'Daftar Hadir - ' . $pelatihan->judul)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Daftar Hadir Pelatihan - {{ $pelatihan->judul }}</h4>

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
                        @forelse($pelatihan->daftarHadirStatus as $status)
                            <tr>
                                <td>{{ $status->formattedDate() }}</td>

                                {{-- Internal Presenters --}}
                                <td>
                                    @php
                                        $internal = $pelatihan->presenters()
                                            ->where('type', 'internal')
                                            ->where('date', $status->date->toDateString())
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

                                {{-- External Presenters --}}
                                <td>
                                    @php
                                        $external = $pelatihan->presenters()
                                            ->where('type', 'external')
                                            ->where('date', $status->date->toDateString())
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

                                {{-- Status --}}
                                <td>
                                    <span class="badge bg-{{ $status->is_submitted ? 'success' : 'warning' }}">
                                        {{ $status->submittedLabel() }}
                                    </span>
                                </td>

                                {{-- Action --}}
                                <td class="text-nowrap">
                                    @php
                                        $submitted = \App\Models\PelatihanPresenter::where('pelatihan_id', $pelatihan->id)
                                            ->whereDate('date', $status->date->toDateString())
                                            ->where('is_submitted', true)
                                            ->exists();
                                    @endphp

                                    @if($submitted)
                                        {{-- Preview --}}
                                        <a href="{{ route('training.daftarhadirpelatihan.preview', [$pelatihan->id, $status->date->toDateString()]) }}" 
                                        class="btn btn-sm btn-info mb-1" target="_blank">
                                            <i class="bx bx-show"></i> Preview
                                        </a>

                                        {{-- Download --}}
                                        <a href="{{ route('training.daftarhadirpelatihan.download', [$pelatihan->id, $status->date->toDateString()]) }}" 
                                        class="btn btn-sm btn-success mb-1" target="_blank">
                                            <i class="bx bx-download"></i> Download
                                        </a>

                                        {{-- Kelola Kehadiran (after Download) --}}
                                        <a href="{{ route('training.daftarhadirpelatihan.day', [$pelatihan->id, $status->date->toDateString()]) }}" 
                                        class="btn btn-sm btn-primary mb-1">
                                            <i class="bx bx-list-check"></i> Kelola Kehadiran
                                        </a>
                                    @else
                                        <button class="btn btn-sm btn-secondary" disabled>
                                            <i class="bx bx-time-five"></i> Menunggu Submit Presenter
                                        </button>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center fw-bold">Belum ada daftar hadir.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
