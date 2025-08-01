@extends('layout.main')

@section('title', 'Evaluasi Pelatihan Level 1')

@section('content')
<div class="page-heading">
    <h3>Evaluasi Level 1</h3>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-header">
            <h5>Daftar Evaluasi</h5>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode Pelatihan</th>
                        <th>Nama Pelatihan</th>
                        <th>Tanggal Pelaksanaan</th>
                        <th>Tempat</th>
                        <th>Penyelenggara</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pelatihans as $pelatihan)
                        @php
                            $evaluasi = $pelatihan->evaluasiLevel1->firstWhere('user_id', auth()->id());
                        @endphp
                        <tr>
                            <td>{{ $pelatihan->kode_pelatihan }}</td>
                            <td>{{ $pelatihan->judul }}</td>
                            <td>{{ $pelatihan->tanggal_mulai->format('d M Y') }} - {{ $pelatihan->tanggal_selesai->format('d M Y') }}</td>
                            <td>{{ $pelatihan->tempat }}</td>
                            <td>{{ $pelatihan->penyelenggara }}</td>
                            <td class="text-center">
                                @if ($evaluasi)
                                    <a href="{{ route('training.evaluation1.show', $pelatihan->id) }}" class="btn btn-sm btn-outline-secondary">
                                        Lihat Detail
                                    </a>
                                @else
                                    <a href="{{ route('training.evaluation1.form', $pelatihan->id) }}" class="btn btn-sm btn-primary">
                                        Isi Evaluasi
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Tidak ada pelatihan tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
