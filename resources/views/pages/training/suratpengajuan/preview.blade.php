<!DOCTYPE html>
<html>
<head>
    <title>Surat Pengajuan Pelatihan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            font-size: 8px;
        }
        .form-container {
            background-color: #ffffff;
            border: 1px solid #333;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            background-color: #ffffff;
            color: #000;
            padding: 5px;
            font-weight: bold;
            font-size: 8px;
            display: flex;
            align-items: center;
        }
        .title-bar {
            background-color: #333;
            color: #ffffff;
            text-align: center;
            padding: 4px;
            font-weight: bold;
            font-size: 8px;
        }
        .form-content {
            padding: 5px;
        }
        .form-section {
            border: 1px solid #333;
            margin-bottom: 0;
            margin-top: 3px;
        }
        .section-header {
            background-color: #ffffff;
            padding: 3px 5px;
            font-weight: bold;
            font-size: 8px;
            border-bottom: 0;
        }
        .section-content {
            padding: 2px 5px 5px 5px;
        }
        .field-group {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 2px;
        }
        .field {
            flex: 1;
            min-width: 0;
        }
        .field-label {
            font-weight: bold;
            font-size: 8px;
            margin-bottom: 2px;
            display: block;
        }
        .field-value {
            border: 1px solid #999;
            padding: 2px 4px;
            background-color: #ffffff;
            min-height: 10px;
            font-size: 8px;
        }
        .participants-list {
            margin-top: 3px;
        }
        .participant-item {
            padding: 2px 0;
            border-bottom: 1px solid #eee;
            font-size: 8px;
        }
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
    <div class="form-container">
        {{-- Header / Logo --}}
        <div class="header">
            <img src="{{ asset('logo.png') }}" alt="Logo" style="height: 25px;">
        </div>

        {{-- Title --}}
        <div class="title-bar">SURAT PENGAJUAN PELATIHAN</div>

        <div class="form-content">

            {{-- Kompetensi --}}
            <div class="form-section">
                <div class="section-header">Kompetensi : {{ $surat->kompetensi }}</div>
            </div>

            {{-- Judul + quick fields --}}
            <div class="form-section">
                <div class="section-header">Judul Pelatihan : {{ $surat->judul }}</div>
                <div class="section-content">
                    <div class="field-group">
                        <div class="field">
                            <span class="field-label">Lokasi</span>
                            <div class="field-value">{{ $surat->lokasi }}</div>
                        </div>
                        <div class="field">
                            <span class="field-label">Instruktur</span>
                            <div class="field-value">{{ $surat->instruktur }}</div>
                        </div>
                        <div class="field">
                            <span class="field-label">Sifat</span>
                            <div class="field-value">{{ $surat->sifat }}</div>
                        </div>
                        <div class="field">
                            <span class="field-label">Kompetensi</span>
                            <div class="field-value">{{ $surat->kompetensi_wajib }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Materi Global --}}
            <div class="form-section">
                <div class="section-header">Materi Global : {{ $surat->materi_global }}</div>
            </div>

            {{-- Program Pelatihan --}}
            <div class="form-section">
                <div class="section-header">
                    Pelatihan ini <strong>{{ $surat->program_pelatihan_ksp }}</strong> dalam Program Pelatihan PT. KSP
                </div>
            </div>

            {{-- Schedule - Updated to show tanggal pelaksanaan inline --}}
            <div class="form-section">
                <div class="section-content">
                    <table class="schedule-table" style="width:100%; border-collapse:separate; border-spacing:0 3px;">
                        <tr>
                            <td style="width:50%;">
                                <span class="field-label">Tanggal Pelaksanaan</span>
                                <div class="field-value" style="word-wrap: break-word;">
                                    @php
                                        $tanggalPelaksanaan = is_array($surat->tanggal_pelaksanaan)
                                            ? $surat->tanggal_pelaksanaan
                                            : json_decode($surat->tanggal_pelaksanaan ?? '[]', true);
                                        $formattedDates = [];
                                        
                                        if(!empty($tanggalPelaksanaan)) {
                                            foreach($tanggalPelaksanaan as $tgl) {
                                                $formattedDates[] = \Carbon\Carbon::parse($tgl)->format('d M Y');
                                            }
                                            echo implode(', ', $formattedDates);
                                        } else {
                                            echo $surat->tanggal_mulai 
                                                ? \Carbon\Carbon::parse($surat->tanggal_mulai)->format('d M Y') . ' - ' . 
                                                  \Carbon\Carbon::parse($surat->tanggal_selesai)->format('d M Y')
                                                : '-';
                                        }
                                    @endphp
                                </div>
                            </td>
                            <td style="width:50%;">
                                <span class="field-label">Durasi :</span>
                                <div class="field-value">{{ $surat->durasi }} Hari</div>
                            </td>
                        </tr>

                        {{-- 🔹 New row for Tanggal Mulai & Tanggal Selesai --}}
                        <tr>
                            <td>
                                <span class="field-label">Tanggal Mulai</span>
                                <div class="field-value">
                                    {{ $surat->tanggal_mulai ? \Carbon\Carbon::parse($surat->tanggal_mulai)->format('d M Y') : '-' }}
                                </div>
                            </td>
                            <td>
                                <span class="field-label">Tanggal Selesai</span>
                                <div class="field-value">
                                    {{ $surat->tanggal_selesai ? \Carbon\Carbon::parse($surat->tanggal_selesai)->format('d M Y') : '-' }}
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <span class="field-label">Tempat</span>
                                <div class="field-value">{{ $surat->tempat }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <span class="field-label">Penyelenggara</span>
                                <div class="field-value">{{ $surat->penyelenggara }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="field-label">Biaya</span>
                                <div class="field-value">{{ number_format($surat->biaya, 0, ',', '.') }}</div>
                            </td>
                            <td>
                                <span class="field-label">Per Orang/Paket</span>
                                <div class="field-value">{{ $surat->per_paket_or_orang }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <span class="field-label">Keterangan</span>
                                <div class="field-value" style="min-height:20px;">{{ $surat->keterangan }}</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Peserta --}}
            <div class="form-section">
                <div class="section-header">Peserta</div>
                <div class="section-content">
                    <div class="participants-list">
                        @foreach($surat->participants as $p)
                            <div class="participant-item">
                                {{ $p->user->name ?? '-' }}
                                ({{ $p->registration_id ?? '-' }}) -
                                {{ $p->jabatan_full ?? '-' }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Tujuan Peserta Section - Added to match PDF --}}
            <div class="form-section">
                <div class="section-header">Tujuan Peserta</div>
                <div class="section-content">
                    <div class="field-value" style="min-height:15px;">{{ $surat->tujuan_peserta ?? '-' }}</div>
                </div>
            </div>

            {{-- Approval Section - With proper null checks --}}
            @php
                $latestRound = $surat->approvals->max('round') ?? 0;
                
                // Get all approvals and sort by sequence (parafs first, then signatures)
                $parafs = $surat->approvals
                    ->where('type', 'paraf')
                    ->where('round', $latestRound)
                    ->sortBy('sequence');
                    
                $signatures = $surat->approvals
                    ->where('type', 'signature')
                    ->where('round', $latestRound)
                    ->sortBy('sequence');
                
                $parafsCount = $parafs->count();
                $signaturesCount = $signatures->count();
                
                // Fixed labels for signature positions
                $signatureLabels = ['Mengusulkan', 'Mengetahui', 'Menyetujui'];
            @endphp

            <div style="margin-top:5px;">
                <div class="section-content">
                    {{-- Paraf section - Always show 3 boxes --}}
                    <div style="font-weight:bold; font-size:8px; margin-bottom:4px;">Paraf</div>
                    <div class="form-section" style="margin-top:0; border:0;">
                        <table style="width:100%; margin-top:2px; text-align:center; border-collapse:collapse;">
                            <tr>
                                @for($i = 0; $i < 3; $i++)
                                    @php
                                        // Safely check if the index exists before accessing
                                        $showParafData = $i < $parafsCount;
                                        // Use null coalescing to prevent "undefined array key" errors
                                        $paraf = $showParafData ? ($parafs->values()->get($i) ?? null) : null;
                                        $showImg = $showParafData && $paraf && $paraf->status === 'approved' && !empty($paraf->preview_url);
                                    @endphp
                                    <td class="sign-cell" style="border:1px solid #ddd; width:33.33%;">
                                        <div class="sign-image-box">
                                            @if($showImg)
                                                <img src="{{ $paraf->preview_url }}" alt="Paraf">
                                            @else
                                                <div class="sign-blank"></div>
                                            @endif
                                        </div>
                                        <div class="sign-meta">
                                            @if($showParafData && $paraf)
                                                {{ $paraf->user->name ?? '-' }}
                                                <small>{{ $paraf->user->registration_id ?? '-' }}</small>
                                                <small>{{ $paraf->user->jabatan_full ?? ($paraf->user->jabatan->name ?? '-') }}</small>
                                            @endif
                                        </div>
                                        @if($showParafData && $paraf)
                                            <div class="sign-role">(Paraf)</div>
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        </table>
                    </div>

                    {{-- Signature section - Always show exactly 3 signature boxes --}}
                    <div style="font-weight:bold; font-size:8px; margin:10px 0 4px;">Tanda Tangan</div>
                    <div class="form-section" style="margin-top:0; border:0;">
                        <table style="width:100%; text-align:center; border-collapse:collapse; margin-bottom:5px;">
                            <tr class="sign-grid-header">
                                @foreach($signatureLabels as $label)
                                    <td style="border:1px solid #ddd; border-bottom:none; width:33.33%;">{{ $label }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                @for($i = 0; $i < 3; $i++)
                                    @php
                                        // Safely check if the index exists before accessing
                                        $showData = $i < $signaturesCount;
                                        // Use values() and get() for safer access to collection items
                                        $signature = $showData ? ($signatures->values()->get($i) ?? null) : null;
                                        $showImg = $showData && $signature && $signature->status === 'approved' && !empty($signature->preview_url);
                                    @endphp
                                    <td class="sign-cell" style="border:1px solid #ddd; border-top:none; width:33.33%;">
                                        <div class="sign-image-box">
                                            @if($showImg)
                                                <img src="{{ $signature->preview_url }}" alt="Signature">
                                            @else
                                                <div class="sign-blank"></div>
                                            @endif
                                        </div>
                                        <div class="sign-meta">
                                            @if($showData && $signature)
                                                {{ $signature->user->name ?? '-' }}
                                                <small>{{ $signature->user->registration_id ?? '-' }}</small>
                                                <small>{{ $signature->user->jabatan_full ?? ($signature->user->jabatan->name ?? '-') }}</small>
                                            @endif
                                        </div>
                                        <div class="sign-role">{{ ($showData && $signature) ? '(Tanda Tangan)' : '' }}</div>
                                    </td>
                                @endfor
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
