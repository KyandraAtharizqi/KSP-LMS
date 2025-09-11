@extends('layout.main')

@section('title', 'Konfirmasi Persetujuan - Surat Tugas')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Konfirmasi Persetujuan Surat Tugas</h4>

    <div class="card">
        <div class="card-body">
            <div class="alert alert-info" role="alert">
                <h5 class="alert-heading">Perhatian!</h5>
                <p>Anda akan menyetujui surat tugas di bawah ini. Tindakan ini akan tercatat dalam sistem dan tidak dapat diubah.</p>
                <hr>
                <p class="mb-0">Pastikan semua informasi sudah benar sebelum melanjutkan.</p>
            </div>

            <div class="mb-4">
                <h5>Detail Surat Tugas</h5>
                <p><strong>Kode Pelatihan:</strong> {{ $surat->kode_pelatihan ?? '-' }}</p>
                <p><strong>Judul:</strong> {{ $surat->judul ?? '-' }}</p>
                <p><strong>Tanggal Mulai:</strong> {{ $surat->tanggal_mulai ? \Carbon\Carbon::parse($surat->tanggal_mulai)->format('d M Y') : '-' }}</p>
                <p><strong>Tanggal Selesai:</strong> {{ $surat->tanggal_selesai ? \Carbon\Carbon::parse($surat->tanggal_selesai)->format('d M Y') : '-' }}</p>
                <p><strong>Tanggal Pelaksanaan:</strong>
                    @if ($surat->tanggal_pelaksanaan)
                        @foreach (json_decode($surat->tanggal_pelaksanaan, true) as $tgl)
                            <span class="badge bg-primary">{{ \Carbon\Carbon::parse($tgl)->format('d M Y') }}</span>
                        @endforeach
                    @else
                        -
                    @endif
                </p>
                <p><strong>Durasi:</strong> {{ $surat->durasi ?? '-' }}</p>
                <p><strong>Tempat:</strong> {{ $surat->tempat ?? '-' }}</p>
            </div>

            {{-- Form persetujuan --}}
            <form action="{{ route('training.surattugas.approve', [$surat->id, $approval->id]) }}" method="POST">
                @csrf
                <div class="d-flex justify-content-end">
                    <a href="{{ route('training.surattugas.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-success">
                        <i class="bx bx-check"></i> Ya, Saya Setuju
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
