@extends('layout.main')

@section('content')
<div class="container">
    <h4 class="mb-4">Evaluasi Level 3 - Peserta</h4>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Kode Pelatihan</th>
                <th>Judul</th>
                <th>Tanggal</th>
                <th>Penyelenggara</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pelatihans as $pelatihan)
                @php
                    $evaluasi = $pelatihan->evaluasiLevel3Peserta; // updated relationship
                @endphp
                <tr>
                    <td>{{ $pelatihan->kode_pelatihan }}</td>
                    <td>{{ $pelatihan->judul }}</td>
                    <td>{{ $pelatihan->tanggal_mulai->format('d/m/Y') }} - {{ $pelatihan->tanggal_selesai->format('d/m/Y') }}</td>
                    <td>{{ $pelatihan->penyelenggara }}</td>
                    <td>
                        @if($evaluasi)
                            <span class="badge bg-success">Sudah Diisi</span>
                        @else
                            <span class="badge bg-warning">Belum Diisi</span>
                        @endif
                    </td>
                    <td>
                        @if($evaluasi)
                            <a href="{{ route('evaluasi-level-3.peserta.preview', $pelatihan->id) }}" class="btn btn-info btn-sm">Preview</a>
                            <a href="{{ route('evaluasi-level-3.peserta.edit', $pelatihan->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <a href="{{ route('evaluasi-level-3.peserta.pdf', $pelatihan->id) }}" class="btn btn-danger btn-sm" target="_blank">Download PDF</a>
                        @else
                            <a href="{{ route('evaluasi-level-3.peserta.create', $pelatihan->id) }}" class="btn btn-primary btn-sm">Isi Evaluasi</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Tidak ada pelatihan tersedia untuk evaluasi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
