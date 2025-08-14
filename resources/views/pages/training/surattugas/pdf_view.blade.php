@php
    use Illuminate\Support\Facades\DB;

    // Pre-sorted collections
    $parafs = $surat->signaturesAndParafs->where('type', 'paraf')->sortBy('sequence');
    $signatures = $surat->signaturesAndParafs->where('type', 'signature')->sortBy('sequence');

    // Helper to build absolute file path for DomPDF
    function stp_pdf_img_path(?string $relPath): ?string {
        if (!$relPath) return null;
        $full = public_path('storage/' . ltrim($relPath, '/'));
        return file_exists($full) ? $full : null;
    }
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Tugas Pelatihan - {{ $surat->judul }}</title>
    <style>
        @page { margin: 25px 25px 40px 25px; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .wrapper {
            width: 100%;
            margin: 0 auto;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .header-table td {
            vertical-align: middle;
            text-align: center;
        }
        .header-left { text-align: left; width: 25%; }
        .header-center { text-align: center; width: 50%; font-weight: bold; font-size: 16px; }
        .header-right { text-align: right; width: 25%; }

        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .content-table,
        .content-table th,
        .content-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }
        .label-strong { font-weight: bold; }

        .list-ol {
            margin: 0;
            padding-left: 18px;
        }
        .list-ol li {
            margin-bottom: 2px;
        }

        .tembusan-wrapper {
            font-size: 12px;
        }
        .tembusan-wrapper ul {
            margin: 0;
            padding-left: 16px;
        }
        .tembusan-wrapper li {
            margin-bottom: 2px;
        }

        .sign-section-wrapper {
            text-align: center;
            font-size: 12px;
        }
        .sign-block {
            margin-bottom: 30px; /* space between blocks */
        }
        .sign-space {
            height: 80px; /* reserved space for image */
            line-height: 80px;
        }
        .sign-space img {
            max-height: 60px;
            max-width: 100%;
        }
        .sign-meta {
            margin-top: 5px;
            margin-bottom: 25px; /* extra vertical space after meta */
            line-height: 1.2;
        }
        .sign-name { font-weight: bold; text-transform: uppercase; }
    </style>
</head>
<body>
<div class="wrapper">

    {{-- HEADER / KOP --}}
    <table class="header-table">
        <tr>
            <td class="header-left">
                @php $logo1 = public_path('logoksp.png'); @endphp
                @if(file_exists($logo1))
                    <img src="{{ $logo1 }}" alt="Logo" height="40">
                @endif
            </td>
            <td class="header-center">
                SURAT TUGAS PELATIHAN
            </td>
            <td class="header-right">
                @php $logo2 = public_path('path/to/logo_ykan.png'); @endphp
                @if(file_exists($logo2))
                    <img src="{{ $logo2 }}" alt="Logo YKAN" height="40">
                @endif
            </td>
        </tr>
    </table>

    {{-- DETAIL SURAT --}}
    <table class="content-table">
        <tr>
            <td colspan="2">Kepada Yth.</td>
        </tr>
        <tr>
            <td class="label-strong" style="width:30%;">Peserta</td>
            <td>
                <ol class="list-ol">
                    @forelse ($surat->pelatihan->participants ?? [] as $participant)
                        <li>{{ $participant->user->name }} ({{ $participant->user->department->name ?? '-' }})</li>
                    @empty
                        <li>-</li>
                    @endforelse
                </ol>
            </td>
        </tr>
        <tr>
            <td class="label-strong">Judul</td>
            <td>{{ $surat->judul }}</td>
        </tr>
        <tr>
            <td class="label-strong">Tujuan / Sasaran</td>
            <td>{!! nl2br(e($surat->tujuan ?? '-')) !!}</td>
        </tr>
        <tr>
            <td class="label-strong">Nama Instruktur / Lembaga</td>
            <td>{{ $surat->pelatihan?->penyelenggara ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-strong">Hari, Tanggal Pelaksanaan Pelatihan</td>
            <td>
                {{ $surat->pelatihan?->tanggal_mulai?->format('l, d F Y') ?? $surat->tanggal?->format('l, d F Y') }}
                s.d.
                {{ $surat->pelatihan?->tanggal_selesai?->format('l, d F Y') ?? '-' }}
            </td>
        </tr>
        <tr>
            <td class="label-strong">Tempat Pelaksanaan Pelatihan</td>
            <td>{{ $surat->tempat }}</td>
        </tr>
        <tr>
            <td class="label-strong">Waktu Pelaksanaan Pelatihan</td>
            <td>{!! nl2br(e($surat->waktu ?? '-')) !!}</td>
        </tr>
        <tr>
            <td class="label-strong">Instruksi Saat Kegiatan</td>
            <td>{!! nl2br(e($surat->instruksi ?? '-')) !!}</td>
        </tr>
        <tr>
            <td class="label-strong">Hal-hal yang perlu diperhatikan</td>
            <td>{!! nl2br(e($surat->hal_perhatian ?? '-')) !!}</td>
        </tr>
        <tr>
            <td class="label-strong">Catatan</td>
            <td>{!! nl2br(e($surat->catatan ?? '-')) !!}</td>
        </tr>
    </table>

    {{-- TEMBUSAN + SIGNATURES --}}
    <table style="width:100%; margin-top:20px;">
        <tr>
            <td style="width:60%; vertical-align:top;">
                <div class="tembusan-wrapper">
                    <strong>Tembusan:</strong>
                    <ul>
                        <li>Direktur Utama</li>
                        <li>Manager Terkait</li>
                        <li>Arsip</li>
                    </ul>
                </div>
            </td>
            <td style="width:40%; vertical-align:top; text-align:center;">
                <div class="sign-section-wrapper">
                    Cilegon, {{ $surat->tanggal?->format('d F Y') }}
                </div>

                {{-- Paraf blocks --}}
                @foreach ($parafs as $paraf)
                    @php
                        $path = null;
                        if ($paraf->status === 'approved' && $paraf->user?->registration_id) {
                            $rel = DB::table('signature_and_parafs')
                                ->where('registration_id', $paraf->user->registration_id)
                                ->value('paraf_path');
                            $path = stp_pdf_img_path($rel);
                        }
                    @endphp
                    <div class="sign-block">
                        <div class="sign-space">
                            @if ($path)
                                <img src="{{ $path }}" alt="Paraf">
                            @else
                                (Menunggu Paraf)
                            @endif
                        </div>
                        <div class="sign-meta">
                            <span class="sign-name">{{ $paraf->user->name }}</span><br>
                            {{ $paraf->user->registration_id }}<br>
                            {{ $paraf->user->jabatan_full ?? ($paraf->user->jabatan->name ?? '-') }}
                        </div>
                    </div>
                @endforeach

                {{-- Signature blocks --}}
                @foreach ($signatures as $signature)
                    @php
                        $path = null;
                        if ($signature->status === 'approved' && $signature->user?->registration_id) {
                            $rel = DB::table('signature_and_parafs')
                                ->where('registration_id', $signature->user->registration_id)
                                ->value('signature_path');
                            $path = stp_pdf_img_path($rel);
                        }
                    @endphp
                    <div class="sign-block">
                        <div class="sign-space">
                            @if ($path)
                                <img src="{{ $path }}" alt="Tanda Tangan">
                            @else
                                (Menunggu Tanda Tangan)
                            @endif
                        </div>
                        <div class="sign-meta">
                            <span class="sign-name">{{ $signature->user->name }}</span><br>
                            {{ $signature->user->registration_id }}<br>
                            {{ $signature->user->jabatan_full ?? ($signature->user->jabatan->name ?? '-') }}
                        </div>
                    </div>
                @endforeach

            </td>
        </tr>
    </table>

</div>
</body>
</html>
