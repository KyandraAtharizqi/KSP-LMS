<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Pengajuan Pelatihan</title>
    <style>
        @page { margin: 20px 25px; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
            color: #000;
        }
        .form-container {
            background-color: #fff;
            border: 2px solid #333;
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            background-color: #ffffff;
            color: #000;
            padding: 10px 15px;
            font-weight: bold;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        .title-bar {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 8px;
            font-weight: bold;
            font-size: 14px;
        }
        .form-content {
            padding: 15px;
        }
        .form-section {
            border: 1px solid #333;
            margin-bottom: 0;
        }
        .section-header {
            background-color: #fff;
            padding: 6px 10px;
            font-weight: bold;
            font-size: 12px;
            border-bottom: 0;
        }
        .section-content {
            padding: 2px 10px 10px 10px;
        }
        .field-group {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 5px;
        }
        .field {
            flex: 1;
            min-width: 0;
        }
        .field-label {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 5px;
            display: block;
        }
        .field-value {
            border: 1px solid #999;
            padding: 5px 8px;
            background-color: #fff;
            min-height: 18px;
            font-size: 12px;
        }
        .participants-list {
            margin-top: 10px;
        }
        .participant-item {
            padding: 5px 0;
            border-bottom: 1px solid #eee;
            font-size: 12px;
        }
        table.schedule-table td {
            vertical-align: top;
        }

        /* Signature grid */
        .sign-grid-header {
            font-size: 10px;
            color: #888;
        }
        .sign-cell {
            text-align: center;
            vertical-align: top;
            padding: 0 5px;
        }
        .sign-image-box {
            height: 60px;
        }
        .sign-image-box img {
            height: 50px;
        }
        .sign-blank {
            height: 50px;
            border-bottom: 1px solid #000;
        }
        .sign-meta {
            font-size: 12px;
            margin-top: 4px;
            line-height: 1.2;
        }
        .sign-meta small {
            display: block;
            line-height: 1.1;
        }
        .sign-role {
            font-size: 10px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="form-container">
        {{-- Header --}}
        <div class="header">
            <img src="{{ public_path('logoksp.png') }}" alt="Logo" style="height: 40px;">
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
                    <table class="schedule-table" style="width:100%; border-collapse:separate; border-spacing:0 5px;">
                        <tr>
                            <td style="width:50%;">
                                <span class="field-label">Tanggal</span>
                                <div class="field-value">
                                    {{ \Carbon\Carbon::parse($surat->tanggal_mulai)->format('d M Y') }} -
                                    {{ \Carbon\Carbon::parse($surat->tanggal_selesai)->format('d M Y') }}
                                </div>
                            </td>
                            <td style="width:50%;">
                                <span class="field-label">Durasi :</span>
                                <div class="field-value">{{ $surat->durasi }} Hari</div>
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
                                <div class="field-value" style="min-height:30px;">{{ $surat->keterangan }}</div>
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

            {{-- Approval Section --}}
            @php
                $latestRound = $surat->approvals->max('round');
                $parafs = $surat->approvals
                    ->where('type','paraf')
                    ->where('round', $latestRound)
                    ->sortBy('sequence');
                $sigs = $surat->approvals
                    ->where('type','signature')
                    ->where('round', $latestRound)
                    ->sortBy('sequence');
            @endphp
            <div style="margin-top:10px;">
                <div class="section-content">

                    {{-- Paraf --}}
                    @if($parafs->count())
                        <div style="font-weight:bold; font-size:13px; margin-bottom:8px;">Paraf</div>
                        <table style="width:100%; margin-top:5px; text-align:center;">
                            <tr>
                                @foreach($parafs as $paraf)
                                    @php
                                        $pdfPath  = $paraf->pdf_path ?? null;
                                        $showImg  = $paraf->status === 'approved' && $pdfPath && is_readable($pdfPath);
                                    @endphp
                                    <td class="sign-cell">
                                        <div class="sign-image-box">
                                            @if($showImg)
                                                <img src="{{ $pdfPath }}" alt="Paraf">
                                            @else
                                                <div class="sign-blank"></div>
                                            @endif
                                        </div>
                                        <div class="sign-meta">
                                            {{ $paraf->user->name ?? '-' }}
                                            <small>{{ $paraf->user->registration_id ?? '-' }}</small>
                                            <small>{{ $paraf->user->jabatan_full ?? ($paraf->user->jabatan->name ?? '-') }}</small>
                                        </div>
                                        <div class="sign-role">(Paraf)</div>
                                    </td>
                                @endforeach
                            </tr>
                        </table>
                    @endif

                    {{-- Signature --}}
                    @if($sigs->count())
                        <div style="font-weight:bold; font-size:13px; margin:25px 0 8px;">Tanda Tangan</div>
                        <table style="width:100%; text-align:center;">
                            {{-- Optional labels row; comment/remove if undesired --}}
                            <tr class="sign-grid-header">
                                <td>Mengusulkan</td>
                                <td>Mengetahui</td>
                                <td>Menyetujui</td>
                            </tr>
                            <tr>
                                @foreach($sigs as $signature)
                                    @php
                                        $pdfPath = $signature->pdf_path ?? null;
                                        $showImg = $signature->status === 'approved' && $pdfPath && is_readable($pdfPath);
                                    @endphp
                                    <td class="sign-cell">
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
                                @endforeach
                            </tr>
                        </table>
                    @endif

                </div>
            </div>

        </div>
    </div>
</body>
</html>
