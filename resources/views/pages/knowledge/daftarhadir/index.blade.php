@extends('layout.main')

@section('title', 'Daftar Hadir Knowledge Sharing')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Daftar Hadir Knowledge Sharing</h4>

    <div class="card mb-4">
        <div class="card-body">
            {{-- Search Bar --}}
            <form method="GET" action="{{ route('knowledge.daftarhadir.index') }}" class="mb-3">
                <div class="input-group input-group-lg">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari judul atau topik knowledge sharing">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bx bx-search"></i> Cari
                    </button>
                    @if(request()->filled('q'))
                        <a href="{{ route('knowledge.daftarhadir.index') }}" class="btn btn-outline-secondary px-4">Reset</a>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID Knowledge</th>
                            <th>Judul</th>
                            <th>Pemateri</th>
                            <th class="text-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Sementara kosong karena belum ada ID knowledge yang bisa ditarik --}}
                        {{-- @forelse($knowledgeList as $knowledge)
                            <tr>
                                <td>{{ $knowledge->id }}</td>
                                <td>{{ $knowledge->judul }}</td>
                                <td>{{ $knowledge->pemateri }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('knowledge.daftarhadir.show', $knowledge->id) }}" class="btn btn-sm btn-primary mb-1">
                                        <i class="bx bx-list-check"></i> Isi Daftar Hadir
                                    </a>
                                </td>
                            </tr>
                        @empty --}}
                            <tr>
                                <td colspan="4" class="text-center fw-bold">Tidak ada knowledge sharing tersedia.</td>
                            </tr>
                        {{-- @endforelse --}}

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
