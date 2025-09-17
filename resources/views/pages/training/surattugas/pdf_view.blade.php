@php
    use Illuminate\Support\Facades\DB;

    // Get the latest round
    $latestRound = $surat->signaturesAndParafs->max('round') ?? 1;

    // Pre-sorted collections - filtered by latest round
    $parafs = $surat->signaturesAndParafs
        ->where('type', 'paraf')
        ->where('round', $latestRound)
        ->sortBy('sequence');
    
    $signatures = $surat->signaturesAndParafs
        ->where('type', 'signature')
        ->where('round', $latestRound)
        ->sortBy('sequence');
    
    // Calculate counts
    $parafsCount = $parafs->count();
    $sigsCount = $signatures->count();

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
        @page { margin: 15px 20px; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 8px;
            color: #000;
            margin: 0;
            padding: 0;
            line-height: 1.2;
        }
        .wrapper {
            width: 100%;
            margin: 0 auto;
            border: 1px solid #333;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: middle;
            text-align: center;
            border-bottom: 1px solid #000;
            padding: 5px;
        }
        .header-left { text-align: left; width: 25%; }
        .header-center { text-align: center; width: 50%; font-weight: bold; font-size: 10px; }
        .header-right { text-align: right; width: 25%; }

        .content-table {
            width: 100%;
            border-collapse: collapse;
        }
        .content-table,
        .content-table th,
        .content-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            vertical-align: top;
            font-size: 8px;
        }
        .label-strong { font-weight: bold; }

        .list-ol {
            margin: 0;
            padding-left: 12px;
        }
        .list-ol li {
            margin-bottom: 1px;
            font-size: 8px;
        }

        .tembusan-wrapper {
            font-size: 8px;
        }
        .tembusan-wrapper ul {
            margin: 0;
            padding-left: 12px;
        }
        .tembusan-wrapper li {
            margin-bottom: 1px;
        }

        /* Signature grid */
        .sign-grid-header {
            font-size: 8px;
            color: #888;
        }
        .sign-cell {
            text-align: center;
            vertical-align: top;
            padding: 0 2px;
        }
        .sign-image-box {
            height: 40px;
        }
        .sign-image-box img {
            height: 35px;
        }
        .sign-blank {
            height: 35px;
            border-bottom: 1px solid #000;
        }
        .sign-meta {
            font-size: 8px;
            margin-top: 2px;
            line-height: 1.1;
        }
        .sign-meta small {
            display: block;
            line-height: 1.1;
            font-size: 8px;
        }
        .sign-role {
            font-size: 8px;
            color: #888;
        }
    </style>
