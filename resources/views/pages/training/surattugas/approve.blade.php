@extends('layout.main')

@section('title', 'Konfirmasi Persetujuan - Surat Tugas')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Konfirmasi Persetujuan</h4>

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
                <p><strong>Kode Tugas:</strong> {{ $surat->kode_tugas ?? '-' }}</p>
                <p><strong>Judul:</strong> {{ $surat->judul ?? '-' }}</p>
                <p><strong>Tanggal Pelatihan:</strong>
                    {{ optional($surat->pelatihan->tanggal_mulai)->format('d M Y') ?? '-' }} s.d.
                    {{ optional($surat->pelatihan->tanggal_selesai)->format('d M Y') ?? '-' }}
                </p>
            </div>

            {{-- Form persetujuan --}}
            <form action="{{ route('training.surattugas.approve', ['id' => $surat->id, 'approval' => $approval->id]) }}" method="POST">
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
