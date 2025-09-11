@extends('layout.main')

@push('styles')
<style>
    .table-bordered-custom,
    .table-bordered-custom th,
    .table-bordered-custom td {
        border: 1px solid black;
        vertical-align: top;
    }
    .table-bordered-custom {
        width: 100%;
        border-collapse: collapse;
    }
    .header-logo {
        height: 50px;
    }
    /* Let signature blocks size themselves; reserve minimum vertical space */
    .signature-space {
        min-height: 50px;   /* was 80; adjust as needed */
        line-height: 0;      /* avoid extra whitespace */
    }
    .signature-space img {
        max-height: 60px;    /* hard cap */
        max-width: 100%;     /* responsive shrink */
        width: auto;
        height: auto;
        display: block;
        margin: 0 auto;
        object-fit: contain; /* preserve aspect ratio inside cap */
    }
    .no-border, .no-border tr, .no-border td {
        border: none;
    }
    /* We’ll not rely on .sign-img height any more */
    .sign-img { display:block; margin:0 auto; }
    .sign-meta {
        text-align: center;
        font-size: 12px;
        margin-top: 5px;
        margin-bottom: 25px; /* Increased spacing between signature blocks */
        line-height: 1.2;
    }
    .signature-block {
        margin-bottom: 10px; /* small safety margin; main spacing handled by .sign-meta */
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Preview Surat Tugas Pelatihan</h4>
        <div>
            <a href="{{ route('training.surattugas.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body px-5 py-4" style="color: black;">
            
            {{-- BAGIAN KOP SURAT --}}
            <div class="row mb-4 align-items-center">
                <div class="col-3">
                    <img src="{{ asset('logoksp.png') }}" alt="Logo" style="height: 40px;">
                </div>
                <div class="col-6 text-center">
                    <h4 class="mb-0"><strong>SURAT TUGAS PELATIHAN</strong></h4>
                </div>
                <div class="col-3 text-end">
                    <img src="{{ asset('path/to/logo_ykan.png') }}" alt="Logo YKAN" class="header-logo">
                </div>
            </div>

            {{-- BAGIAN ISI SURAT --}}
            <table class="table table-bordered-custom">
                <tr>
                    <td><strong>Peserta</strong></td>
                    <td>
                        <ol class="mb-0 ps-3">
                            @forelse ($surat->pelatihan->participants ?? [] as $participant)
                                <li>
                                    {{ $participant->user->name }} 
                                    ({{ $participant->user->department->name ?? '-' }})
                                </li>
                            @empty
                                <li>-</li>
                            @endforelse
                        </ol>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">Untuk mengikuti kegiatan pelatihan dengan informasi sebagai berikut:</td>
                </tr>
                <tr>
                    <td><strong>Judul</strong></td>
                    <td>{{ $surat->judul }}</td>
                </tr>
                <tr>
                    <td><strong>Tujuan / Sasaran</strong></td>
                    <td>{!! nl2br(e($surat->tujuan ?? '-')) !!}</td>
                </tr>
                <tr>
                    <td><strong>Nama Instruktur / Lembaga</strong></td>
                    <td>{{ $surat->pelatihan?->penyelenggara ?? '-' }}</td>
                    <tr>
                        <td><strong>Tanggal Mulai</strong></td>
                        <td>
                            {{ $surat->pelatihan?->tanggal_mulai?->translatedFormat('l, d F Y') ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Selesai</strong></td>
                        <td>
                            {{ $surat->pelatihan?->tanggal_selesai?->translatedFormat('l, d F Y') ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Rincian Hari/Tanggal Pelaksanaan</strong></td>
                        <td>
                            @php
                                $pelaksanaan = $surat->pelatihan?->tanggal_pelaksanaan;
                                if (is_string($pelaksanaan)) {
                                    $pelaksanaan = json_decode($pelaksanaan, true);
                                }
                            @endphp

                            @if (!empty($pelaksanaan))
                                <ul class="mb-0 ps-3">
                                    @foreach ($pelaksanaan as $tgl)
                                        <li>{{ \Carbon\Carbon::parse($tgl)->translatedFormat('l, d F Y') }}</li>
                                    @endforeach
                                </ul>
                            @else
                                -
                            @endif
                        </td>
                    </tr>

                    <td><strong>Tempat Pelaksanaan Pelatihan</strong></td>
                    <td>{{ $surat->tempat }}</td>
                </tr>
                <tr>
                    <td><strong>Waktu Pelaksanaan Pelatihan</strong></td>
                    <td>{!! nl2br(e($surat->waktu ?? '-')) !!}</td>
                </tr>
                <tr>
                    <td><strong>Instruksi Saat Kegiatan</strong></td>
                    <td>{!! nl2br(e($surat->instruksi ?? '-')) !!}</td>
                </tr>
                 <tr>
                    <td><strong>Hal-hal yang perlu diperhatikan</strong></td>
                    <td>{!! nl2br(e($surat->hal_perhatian ?? '-')) !!}</td>
                </tr>
                 <tr>
                    <td><strong>Catatan</strong></td>
                    <td>{!! nl2br(e($surat->catatan ?? '-')) !!}</td>
                </tr>
            </table>

            <br>

            {{-- BAGIAN TANDA TANGAN & TEMBUSAN --}}
            <div class="row mt-4">
                <div class="col-7">
                    <p><strong>Tembusan:</strong></p>
                    <ul class="ps-3">
                        <li>Direktur Utama</li>
                        <li>Manager Terkait</li>
                        <li>Arsip</li>
                    </ul>
                </div>

                <div class="col-5 text-center">
                    @php
                        // Load all signature & paraf file paths once
                        $sapMap = DB::table('signature_and_parafs')
                            ->whereIn('registration_id', $surat->signaturesAndParafs->pluck('user.registration_id')->filter()->unique())
                            ->get()
                            ->keyBy('registration_id');

                        // Get the latest round
                        $latestRound = $surat->signaturesAndParafs->max('round') ?? 1;

                        // Filter by the latest round
                        $parafs = $surat->signaturesAndParafs
                            ->where('type', 'paraf')
                            ->where('round', $latestRound)
                            ->sortBy('sequence');
                            
                        $signatures = $surat->signaturesAndParafs
                            ->where('type', 'signature')
                            ->where('round', $latestRound)
                            ->sortBy('sequence');
                            
                        // Get the latest signature's date
                        $latestApprovedSignature = $signatures
                            ->where('status', 'approved')
                            ->sortByDesc('signed_at')
                            ->first();
                        
                        $documentDate = $latestApprovedSignature && $latestApprovedSignature->signed_at 
                            ? $latestApprovedSignature->signed_at
                            : ($surat->tanggal ?? now());
                    @endphp

                    <p>Cilegon, {{ $documentDate->format('d F Y') }}</p>
                    
                    {{-- Tampilkan Paraf --}}
                    @foreach ($parafs as $paraf)
                        @php
                            $reg = $paraf->user?->registration_id;
                            $filePath = ($paraf->status === 'approved' && $reg && isset($sapMap[$reg]))
                                ? $sapMap[$reg]->paraf_path
                                : null;
                        @endphp
                        <div class="signature-block text-center">
                            <div class="signature-space">
                                @if ($filePath)
                                    <img src="{{ asset('storage/'.$filePath) }}" alt="Paraf" class="sign-img">
                                @else
                                    <p>(Menunggu Paraf)</p>
                                @endif
                            </div>
                            <div class="sign-meta">
                                <strong>{{ $paraf->user->name }}</strong><br>
                                {{ $paraf->user->registration_id }}<br>
                                {{ $paraf->user->jabatan_full ?? ($paraf->user->jabatan->name ?? '-') }}
                            </div>
                        </div>
                    @endforeach

                    {{-- Tampilkan Tanda Tangan --}}
                    @foreach ($signatures as $signature)
                        @php
                            $reg = $signature->user?->registration_id;
                            $filePath = ($signature->status === 'approved' && $reg && isset($sapMap[$reg]))
                                ? $sapMap[$reg]->signature_path
                                : null;
                        @endphp
                        <div class="signature-block text-center">
                            <div class="signature-space">
                                @if ($filePath)
                                    <img src="{{ asset('storage/'.$filePath) }}" alt="Tanda Tangan" class="sign-img">
                                @else
                                    <p>(Menunggu Tanda Tangan)</p>
                                @endif
                            </div>
                            <div class="sign-meta">
                                <strong>{{ $signature->user->name }}</strong><br>
                                {{ $signature->user->registration_id }}<br>
                                {{ $signature->user->jabatan_full ?? ($signature->user->jabatan->name ?? '-') }}
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
