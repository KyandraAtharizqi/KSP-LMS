@extends('layout.main')

@section('title', 'Daftar Hadir Pelatihan')

@section('content')
<div class="page-heading">
    <h3>Daftar Hadir Pelatihan</h3>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Daftar Pelatihan</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Kode Pelatihan</th>
                        <th>Judul</th>
                        <th>Tanggal</th>
                        <th>Tempat</th>
                        <th>Penyelenggara</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pelatihans as $pelatihan)
                        <tr>
                            <td>{{ $pelatihan->kode_pelatihan }}</td>
                            <td>{{ $pelatihan->judul }}</td>
                            <td>{{ $pelatihan->tanggal_mulai->format('d M Y') }} - {{ $pelatihan->tanggal_selesai->format('d M Y') }}</td>
                            <td>{{ $pelatihan->tempat }}</td>
                            <td>{{ $pelatihan->penyelenggara }}</td>
                            <td>
                                <a href="{{ route('training.daftarhadirpelatihan.show', $pelatihan->id) }}" class="btn btn-sm btn-primary">
                                    Isi Daftar Hadir
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada pelatihan tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
