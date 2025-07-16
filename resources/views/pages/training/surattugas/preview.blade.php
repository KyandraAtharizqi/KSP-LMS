@extends('layout.main')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Preview Surat Tugas Pelatihan</h4>
        <a href="{{ route('training.surattugas.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card">
        <div class="card-body px-5 py-4">
            <div class="text-center mb-4">
                <h4><strong>SURAT TUGAS</strong></h4>
            </div>

            <p>Kepada Yth,</p>
            <ul>
                @foreach ($surat->pelatihan->participants ?? [] as $participant)
                    <li>{{ $participant->user->name }} ({{ $participant->user->jabatan?->nama ?? '-' }})</li>
                @endforeach
            </ul>

            <p>Untuk mengikuti pelatihan dengan informasi sebagai berikut:</p>

            <ul>
                <li><strong>Judul:</strong> {{ $surat->judul }}</li>
                <li><strong>Tempat:</strong> {{ $surat->tempat }}</li>
                <li><strong>Tanggal:</strong>
                    {{ $surat->pelatihan?->tanggal_mulai?->format('d F Y') ?? $surat->tanggal?->format('d F Y') }}
                    s.d.
                    {{ $surat->pelatihan?->tanggal_selesai?->format('d F Y') ?? '-' }}
                </li>
                <li><strong>Penyelenggara:</strong> {{ $surat->pelatihan?->penyelenggara ?? '-' }}</li>
            </ul>

            <p><strong>Tujuan:</strong> Untuk meningkatkan kompetensi dan kinerja karyawan.</p>
            <p><strong>Instruksi:</strong> Harap mengikuti pelatihan sesuai jadwal dan melaporkan hasil pelatihan setelah selesai.</p>
            <p><strong>Hal-hal:</strong> Biaya ditanggung oleh perusahaan. Harap menjaga nama baik perusahaan selama pelatihan berlangsung.</p>

            <br>

            <p>Demikian surat tugas ini dibuat untuk dilaksanakan sebagaimana mestinya.</p>

            <div class="row mt-5">
                <div class="col text-start">
                    <p>Cilegon, {{ $surat->tanggal?->format('d F Y') }}</p>
                </div>
            </div>

            @foreach ($surat->signatures as $sig)
                <div class="text-center mt-5">
                    <p><strong>{{ ucfirst($sig->type) }}</strong></p>

                    @if ($sig->status === 'approved' && $sig->signed_at && $sig->user?->signature_path)
                        <img src="{{ asset('storage/signatures/' . $sig->user->signature_path) }}" alt="Signature" style="height: 60px;">
                    @elseif ($sig->status === 'rejected')
                        <p class="text-danger">(Ditolak)</p>
                    @else
                        <p class="text-muted">(Menunggu)</p>
                    @endif

                    <p class="mt-2">{{ $sig->user->name }}</p>
                </div>
            @endforeach

            <div class="mt-5">
                <p><strong>Tembusan:</strong></p>
                <ul>
                    <li>Direktur Utama</li>
                    <li>Manager Terkait</li>
                    <li>Arsip</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
