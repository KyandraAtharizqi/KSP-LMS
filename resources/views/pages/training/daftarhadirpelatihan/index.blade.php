@extends('layout.main')

@section('title', 'Daftar Hadir Pelatihan')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Daftar Hadir Pelatihan</h4>

    <div class="card mb-4">
        <div class="card-body">
            {{-- Search Bar --}}
            <form method="GET" action="{{ route('training.daftarhadirpelatihan.index') }}" class="mb-3">
                <div class="input-group input-group-lg">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari judul atau kode pelatihan (contoh: Leadership / KSP-001)">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bx bx-search"></i> Cari
                    </button>
                    @if(request()->filled('q'))
                        <a href="{{ route('training.daftarhadirpelatihan.index') }}" class="btn btn-outline-secondary px-4">Reset</a>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Pelatihan</th>
                            <th>Judul</th>
                            <th>Tanggal</th>
                            <th>Tempat</th>
                            <th>Penyelenggara</th>
                            <th class="text-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pelatihans as $pelatihan)
                            <tr>
                                <td>{{ $pelatihan->kode_pelatihan }}</td>
                                <td>{{ $pelatihan->judul }}</td>
                                <td>
                                    {{ $pelatihan->tanggal_mulai?->format('d M Y') ?? '-' }}
                                    s.d.
                                    {{ $pelatihan->tanggal_selesai?->format('d M Y') ?? '-' }}
                                </td>
                                <td>{{ $pelatihan->tempat }}</td>
                                <td>{{ $pelatihan->penyelenggara }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('training.daftarhadirpelatihan.show', $pelatihan->id) }}" class="btn btn-sm btn-primary mb-1">
                                        <i class="bx bx-list-check"></i> Isi Daftar Hadir
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center fw-bold">Tidak ada pelatihan tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
