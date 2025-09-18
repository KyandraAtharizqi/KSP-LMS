<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Form Evaluasi Training Level 3 - Atasan</title>
    <style>
        @page {
            margin: 10mm;
            size: A4;
        }
        
        * {
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        
        body { 
            font-family: Arial, sans-serif; 
            font-size: 8px; 
            line-height: 1.1; 
            margin: 0;
            padding: 0;
            background: white;
            color: black;
        }
        
        .header {
            border: 2px solid #000;
            margin-bottom: 5px;
            padding: 4px;
        }
        
        .header-top {
            margin-bottom: 4px;
            width: 100%;
            min-height: 35px;
            position: relative;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
        }
        
        .company-info h2 {
            font-size: 10px;
            font-weight: bold;
            margin: 0;
            letter-spacing: 0.5px;
        }
        
        .company-info p {
            font-size: 8px;
            margin: 0;
        }
        
        .form-type {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            font-style: italic;
            background-color: #f8f8f8;
            width: 160px;
            font-size: 7px;
            position: absolute;
            top: 0;
            right: 0;
        }
        
        .main-title {
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            margin: 5px 0 3px 0;
            letter-spacing: 0.5px;
        }
        
        .subtitle {
            text-align: center;
            font-size: 8px;
            font-weight: bold;
            margin: 3px 0 8px 0;
        }
        
        .form-fields {
            margin-bottom: 8px;
        }
        
        .field-row {
            display: flex;
            margin-bottom: 3px;
            align-items: baseline;
        }
        
        .field-label {
            width: 150px;
            font-size: 8px;
        }
        
        .field-value {
            flex: 1;
            border-bottom: 1px dotted #000;
            min-height: 10px;
            padding-left: 2px;
            font-size: 8px;
        }
        
        .evaluation-table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
            font-size: 7px;
        }
        
        .evaluation-table th,
        .evaluation-table td {
            border: 1px solid #000;
            padding: 2px;
            text-align: center;
            vertical-align: middle;
        }
        
        .evaluation-table th {
            background-color: #e8e8e8;
            font-weight: bold;
        }
        
        .col-no {
            width: 5%;
        }
        
        .col-activity {
            width: 40%;
            text-align: left !important;
            padding-left: 4px;
        }
        
        .col-applied {
            width: 15%;
        }
        
        .col-freq, .col-result {
            width: 20%;
        }
        
        .legend {
            font-size: 6px;
            margin: 4px 0;
            line-height: 1.2;
        }
        
        .section {
            margin: 5px 0;
        }
        
        .section-title {
            font-weight: bold;
            margin-bottom: 3px;
            font-size: 8px;
            line-height: 1.2;
        }
        
        .dotted-line {
            border-bottom: 1px dotted #000;
            height: 10px;
            margin: 1px 0;
            padding-left: 2px;
            font-size: 8px;
            line-height: 10px;
        }
        
        .feedback-section {
            margin: 5px 0;
        }
        
        .feedback-item {
            margin: 2px 0;
            font-size: 8px;
            line-height: 1.2;
        }
        
        .performance-section {
            margin: 5px 0;
        }
        
        .performance-options {
            display: flex;
            justify-content: space-around;
            margin: 4px 0;
            text-align: center;
        }
        
        .performance-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 7px;
            text-align: center;
            flex: 1;
            border: 1px solid #ccc;
            padding: 3px;
            margin: 0 1px;
        }
        
        .performance-item.selected {
            background-color: #000;
            color: white;
            border: 1px solid #000;
        }
        
        .signature-section {
            margin-top: 10px;
            display: flex;
            justify-content: flex-end;
        }
        
        .signature-box {
            text-align: center;
            width: 200px;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            height: 40px;
            margin: 10px 0 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-top">
            <div class="logo-section">
                <img src="{{ public_path('logo.png') }}" alt="Company Logo" style="width: auto; height: 30px; margin-right: 6px;">
            </div>
            <div class="form-type">
                <div><em>Evaluasi Training Level 3</em></div>
                <div><em>Form ini diisi oleh atasan peserta</em></div>
            </div>
        </div>

        <div class="main-title">FORM EVALUASI TRAINING PT KRAKATAU SARANA PROPERTI</div>
        <div class="subtitle">(EVALUASI ATASAN)</div>

        <div class="form-fields">
            <div class="field-row">
                <div class="field-label">Nama Peserta :</div>
                <div class="field-value">{{ $evaluasiAtasan->user->name ?? '' }}</div>
            </div>
            <div class="field-row">
                <div class="field-label">Training yang Diikuti :</div>
                <div class="field-value">{{ $evaluasiAtasan->pelatihan->judul ?? '' }}</div>
            </div>
            <div class="field-row">
                <div class="field-label">Kode Pelatihan :</div>
                <div class="field-value">{{ $evaluasiAtasan->kode_pelatihan ?? '' }}</div>
            </div>
            <div class="field-row">
                <div class="field-label">Jabatan Peserta Saat Pelatihan :</div>
                <div class="field-value">{{ $evaluasiAtasan->participantSnapshot->jabatan_full ?? ($evaluasiAtasan->user->jabatan->name ?? '') }}</div>
            </div>
            <div class="field-row">
                <div class="field-label">Department :</div>
                <div class="field-value">{{ $evaluasiAtasan->participantSnapshot->department->name ?? ($evaluasiAtasan->user->department->name ?? '') }}</div>
            </div>
            <div class="field-row">
                <div class="field-label">Nama Atasan :</div>
                <div class="field-value">{{ $evaluasiAtasan->atasan->name ?? '' }}</div>
            </div>
        </div>

        <table class="evaluation-table">
            <thead>
                <tr>
                    <th class="col-no">No.</th>
                    <th class="col-activity">Tujuan Pembelajaran</th>
                    <th class="col-applied">Diaplikasikan<br>di Pekerjaan</th>
                    <th class="col-freq">Frekuensi<sup>*)</sup></th>
                    <th class="col-result">Hasil<sup>**)</sup></th>
                </tr>
            </thead>
            <tbody>
                @foreach($evaluasiAtasan->tujuanPembelajarans as $index => $tujuan)
                <tr>
                    <td>{{ $index + 1 }}.</td>
                    <td class="col-activity">{{ $tujuan->tujuan_pembelajaran }}</td>
                    <td>{{ $tujuan->diaplikasikan ? 'Ya' : 'Tidak' }}</td>
                    <td>{{ $tujuan->frekuensi }}</td>
                    <td>{{ $tujuan->hasil }}</td>
                </tr>
                @endforeach
                @for($i = $evaluasiAtasan->tujuanPembelajarans->count(); $i < 5; $i++)
                <tr>
                    <td>{{ $i + 1 }}.</td>
                    <td class="col-activity"></td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                @endfor
            </tbody>
        </table>

        <div class="legend">
            <strong>Keterangan:</strong><br>
            <sup>*)</sup> Frekuensi: 0 (tidak pernah), 1 (sekali), 2 (sering), 3 (selalu)<br>
            <sup>**)</sup> Hasil: 1 (tidak berhasil), 2 (cukup berhasil), 3 (berhasil), 4 (sangat berhasil)
        </div>

        <div class="section">
            <div class="section-title">Setelah mengikuti training ini, peserta mampu untuk:</div>
            <div class="dotted-line">{{ $evaluasiAtasan->manfaat_pelatihan ?? '' }}</div>
            <div class="dotted-line"></div>
            <div class="dotted-line"></div>
        </div>

        <div class="feedback-section">
            <div class="section-title">Apa yang dapat dilakukan oleh HC Department untuk membantu peserta mengaplikasikan hasil training?</div>
            
            @php $fb = $evaluasiAtasan->feedbacks->first(); @endphp
            
            <div class="feedback-item">
                {{ $fb && $fb->telah_mampu ? 'Ya' : 'Tidak' }} - Peserta telah mampu mengaplikasikannya dalam pekerjaan
            </div>
            
            <div class="feedback-item">
                Materi training tidak dapat diaplikasikan karena: {{ $fb->tidak_diaplikasikan_karena ?? '................................................' }}
            </div>
            
            <div class="feedback-item">
                Memberikan informasi mengenai: {{ $fb->memberikan_informasi_mengenai ?? '................................................' }}
            </div>
            
            <div class="feedback-item">
                Lain-lain: {{ $fb->lain_lain ?? '................................................' }}
            </div>
        </div>

        <div class="performance-section">
            <div class="section-title">Secara keseluruhan, saya menilai training tersebut dapat meningkatkan kinerja peserta:</div>
            
            <div class="performance-options">
                <div class="performance-item {{ $evaluasiAtasan->kinerja == 1 ? 'selected' : '' }}">
                    <span>Tidak sama sekali</span>
                </div>
                <div class="performance-item {{ $evaluasiAtasan->kinerja == 2 ? 'selected' : '' }}">
                    <span>Kurang Membantu</span>
                </div>
                <div class="performance-item {{ $evaluasiAtasan->kinerja == 3 ? 'selected' : '' }}">
                    <span>Cukup Membantu</span>
                </div>
                <div class="performance-item {{ $evaluasiAtasan->kinerja == 4 ? 'selected' : '' }}">
                    <span>Sangat Membantu</span>
                </div>
                <div class="performance-item {{ $evaluasiAtasan->kinerja == 5 ? 'selected' : '' }}">
                    <span>Luar Biasa</span>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Saran/masukan untuk penyelenggaraan training sejenis:</div>
            <div class="dotted-line">{{ $evaluasiAtasan->saran ?? '' }}</div>
            <div class="dotted-line"></div>
            <div class="dotted-line"></div>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <div style="font-size: 8px; margin-bottom: 5px;">
                    {{ now()->format('d F Y') }}
                </div>
                <div style="font-size: 8px; margin-bottom: 5px;">
                    Atasan/Supervisor,
                </div>
                <div class="signature-line"></div>
                <div style="font-size: 8px; font-weight: bold;">
                    {{ strtoupper($evaluasiAtasan->atasan->name ?? '') }}
                </div>
                <div style="font-size: 7px;">
                    {{ $evaluasiAtasan->atasan->registration_id ?? '' }}
                </div>
                <div style="font-size: 7px;">
                    {{ $evaluasiAtasan->atasan->jabatan->name ?? '' }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
