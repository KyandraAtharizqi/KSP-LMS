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
                    {{ $pelatihan->tanggal_mulai->format('d M Y') }} - {{ $pelatihan->tanggal_selesai->format('d M Y') }}
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
            <div class="d-flex gap-2">
                <a href="{{ route('training.daftarhadirpelatihan.presenter.index', $pelatihan->id) }}" class="btn btn-outline-dark btn-sm">
                    Kelola Presenter
                </a>
                <a href="{{ route('training.daftarhadirpelatihan.index') }}" class="btn btn-secondary btn-sm">
                    Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Presenter Internal</th>
                        <th>Presenter Eksternal</th>
                        <th>Status</th>
                        <th>Aksi</th>
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
                            <td>
                                @php
                                    $submitted = \App\Models\PelatihanPresenter::where('pelatihan_id', $pelatihan->id)
                                        ->whereDate('date', $status->date->toDateString())
                                        ->where('is_submitted', true)
                                        ->exists();
                                @endphp

                                @if($submitted)
                                    <a href="{{ route('training.daftarhadirpelatihan.day', [$pelatihan->id, $status->date->toDateString()]) }}" 
                                       class="btn btn-sm btn-primary">
                                        Kelola Kehadiran
                                    </a>
                                @else
                                    <button class="btn btn-sm btn-secondary" disabled>
                                        Menunggu Submit Presenter
                                    </button>
                                @endif

                                {{-- New logic for preview/download buttons --}}
                                @if($status->is_submitted)
                                    <a href="{{ route('training.daftarhadirpelatihan.preview', [$pelatihan->id, $status->date->toDateString()]) }}" 
                                    class="btn btn-sm btn-info" target="_blank">
                                        Preview
                                    </a>
                                    <a href="{{ route('training.daftarhadirpelatihan.download', [$pelatihan->id, $status->date->toDateString()]) }}" 
                                    class="btn btn-sm btn-success" target="_blank">
                                        Download PDF
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada daftar hadir.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
