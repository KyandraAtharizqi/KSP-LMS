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
        }
        
        .form-container {
            background-color: white;
            border: 2px solid #333;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .header {
            background-color: #ffffff;
            color: black;
            padding: 10px 15px;
            font-weight: bold;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        
        .header::before {
            content: "";
            width: 0;
            height: 0;
            border-left: 12px solid white;
            border-top: 8px solid transparent;
            border-bottom: 8px solid transparent;
            margin-right: 10px;
        }
        
        .title-bar {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 8px;
            font-weight: bold;
            font-size: 14px;
        }
        
        .form-content {
            padding: 15px;
        }
        
        .form-row {
            display: flex;
            margin-bottom: 12px;
            align-items: flex-start;
        }
        
        .form-section {
            border: 1px solid #333;
            margin-bottom: 0px;
        }
        
        .section-header {
            background-color: #FFFFFF;
            padding: 6px 10px;
            font-weight: bold;
            font-size: 12px;
            border-bottom: 0px solid #333;
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
            min-width: 0px;
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
            background-color: #ffffff;
            min-height: 18px;
            font-size: 12px;
        }
        
        .checkbox-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 5px;
        }
        
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
        }
        
        .date-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .date-boxes {
            display: flex;
            gap: 2px;
        }
        
        .date-box {
            width: 20px;
            height: 20px;
            border: 1px solid #333;
            text-align: center;
            font-size: 12px;
            line-height: 18px;
        }
        
        .participants-list {
            margin-top: 10px;
        }
        
        .participant-item {
            padding: 5px;
            border-bottom: 1px solid #eee;
            font-size: 12px;
        }
        
        .creator-info {
            margin-top: 20px;
            text-align: right;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="header">
            <img src="{{ asset('logoksp.png') }}" alt="Logo" style="height: 40px;">
        </div>
        
        <div class="title-bar">SURAT PENGAJUAN PELATIHAN</div>
        
        <div class="form-content">
            <!-- Kompetensi Section -->
            <div class="form-section">
                <div class="section-header">Kompetensi : {{ $surat->kompetensi }}</div>
            </div>
            
            <!-- Judul Pelatihan Section -->
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
            
            <!-- Materi Global Section -->
            <div class="form-section">
                <div class="section-header">Materi Global : {{ $surat->materi_global }}</div>
            </div>
            
            <!-- Program Pelatihan Section -->
            <div class="form-section">
                <div class="section-header">
                    Pelatihan ini <strong>{{ $surat->program_pelatihan_ksp }}</strong> dalam Program Pelatihan PT. KSP
                </div>
            </div>
            
            <!-- Schedule Section -->
            <div class="form-section">
                <div class="section-content">
                    <table class="schedule-table" style="width: 100%; border-collapse: separate; border-spacing: 0 5px;">
                        <tr>
                            <td style="width: 50%;">
                                <span class="field-label">Tanggal</span>
                                <div class="field-value">
                                    {{ \Carbon\Carbon::parse($surat->tanggal_mulai)->format('d M Y') }} - 
                                    {{ \Carbon\Carbon::parse($surat->tanggal_selesai)->format('d M Y') }}
                                </div>
                            </td>
                            <td style="width: 50%;">
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
                                <div class="field-value" style="min-height: 30px;">{{ $surat->keterangan }}</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Participants Section -->
            <div class="form-section">
                <div class="section-header">Peserta</div>
                <div class="section-content">
                    <div class="participants-list">
                        @foreach($surat->participants as $p)
                        <div class="participant-item">
                            {{ $p->user->name }} ({{ $p->user->registration_id }}) - {{ $p->user->jabatan->name ?? '-' }} / {{ $p->user->department->name ?? '-' }}
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>


            <!-- Approval Section -->
            <div style="margin-top: 10px;">
                <div class="section-content">

                    {{-- Paraf Section --}}
                    @if ($surat->approvals->where('type', 'paraf')->count())
                        <div style="font-weight: bold; font-size: 13px; margin-bottom: 8px;">Paraf</div>
                        <table style="width: 100%; margin-top: 5px; text-align: center;">
                            <tr>
                                @foreach ($surat->approvals->where('type', 'paraf')->sortBy('sequence') as $paraf)
                                    <td>
                                        <div style="height: 60px;">
                                            @if ($paraf->status === 'approved' && $paraf->image_path)
                                                <img src="{{ asset('storage/' . $paraf->image_path) }}" alt="Paraf" style="height: 50px;">
                                            @else
                                                <div style="height: 50px; border-bottom: 1px solid #000;"></div>
                                            @endif
                                        </div>
                                        <div style="font-size: 12px; margin-top: 4px;">
                                            {{ $paraf->user->name ?? '-' }}<br>
                                            <small>{{ $paraf->user->registration_id ?? '-' }}</small><br>
                                            <small>{{ $paraf->user->jabatan_full ?? '-' }}</small>
                                        </div>
                                        <div style="font-size: 10px; color: #888;">(Paraf)</div>
                                    </td>
                                @endforeach
                            </tr>
                        </table>
                    @endif

                    {{-- Signature Section --}}
                    @if ($surat->approvals->where('type', 'signature')->count())
                        <div style="font-weight: bold; font-size: 13px; margin: 25px 0 8px;">Tanda Tangan</div>
                        <table style="width: 100%; text-align: center;">
                            <tr style="font-size: 10px; color: #888;">
                                <td>Mengusulkan</td>
                                <td>Mengetahui</td>
                                <td>Menyetujui</td>
                            </tr>
                            <tr>
                                @foreach ($surat->approvals->where('type', 'signature')->sortBy('sequence') as $signature)
                                    <td>
                                        <div style="height: 60px;">
                                            @if ($signature->status === 'approved' && $signature->image_path)
                                                <img src="{{ asset('storage/' . $signature->image_path) }}" alt="Signature" style="height: 50px;">
                                            @else
                                                <div style="height: 50px; border-bottom: 1px solid #000;"></div>
                                            @endif
                                        </div>
                                        <div style="font-size: 12px; margin-top: 4px;">
                                            {{ $signature->user->name ?? '-' }}<br>
                                            <small>{{ $signature->user->registration_id ?? '-' }}</small><br>
                                            <small>{{ $signature->user->jabatan_full ?? '-' }}</small>
                                        </div>
                                        <div style="font-size: 10px; color: #888;">(Tanda Tangan)</div>
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
