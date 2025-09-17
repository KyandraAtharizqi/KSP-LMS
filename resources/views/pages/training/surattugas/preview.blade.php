<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Preview - Surat Tugas Pelatihan - {{ $surat->judul }}</title>
    <style>
        @page { margin: 15px 20px; }
        body {
            font-family: Arial, sans-serif;
            font-size: 8px; /* Changed from 10px to match PDF */
            color: #000;
            margin: 0;
            padding: 20px;
            line-height: 1.2; /* Changed from 1.3 to match PDF */
            background-color: #f5f5f5;
        }
        
        .wrapper {
            width: 21cm;
            min-height: 29.7cm;
            margin: 0 auto;
            border: 1px solid #333; /* Changed from 2px to match PDF */
            background-color: white;
            padding: 0;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        /* Header with logos and title */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        .header-table td {
            vertical-align: middle;
            text-align: center;
            border-bottom: 1px solid #000; /* Changed from 2px to match PDF */
            padding: 5px; /* Changed from 10px to match PDF */
        }
        .header-left { text-align: left; width: 25%; }
        .header-center { 
            text-align: center; 
            width: 50%; 
            font-weight: bold; 
            font-size: 10px; /* Reduced from 14px to match PDF */
            letter-spacing: 1px;
        }
        .header-right { text-align: right; width: 25%; }

        /* Main content table */
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        
        .content-table,
        .content-table th,
        .content-table td {
            border: 1px solid #000;
            padding: 3px 4px; /* Changed from 8px to match PDF */
            vertical-align: top;
            font-size: 8px; /* Changed from 10px to match PDF */
            line-height: 1.2; /* Changed from 1.3 to match PDF */
        }
        
        .label-strong {
            font-weight: bold;
            width: 30%; /* Changed from 180px to percentage like PDF */
            background-color: #f8f8f8;
        }

        /* Lists styling */
        .list-ol {
            margin: 0;
            padding-left: 12px; /* Changed from 20px to match PDF */
            counter-reset: item;
        }
        .list-ol li {
            margin-bottom: 1px; /* Changed from 2px to match PDF */
            font-size: 8px; /* Changed from 10px to match PDF */
            list-style: none;
            position: relative;
        }
        .list-ol li::before {
            content: counter(item) ". ";
            counter-increment: item;
            font-weight: normal;
        }

        .date-list {
            margin: 0;
            padding-left: 12px; /* Changed from 15px to match PDF */
        }
        .date-list li {
            margin-bottom: 1px;
            font-size: 8px; /* Changed from 10px to match PDF */
        }

        .catatan-list {
            margin: 0;
            padding-left: 0;
            counter-reset: catatan;
        }
        .catatan-list li {
            margin-bottom: 1px; /* Changed from 2px to match PDF */
            font-size: 8px; /* Changed from 10px to match PDF */
            list-style: none;
            position: relative;
            padding-left: 12px; /* Changed from 20px to match PDF */
        }
        .catatan-list li::before {
            content: counter(catatan) ") ";
            counter-increment: catatan;
            position: absolute;
            left: 0;
            font-weight: normal;
        }

        /* Bottom section */
        .tembusan-wrapper {
            font-size: 8px; /* Changed from 10px to match PDF */
            padding: 5px; /* Changed from 15px to match PDF */
        }
        .tembusan-wrapper ul {
            margin: 0; /* Changed from 5px 0 0 0 to match PDF */
            padding-left: 12px; /* Changed from 15px to match PDF */
        }
        .tembusan-wrapper li {
            margin-bottom: 1px; /* Changed from 2px to match PDF */
            font-size: 8px; /* Changed from 10px to match PDF */
        }

        /* Signature section */
        .sign-grid-header {
            font-size: 8px; /* Changed from 10px to match PDF */
            color: #888;
        }
        .sign-cell {
            text-align: center;
            vertical-align: top;
            padding: 0 2px; /* Changed from 5px to match PDF */
            border: 1px solid #ddd; /* Changed from #ccc to match PDF */
        }
        .sign-image-box {
            height: 40px; /* Changed from 50px to match PDF */
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2px; /* Changed from 5px to match PDF */
        }
        .sign-image-box img {
            height: 35px; /* Changed from 40px to match PDF */
            max-width: 100%;
        }
        .sign-blank {
            height: 35px; /* Changed from 40px to match PDF */
            border-bottom: 1px solid #000;
            width: 80%;
            margin-bottom: 2px; /* Changed from 5px to match PDF */
        }
        .sign-meta {
            margin-top: 2px; /* Changed from 5px to match PDF */
            line-height: 1.1; /* Changed from 1.2 to match PDF */
            font-size: 8px; /* Changed from 9px to match PDF */
        }
        .sign-meta small {
            display: block;
            line-height: 1.1; /* Changed from 1.2 to match PDF */
            font-size: 8px; /* Changed from 9px to match PDF */
        }
        .sign-role {
            font-size: 8px; /* Changed from 9px to match PDF */
            color: #888; /* Changed from #666 to match PDF */
            margin-top: 2px; /* Changed from 3px to match PDF */
        }

        /* No signatures message */
        .no-signatures {
            margin: 10px 0; /* Changed from 20px 0 to match PDF */
            text-align: center;
            font-style: italic;
            font-size: 8px; /* Changed from 10px to match PDF */
            color: #888;
        }

        /* Preview controls */
        .preview-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: rgba(0,0,0,0.8);
            padding: 10px;
            border-radius: 5px;
        }
        .preview-controls a {
            color: white;
            text-decoration: none;
            margin: 0 5px;
            padding: 5px 10px;
            background: #007bff;
            border-radius: 3px;
            font-size: 12px;
        }
        .preview-controls a:hover {
            background: #0056b3;
        }
        .preview-controls a.btn-secondary {
            background: #6c757d;
        }
        .preview-controls a.btn-success {
            background: #28a745;
        }

        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            .wrapper {
                box-shadow: none;
                border: none;
            }
            .preview-controls {
                display: none;
            }
        }
    </style>
</head>
<body>

{{-- Preview Controls --}}
<div class="preview-controls">
    <a href="{{ route('training.surattugas.index') }}" class="btn-secondary">Kembali</a>
    @if($surat->id && $surat->is_accepted)
        <a href="{{ route('training.surattugas.download', $surat->id) }}" class="btn-success">Download PDF</a>
    @endif
</div>

<div class="wrapper">
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

        // Helper to build absolute file path for preview
        function preview_img_path(?string $relPath): ?string {
            if (!$relPath) return null;
            return asset('storage/' . ltrim($relPath, '/'));
        }
    @endphp

    {{-- HEADER / KOP --}}
    <table class="header-table">
        <tr>
            <td class="header-left">
                <img src="{{ asset('logo.png') }}" alt="Logo" height="25">
            </td>
            <td class="header-center">
                SURAT TUGAS PELATIHAN
            </td>
            <td class="header-right">
                <img src="{{ asset('logo.png') }}" alt="Logo" height="25">
            </td>
        </tr>
    </table>

    {{-- DETAIL SURAT --}}
    <table class="content-table">
        <tr>
            <td class="label-strong">Peserta</td>
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
                                                $path = preview_img_path($rel);
                                            }
                                        @endphp
                                        <td class="sign-cell" style="width:33.33%;">
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
                                        $path = preview_img_path($rel);
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