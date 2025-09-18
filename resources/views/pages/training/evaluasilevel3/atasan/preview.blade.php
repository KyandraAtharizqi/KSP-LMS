@extends('layout.main')

@section('title', 'Preview Evaluasi Level 3 - Atasan')

@section('content')
<div class="container">
    <h4 class="mb-4">Preview Evaluasi Level 3 - Atasan</h4>

    <div class="card mb-4">
        <div class="card-header">
            <h5>Informasi Pelatihan & Peserta</h5>
        </div>
        <div class="card-body">
            <p><strong>Kode Pelatihan:</strong> {{ $evaluasiAtasan->kode_pelatihan }}</p>
            <p><strong>Judul Pelatihan:</strong> {{ $evaluasiAtasan->pelatihan->judul }}</p>
            <p><strong>Peserta:</strong> {{ $evaluasiAtasan->user->name }}</p>
            <p><strong>Jabatan Saat Pelatihan:</strong> {{ $evaluasiAtasan->participantSnapshot->jabatan_full ?? '-' }}</p>
            <p><strong>Department Saat Pelatihan:</strong> {{ $evaluasiAtasan->participantSnapshot->department->name ?? '-' }}</p>
            <p><strong>Atasan Sekarang:</strong> {{ $evaluasiAtasan->atasan->name }}</p>

        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5>Tujuan Pembelajaran</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Tujuan Pembelajaran</th>
                        <th>Diaplikasikan</th>
                        <th>Frekuensi</th>
                        <th>Hasil</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($evaluasiAtasan->tujuanPembelajarans as $tujuan)
                        <tr>
                            <td>{{ $tujuan->tujuan_pembelajaran }}</td>
                            <td>{{ $tujuan->diaplikasikan ? 'Ya' : 'Tidak' }}</td>
                            <td>{{ $tujuan->frekuensi ?? '-' }}</td>
                            <td>{{ $tujuan->hasil ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5>Feedback & Evaluasi Umum</h5>
        </div>
        <div class="card-body">
            @php $feedback = $evaluasiAtasan->feedbacks->first() @endphp

            <p><strong>Telah Mampu:</strong> {{ $feedback->telah_mampu ?? '-' }}</p>
            <p><strong>Tidak Diaplikasikan Karena:</strong> {{ $feedback->tidak_diaplikasikan_karena ?? '-' }}</p>
            <p><strong>Memberikan Informasi Mengenai:</strong> {{ $feedback->memberikan_informasi_mengenai ?? '-' }}</p>
            <p><strong>Lain-lain:</strong> {{ $feedback->lain_lain ?? '-' }}</p>

            <p><strong>Manfaat Pelatihan:</strong> {{ $evaluasiAtasan->manfaat_pelatihan ?? '-' }}</p>
            <p><strong>Kinerja:</strong> {{ $evaluasiAtasan->kinerja ?? '-' }}</p>
            <p><strong>Saran:</strong> {{ $evaluasiAtasan->saran ?? '-' }}</p>
        </div>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('evaluasi-level-3.atasan.index') }}" class="btn btn-secondary">Kembali</a>
        <a href="{{ route('evaluasi-level-3.atasan.downloadPdf', $evaluasiAtasan->id) }}" class="btn btn-danger">
            <i class="fas fa-file-pdf"></i> Download PDF
        </a>
    </div>
</div>
@endsection