</head>
<body>
<div class="wrapper">

    {{-- HEADER / KOP --}}
    <table class="header-table">
        <tr>
            <td class="header-left">
                <img src="{{ public_path('logo.png') }}" alt="Logo" height="25">
            </td>
            <td class="header-center">
                SURAT TUGAS PELATIHAN
            </td>
            <td class="header-right">
                <img src="{{ public_path('logo.png') }}" alt="Logo" height="25">
            </td>
        </tr>
    </table>

    {{-- DETAIL SURAT --}}
    <table class="content-table">
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
            <td><strong>Hari, Tanggal Pelaksanaan Pelatihan</strong></td>
            <td>
                @php
                    // Try to get tanggal_pelaksanaan from surat tugas first
                    $pelaksanaan = $surat->tanggal_pelaksanaan;
                    
                    // If not available, fall back to pengajuan
                    if (empty($pelaksanaan) && $surat->pelatihan) {
                        $pelaksanaan = $surat->pelatihan->tanggal_pelaksanaan;
                    }
                    
                    // Handle JSON string if needed
                    if (is_string($pelaksanaan)) {
                        try {
                            $pelaksanaan = json_decode($pelaksanaan, true);
                        } catch (\Exception $e) {
                            $pelaksanaan = null;
                        }
                    }
                @endphp

                @if (!empty($pelaksanaan))
                    <ul style="margin:0; padding-left:12px;">
                        @foreach ($pelaksanaan as $tgl)
                            <li>{{ \Carbon\Carbon::parse($tgl)->translatedFormat('l, d F Y') }}</li>
                        @endforeach
                    </ul>
                @else
                    {{ $surat->pelatihan?->tanggal_mulai?->translatedFormat('l, d F Y') ?? '-' }}
                    s/d 
                    {{ $surat->pelatihan?->tanggal_selesai?->translatedFormat('l, d F Y') ?? '-' }}
                @endif
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

    <div style="padding: 5px;">
        <table style="width:100%; margin-top:5px; border-collapse:collapse;">
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
                    <div style="font-size: 8px;">
                        @if($signatures->count() > 0 && $signatures->first() && $signatures->first()->updated_at)
                            Cilegon, {{ $signatures->first()->updated_at->format('d F Y') }}
                        @elseif($surat->tanggal)
                            Cilegon, {{ $surat->tanggal->format('d F Y') }}
                        @else
                            Cilegon, {{ now()->format('d F Y') }}
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        {{-- Approval Section for Surat Tugas - Simplified structure --}}
        <div style="margin-top:15px;">
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="width:60%; vertical-align:top;">
                        {{-- Paraf section - Up to 3 parafs in one row --}}
                        @if($parafsCount > 0)
                            <div style="font-weight:bold; font-size:8px; margin-bottom:4px;">Paraf:</div>
                            <table style="width:100%; border-collapse:collapse;">
                                <tr>
                                    @foreach($parafs->take(3) as $index => $paraf)
                                        @php
                                            // Safely get the paraf
                                            $path = null;
                                            
                                            if ($paraf && $paraf->status === 'approved' && $paraf->user?->registration_id) {
                                                $rel = DB::table('signature_and_parafs')
                                                    ->where('registration_id', $paraf->user->registration_id)
                                                    ->value('paraf_path');
                                                $path = stp_pdf_img_path($rel);
                                            }
                                        @endphp
                                        <td class="sign-cell" style="border:1px solid #ddd; width:33.33%;">
                                            <div class="sign-image-box">
                                                @if ($path)
                                                    <img src="{{ $path }}" alt="Paraf">
                                                @else
                                                    <div class="sign-blank"></div>
                                                @endif
                                            </div>
                                            <div class="sign-meta">
                                                {{ $paraf->user->name ?? '-' }}
                                                <small>{{ $paraf->user->registration_id ?? '-' }}</small>
                                                <small>{{ $paraf->user->jabatan_full ?? ($paraf->user->jabatan->name ?? '-') }}</small>
                                            </div>
                                            <div class="sign-role">
                                                @if($paraf->user?->registration_id === '0000')
                                                    (Kepala Bagian)
                                                @else
                                                    (Paraf)
                                                @endif
                                            </div>
                                        </td>
                                    @endforeach
                                    
                                    {{-- Fill remaining cells if less than 3 parafs --}}
                                    @for($i = $parafsCount; $i < 3; $i++)
                                        <td style="width:33.33%;"></td>
                                    @endfor
                                </tr>
                            </table>
                        @endif
                    </td>
                    <td style="width:40%; vertical-align:top;">
                        {{-- Signature section - Only one signature --}}
                        @if($sigsCount > 0)
                            <div style="font-weight:bold; font-size:8px; margin-bottom:4px; text-align:center;">
                                Menyetujui
                            </div>
                            <div style="text-align:center; border:1px solid #ddd; padding:5px;">
                                @php
                                    // Safely get the first signature
                                    $signature = $signatures->first();
                                    $path = null;
                                    
                                    if ($signature && $signature->status === 'approved' && $signature->user?->registration_id) {
                                        $rel = DB::table('signature_and_parafs')
                                            ->where('registration_id', $signature->user->registration_id)
                                            ->value('signature_path');
                                        $path = stp_pdf_img_path($rel);
                                    }
                                @endphp
                                
                                <div class="sign-image-box">
                                    @if ($path)
                                        <img src="{{ $path }}" alt="Tanda Tangan">
                                    @else
                                        <div class="sign-blank"></div>
                                    @endif
                                </div>
                                <div class="sign-meta">
                                    {{ $signature->user->name ?? '-' }}
                                    <small>{{ $signature->user->registration_id ?? '-' }}</small>
                                    <small>{{ $signature->user->jabatan_full ?? ($signature->user->jabatan->name ?? '-') }}</small>
                                </div>
                                <div class="sign-role">(Tanda Tangan)</div>
                            </div>
                        @endif
                    </td>
                </tr>
            </table>
            
            {{-- Show message if no signatures or parafs --}}
            @if($parafsCount == 0 && $sigsCount == 0)
                <div style="margin:10px 0; text-align:center; font-style:italic; font-size:8px; color:#888;">
                    Dokumen ini belum memiliki tanda tangan atau paraf.
                </div>
            @endif
        </div>
    </div>
</div>
</body>
</html>