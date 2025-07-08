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
            background-color: #4a90e2;
            color: white;
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
            margin-bottom: 15px;
        }
        
        .section-header {
            background-color: #e8e8e8;
            padding: 6px 10px;
            font-weight: bold;
            font-size: 12px;
            border-bottom: 1px solid #333;
        }
        
        .section-content {
            padding: 10px;
        }
        
        .field-group {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 10px;
        }
        
        .field {
            flex: 1;
            min-width: 200px;
        }
        
        .field-label {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 4px;
            display: block;
        }
        
        .field-value {
            border: 1px solid #999;
            padding: 5px 8px;
            background-color: #f9f9f9;
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
            KRAKATAU
            <span style="margin-left: auto; font-size: 10px;">SARANA PROPERTI</span>
        </div>
        
        <div class="title-bar">SURAT PENGAJUAN PELATIHAN</div>
        
        <div class="form-content">
            <!-- Kompetensi Section -->
            <div class="form-section">
                <div class="section-header">Kompetensi</div>
                <div class="section-content">
                    <div class="field-value">{{ $surat->kompetensi }}</div>
                </div>
            </div>
            
            <!-- Judul Pelatihan Section -->
            <div class="form-section">
                <div class="section-header">Judul Pelatihan :</div>
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
                    
                    <div class="field">
                        <span class="field-label">Judul:</span>
                        <div class="field-value">{{ $surat->judul }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Materi Global Section -->
            <div class="form-section">
                <div class="section-header">Materi Global :</div>
                <div class="section-content">
                    <div class="field-value">{{ $surat->materi_global }}</div>
                </div>
            </div>
            
            <!-- Program Pelatihan Section -->
            <div class="form-section">
                <div class="section-content">
                    <div style="font-weight: bold; margin-bottom: 10px;">
                        Pelatihan ini <strong>{{ $surat->program_pelatihan_ksp }}</strong> dalam Program Pelatihan PT. KSP
                    </div>
                </div>
            </div>
            
            <!-- Schedule Section -->
            <div class="form-section">
                <div class="section-content">
                    <div class="field-group">
                        <div class="field">
                            <span class="field-label">Tanggal</span>
                            <div class="field-value">
                                {{ \Carbon\Carbon::parse($surat->tanggal_mulai)->format('d M Y') }} - 
                                {{ \Carbon\Carbon::parse($surat->tanggal_selesai)->format('d M Y') }}
                            </div>
                        </div>
                        <div class="field">
                            <span class="field-label">Durasi :</span>
                            <div class="field-value">{{ $surat->durasi }} Hari</div>
                        </div>
                    </div>
                    
                    <div class="field" style="margin-bottom: 15px;">
                        <span class="field-label">Tempat</span>
                        <div class="field-value">{{ $surat->tempat }}</div>
                    </div>
                    
                    <div class="field" style="margin-bottom: 15px;">
                        <span class="field-label">Penyelenggara</span>
                        <div class="field-value">{{ $surat->penyelenggara }}</div>
                    </div>
                    
                    <div class="field-group" style="margin-bottom: 15px;">
                        <div class="field">
                            <span class="field-label">Biaya</span>
                            <div class="field-value">{{ $surat->biaya }}</div>
                        </div>
                        <div class="field">
                            <span class="field-label">Per Orang/Paket</span>
                            <div class="field-value">{{ $surat->per_paket_or_orang }}</div>
                        </div>
                    </div>
                    
                    <div class="field">
                        <span class="field-label">Keterangan</span>
                        <div class="field-value">{{ $surat->keterangan }}</div>
                    </div>
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
            
            <div class="creator-info">
                <strong>Dibuat oleh:</strong> {{ $surat->creator->name ?? '-' }}
            </div>
        </div>
    </div>
</body>
</html>