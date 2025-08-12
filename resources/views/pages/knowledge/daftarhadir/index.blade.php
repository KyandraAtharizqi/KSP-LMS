@extends('layout.main')

@section('title', 'Daftar Hadir Knowledge Sharing')

@section('content')
<div class="page-heading">
    <h3>Daftar Hadir</h3>
</div>

<div class="page-content">
    <a href="{{ route('knowledge.daftarhadir.create') }}" class="btn btn-primary mb-3">+ Tambah</a>

    <div class="card">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table class="table table-bordered table-striped small">
                <thead>
                    <tr>
                        <th>Nama Kegiatan</th>
                        <th>Pemateri</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($daftarHadir as $data)
                        <tr>
                            <td>{{ $data->judul }}</td>
                            <td>{{ $data->pemateri }}</td>
                            <td>{{ \Carbon\Carbon::parse($data->tanggal)->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('knowledge.daftarhadir.show', $data->id) }}" class="btn btn-info btn-sm">Lihat</a>
                                <a href="{{ route('knowledge.daftarhadir.edit', $data->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('knowledge.daftarhadir.destroy', $data->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button onclick="return confirm('Hapus data ini?')" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    @if($daftarHadir->isEmpty())
                        <tr><td colspan="4" class="text-center text-muted">Tidak ada data.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
