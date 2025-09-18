<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Form Evaluasi Training Level 3</title>
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
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 4px;
            width: 100%;
            min-height: 35px;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            flex: 1;
            max-width: 70%;
        }
        
        .logo {
            width: 35px;
            height: 25px;
            background-color: #87CEEB;
            margin-right: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 6px;
            font-weight: bold;
            color: white;
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
            flex-shrink: 0;
            align-self: flex-start;
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
        
        .field-colon {
            width: 15px;
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
            width: 45%;
            text-align: left !important;
            padding-left: 4px;
        }
        
        .col-applied {
            width: 15%;
        }
        
        .col-freq, .col-result {
            width: 17.5%;
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
    </style>
</head>
<body>
    <div class="header">
        <div class="header-top">
            <table style="width: 100%; border: none; border-collapse: collapse;">
                <tr>
                    <td style="width: 70%; vertical-align: top; border: none; padding: 0;">
                        <div class="logo-section">
                            <img src="{{ public_path('logo.png') }}" alt="Company Logo" style="width: auto; height: 30px; margin-right: 6px;">
                        </div>
                    </td>
                    <td style="width: 30%; vertical-align: top; text-align: right; border: none; padding: 0;">
                        <div class="form-type">
                            <div><em>Evaluasi Training Level 3</em></div>
                            <div><em>Form ini diisi oleh peserta training</em></div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="main-title">FORM EVALUASI TRAINING PT KRAKATAU SARANA PROPERTI</div>
        <div class="subtitle">(KEPALA URUSAN KE ATAS)</div>

        <div class="form-fields">
            <div class="field-row">
                <div class="field-label">Nama :</div>
                <div class="field-value">{{ $evaluasi->user->name ?? '' }}</div>
            </div>
            <div class="field-row">
                <div class="field-label">Training yang Diikuti :</div>
                <div class="field-value">{{ $evaluasi->pelatihan->judul ?? '' }}</div>
            </div>
            <div class="field-row">
                <div class="field-label">Jabatan Saat Pengajuan :</div>
                <div class="field-value">{{ $jabatan_saat_pengajuan ?? '' }}</div>
            </div>
            <div class="field-row">
                <div class="field-label">Atasan Saat Pengajuan :</div>
                <div class="field-value">{{ $atasan_saat_pengajuan ?? '' }}</div>
            </div>
        </div>


        <table class="evaluation-table">
            <thead>
                <tr>
                    <th class="col-no">No.</th>
                    <th class="col-activity">Aktivitas <em>(Action Plan*)</em></th>
                    <th class="col-applied">Diaplikasikan<br>di Pekerjaan</th>
                    <th class="col-freq">Frekuensi<sup>**)</sup></th>
                    <th class="col-result">Hasil<sup>***)</sup></th>
                </tr>
            </thead>
            <tbody>
                @foreach($evaluasi->actionPlans as $index => $plan)
                <tr>
                    <td>{{ $index + 1 }}.</td>
                    <td class="col-activity">{{ $plan->action_plan }}</td>
                    <td>{{ $plan->diaplikasikan ? 'Ya' : 'Tidak' }}</td>
                    <td>{{ $plan->frekuensi }}</td>
                    <td>{{ $plan->hasil }}</td>
                </tr>
                @endforeach
                @for($i = $evaluasi->actionPlans->count(); $i < 5; $i++)
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
            <sup>*)</sup> <em>Action Plan</em>: rencana langkah-langkah dalam mengaplikasikan materi pelatihan<br>
            <sup>**)</sup> Frekuensi: 0 (tidak pernah), 1 (sekali), 2 (sering), 3 (selalu)<br>
            <sup>***)</sup> Hasil: 1 (tidak berhasil), 2 (cukup berhasil), 3 (berhasil), 4 (sangat berhasil)
        </div>

        <div class="section">
            <div class="section-title">Setelah mengikuti training ini, saya mampu untuk:</div>
            <div class="dotted-line">{{ $evaluasi->manfaat_pelatihan ?? '' }}</div>
            <div class="dotted-line"></div>
            <div class="dotted-line"></div>
        </div>

        <div class="feedback-section">
            <div class="section-title">Apa yang dapat dilakukan oleh HC Department untuk membantu Anda mengaplikasikan hasil training?</div>
            
            @php $fb = $evaluasi->feedbacks->first(); @endphp
            
            <div class="feedback-item">
                {{ $fb && $fb->telah_mampu ? 'Ya' : 'Tidak' }} - Saya telah mampu mengaplikasikannya dalam pekerjaan
            </div>
            
            <div class="feedback-item">
                {{ $fb && $fb->membantu_mengaplikasikan ? 'Ya' : 'Tidak' }} - Membantu cara mengaplikasikan materi training
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
            <div class="section-title">Secara keseluruhan, saya menilai training tersebut dapat meningkatkan kinerja:</div>
            
            <div class="performance-options">
                <div class="performance-item {{ $evaluasi->kinerja == 0 ? 'selected' : '' }}">
                    <span>Tidak sama sekali</span>
                </div>
                <div class="performance-item {{ $evaluasi->kinerja == 1 ? 'selected' : '' }}">
                    <span>Cukup Membantu</span>
                </div>
                <div class="performance-item {{ $evaluasi->kinerja == 2 ? 'selected' : '' }}">
                    <span>Sangat Membantu</span>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Saran/ masukan penyelenggaraan training sejenis:</div>
            <div class="dotted-line">{{ $evaluasi->saran ?? '' }}</div>
            <div class="dotted-line"></div>
            <div class="dotted-line"></div>
        </div>
    </div>
</body>
</html>