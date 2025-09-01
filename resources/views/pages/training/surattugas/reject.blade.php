@extends('layout.main')

@section('title', 'Konfirmasi Penolakan - Surat Tugas')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Konfirmasi Penolakan</h4>

    <div class="card">
        <div class="card-body">
            <div class="alert alert-warning" role="alert">
                <h5 class="alert-heading">Perhatian!</h5>
                <p>Anda akan menolak surat tugas di bawah ini. Tindakan ini akan <strong>mengembalikan surat kepada ADMIN dan DEPARTMENT ADMIN</strong> untuk diperbaiki.</p>
                <hr>
                <p class="mb-0">Harap berikan alasan penolakan agar pembuat dapat melakukan perbaikan yang sesuai.</p>
            </div>

            <div class="mb-4">
                <h5>Detail Surat Tugas</h5>
                <p><strong>Kode Pelatihan:</strong> {{ $surat->kode_pelatihan ?? '-' }}</p>
                <p><strong>Judul:</strong> {{ $surat->judul ?? '-' }}</p>
                <p><strong>Tanggal Pelatihan:</strong>
                    {{ optional($surat->pelatihan->tanggal_mulai)->format('d M Y') ?? '-' }} s.d.
                    {{ optional($surat->pelatihan->tanggal_selesai)->format('d M Y') ?? '-' }}
                </p>
            </div>

            {{-- Form penolakan --}}
            <form action="{{ route('training.surattugas.reject', ['id' => $surat->id, 'approval' => $approval->id]) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="reason" class="form-label">Alasan Penolakan</label>
                    <textarea class="form-control" id="reason" name="reason" rows="4" required></textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('training.surattugas.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-danger">
                        <i class="bx bx-x"></i> Tolak Surat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
