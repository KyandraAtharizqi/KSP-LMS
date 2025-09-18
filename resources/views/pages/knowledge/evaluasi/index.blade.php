@extends('layout.main')

@section('title', 'Evaluasi Knowledge Sharing')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Daftar Evaluasi Knowledge Sharing</h4>

    <div class="card mb-4">
        <div class="card-body">
            {{-- Search Bar --}}
            <form method="GET" action="{{ route('knowledge.evaluasi.index') }}" class="mb-3">
                <div class="input-group input-group-lg">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                        placeholder="Cari judul atau kode knowledge sharing (contoh: Leadership / KS-001)">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bx bx-search"></i> Cari
                    </button>
                    @if(request()->filled('q'))
                        <a href="{{ route('knowledge.evaluasi.index') }}" class="btn btn-outline-secondary px-4">Reset</a>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Knowledge (ID)</th>
                            <th>Nama Knowledge Sharing</th>
                            <th>Pemateri</th>
                            <th>Peserta</th>
                            <th>Atasan (Saat Pengajuan)</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Data sementara kosong karena belum ada ID knowledge yang bisa ditarik --}}
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada evaluasi tersedia.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection