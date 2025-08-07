@extends('layout.main')

@section('title', 'Daftar Nota Dinas')

@section('content')
<div class="page-heading">
    <h3>Daftar Nota Dinas</h3>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Nota Dinas</h5>
            <a href="{{ route('knowledge.notadinas.create') }}" class="btn btn-primary btn-sm">
                + Tambah Nota
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped small" style="font-size: 0.85rem;">
                    <thead class="table-light text-center align-middle">
                        <tr>
                            <th>Kode Nota</th>
                            <th>Perihal</th>
                            <th>Tanggal</th>
                            <th>Dari</th>
                            <th>Kepada</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($notaDinas as $nota)
                            <tr>
                                <td>{{ $nota->kode }}</td>
                                <td>{{ $nota->perihal }}</td>
                                <td>{{ \Carbon\Carbon::parse($nota->tanggal)->translatedFormat('d M Y') }}</td>
                                <td>{{ $nota->dari }}</td>
                                <td>{{ $nota->kepada }}</td>
                                <td>
                                    <a href="{{ route('knowledge.notadinas.show', $nota->id) }}" class="btn btn-sm btn-info me-1 my-1">Lihat</a>
                                    <a href="{{ route('knowledge.notadinas.edit', $nota->id) }}" class="btn btn-sm btn-warning me-1 my-1">Edit</a>

                                    <form action="{{ route('knowledge.notadinas.destroy', $nota->id) }}" method="POST" class="d-inline me-1 my-1">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus nota ini?')">Hapus</button>
                                    </form>

                                    <a href="{{ route('knowledge.notadinas.download', $nota->id) }}" class="btn btn-sm btn-success">Unduh PDF</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada nota dinas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
