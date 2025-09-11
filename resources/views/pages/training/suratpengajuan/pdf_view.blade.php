<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Pengajuan Pelatihan</title>
    <style>
        @page { margin: 15px 20px; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 8px;
            color: #000;
            line-height: 1.2;
        }
        .form-container {
            background-color: #fff;
            border: 1px solid #333;
            width: 100%;
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
            color: #fff;
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
            background-color: #fff;
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
            background-color: #fff;
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
        table.schedule-table td {
            vertical-align: top;
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
    <div class="form-container">
        {{-- Header --}}
        <div class="header">
            <img src="{{ public_path('logo.png') }}" alt="Logo" style="height: 25px;">
        </div>

        {{-- Title --}}
        <div class="title-bar">SURAT PENGAJUAN PELATIHAN</div>

        <div class="form-content">

            {{-- Kompetensi --}}
            <div class="form-section">
                <div class="section-header">Kompetensi : {{ $surat->kompetensi }}</div>
            </div>

            {{-- Judul & quick fields --}}
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

            {{-- Program --}}
            <div class="form-section">
                <div class="section-header">
                    Pelatihan ini <strong>{{ $surat->program_pelatihan_ksp }}</strong> dalam Program Pelatihan PT. KSP
                </div>
            </div>

            {{-- Schedule --}}
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

            {{-- Tujuan Peserta --}}
            <div class="form-section">
                <div class="section-header">Tujuan Peserta</div>
                <div class="section-content">
                    <div class="field-value" style="min-height:15px;">{{ $surat->tujuan_peserta ?? '-' }}</div>
                </div>
            </div>

            {{-- Approval Section - Improved layout to prevent cutoffs --}}
            @php
                $latestRound = $surat->approvals->max('round') ?? 0;
                $parafs = $surat->approvals
                    ->where('type','paraf')
                    ->where('round', $latestRound)
                    ->sortBy('sequence');
                $sigs = $surat->approvals
                    ->where('type','signature')
                    ->where('round', $latestRound)
                    ->sortBy('sequence');
                
                // Calculate how many rows we need to prevent cutoffs
                $parafsCount = $parafs->count();
                $parafsPerRow = min(4, $parafsCount); // Max 4 per row
                $parafRows = $parafsCount > 0 ? ceil($parafsCount / $parafsPerRow) : 0;
                
                $sigsCount = $sigs->count();
                $sigsPerRow = min(3, $sigsCount > 0 ? $sigsCount : 3); // Max 3 per row 
                $sigRows = $sigsCount > 0 ? ceil($sigsCount / $sigsPerRow) : 0;
            @endphp

            <div style="margin-top:5px;">
                <div class="section-content">

                    {{-- Paraf with row-based layout - Always show at least one row with three boxes --}}
                    <div style="font-weight:bold; font-size:8px; margin-bottom:4px;">Paraf</div>
                    <div class="form-section" style="margin-top:0; border:0;">
                        <table style="width:100%; margin-top:2px; text-align:center; border-collapse:collapse;">
                            <tr>
                                @php
                                    // Always display at least 3 paraf boxes, even if none assigned
                                    $minParafBoxes = 3;
                                    $actualParafs = $parafsCount;
                                    $boxesToShow = max($minParafBoxes, $actualParafs);
                                @endphp
                                
                                @for($i = 0; $i < $boxesToShow; $i++)
                                    @php
                                        $showParafData = $i < $parafsCount;
                                        $paraf = $showParafData ? $parafs[$i] : null;
                                        $pdfPath = $showParafData ? ($paraf->pdf_path ?? null) : null;
                                        $showImg = $showParafData && $paraf->status === 'approved' && $pdfPath && is_readable($pdfPath);
                                    @endphp
                                    <td class="sign-cell" style="border:1px solid #ddd; width:{{ 100/$boxesToShow }}%;">
                                        <div class="sign-image-box">
                                            @if($showImg)
                                                <img src="{{ $pdfPath }}" alt="Paraf">
                                            @else
                                                <div class="sign-blank"></div>
                                            @endif
                                        </div>
                                        <div class="sign-meta">
                                            {{ $showParafData ? ($paraf->user->name ?? '-') : '' }}
                                            @if($showParafData)
                                                <small>{{ $paraf->user->registration_id ?? '-' }}</small>
                                                <small>{{ $paraf->user->jabatan_full ?? ($paraf->user->jabatan->name ?? '-') }}</small>
                                            @endif
                                        </div>
                                        @if($showParafData)
                                            <div class="sign-role">(Paraf)</div>
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        </table>
                    </div>

                    {{-- Signature with row-based layout - Only show if there are signatures --}}
                    @if($sigsCount > 0)
                        <div style="font-weight:bold; font-size:8px; margin:10px 0 4px;">Tanda Tangan</div>
                        <div class="form-section" style="margin-top:0; border:0;">
                            @for($row = 0; $row < $sigRows; $row++)
                                <table style="width:100%; text-align:center; border-collapse:collapse; margin-bottom:5px;">
                                    <tr class="sign-grid-header">
                                        @php
                                            $labels = ['Mengusulkan', 'Mengetahui', 'Menyetujui'];
                                            $defaultLabel = 'Tanda Tangan';
                                        @endphp
                                        
                                        @for($col = 0; $col < $sigsPerRow; $col++)
                                            @php
                                                $idx = $row * $sigsPerRow + $col;
                                                $showCell = $idx < $sigsCount;
                                                $cellLabel = ($row == 0 && $col < count($labels)) ? $labels[$col] : $defaultLabel;
                                            @endphp
                                            
                                            @if($showCell)
                                                <td style="border:1px solid #ddd; border-bottom:none;">{{ $cellLabel }}</td>
                                            @else
                                                <td style="border:none;"></td>
                                            @endif
                                        @endfor
                                    </tr>
                                    <tr>
                                        @php $startIdx = $row * $sigsPerRow; @endphp
                                        @for($i = $startIdx; $i < min($startIdx + $sigsPerRow, $sigsCount); $i++)
                                            @php
                                                $signature = $sigs[$i];
                                                $pdfPath = $signature->pdf_path ?? null;
                                                $showImg = $signature->status === 'approved' && $pdfPath && is_readable($pdfPath);
                                            @endphp
                                            <td class="sign-cell" style="border:1px solid #ddd; border-top:none;">
                                                <div class="sign-image-box">
                                                    @if($showImg)
                                                        <img src="{{ $pdfPath }}" alt="Signature">
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
                                            </td>
                                            {{-- Fill empty cells to maintain structure --}}
                                            @if($i == $sigsCount-1 && ($i - $startIdx + 1) < $sigsPerRow)
                                                @for($j = 0; $j < $sigsPerRow - ($i - $startIdx + 1); $j++)
                                                    <td class="sign-cell" style="border:none;"></td>
                                                @endfor
                                            @endif
                                        @endfor
                                    </tr>
                                </table>
                            @endfor
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</body>
</html>