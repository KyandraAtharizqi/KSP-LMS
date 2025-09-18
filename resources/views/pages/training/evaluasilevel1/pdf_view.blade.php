<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Evaluasi Level 1 - {{ $pelatihan->kode_pelatihan }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            margin: 0;
            padding: 0;
            line-height: 1.2;
        }

        .form-container {
            max-width: 99%;
            margin: 3px auto;
            border: 1px solid #000;
            padding: 5px;
        }

        .form-table {
            width: 98%;
            border-collapse: collapse;
            margin: 3px auto;
            border: none;
        }

        .header {
            padding: 5px;
            border-bottom: 1px solid #000;
            background-color: #f0f0f0;
            margin: -3px -3px 0 -3px;
        }

        .logo {
            max-width: 120px;
            height: auto px;
            margin-right: 10px;
            object-fit: contain;
        }

        .title-section {
            text-align: center;
            padding: 4px;
            border-bottom: 1px solid #000;
            font-weight: bold;
            font-size: 10px;
            margin: 0 -3px;
        }

        .form-table td {
            border: 1px solid #000;
            padding: 3px;
            vertical-align: top;
            font-size: 8px;
            line-height: 1.2;
        }

        .field-label {
            width: 25%;
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .field-value {
            width: 75%;
            font-size: 8px;
        }

        .section-12-header {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: left;
            padding: 4px;
            font-size: 8px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        .eval-section-title {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
            padding: 2px;
            border-bottom: 1px solid #000;
            font-size: 8px;
        }

        .eval-mini-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }

        .eval-mini-table th,
        .eval-mini-table td {
            border: 1px solid #000;
            padding: 2px;
            text-align: center;
            font-size: 8px;
            vertical-align: middle;
        }

        .eval-mini-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            border-bottom: 2px solid #000;
        }

        .eval-mini-table .item-desc {
            text-align: left;
            padding-left: 3px;
        }

        /* Tambahan untuk D. Kemampuan Instruktur */
        .instructor-title {
            text-align: center;
            padding: 4px;
            background-color: #e0e0e0;
            border: 1px solid #000;
            font-size: 8px;
            font-weight: bold;
        }

        .instructor-wrapper {
            border: 1px solid #000;
            padding: 0;
        }

        .instructor-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin: 0;
            font-size: 8px;
        }

        .instructor-table th,
        .instructor-table td {
            border: 1px solid #000;
            padding: 2px;
            text-align: center;
            vertical-align: middle;
        }

        .instructor-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .complaints-section {
            min-height: 50px;
            padding: 3px;
            font-size: 8px;
            line-height: 1.2;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <!-- Header -->
        <div class="header">
            <table style="width: 100%; border: none; border-collapse: collapse;">
                <tr>
                    <td style="width: 70%; vertical-align: middle; border: none; padding: 0;">
                        <img src="logo.png" alt="Company Logo" class="logo">
                    </td>
                    <td style="width: 30%; vertical-align: middle; text-align: right; border: none; padding: 0;">
                        <img src="sics.png" alt="SICS Logo" style="height: 30px; margin-right: 5px; margin-top: 8px;">
                        <img src="kan.png" alt="KAN Logo" style="height: 30px; margin-top: 8px;">
                    </td>
                </tr>
            </table>
        </div>

        <!-- Title -->
        <div class="title-section">
            LEVEL 1 : EVALUASI PELATIHAN KARYAWAN (REAKSI)
        </div>

        <table class="form-table">
            <tr>
                <td class="field-label">1. No. Kode Pelatihan*)</td>
                <td class="field-value">{{ $pelatihan->kode_pelatihan }}</td>
            </tr>
            <tr>
                <td class="field-label">2. Nama Pelatihan</td>
                <td class="field-value">{{ $pelatihan->judul }}</td>
            </tr>
            <tr>
                <td class="field-label">3. Tanggal Pelaksanaan</td>
                <td class="field-value">
                    {{ $pelatihan->tanggal_mulai->format('d M Y') }}
                    – {{ $pelatihan->tanggal_selesai->format('d M Y') }}
                </td>
            </tr>
            <tr>
                <td class="field-label">4. Tempat Pelatihan</td>
                <td class="field-value">{{ $pelatihan->tempat }}</td>
            </tr>
            <tr>
                <td class="field-label">5. Penyelenggara</td>
                <td class="field-value">{{ $pelatihan->penyelenggara }}</td>
            </tr>
            <tr>
                <td class="field-label">6. Nama Peserta/Karyawan</td>
                <td class="field-value">{{ $evaluasi->user->name }}</td>
            </tr>
            <tr>
                <td class="field-label">7. N.I.K.</td>
                <td class="field-value">{{ $evaluasi->user->registration_id }}</td>
            </tr>
            <tr>
                <td class="field-label">8. Unit Kerja</td>
                <td class="field-value">{{ $evaluasi->user->department->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="field-label">9. Nama Atasan Saat Pengajuan</td>
                <td class="field-value">{{ $evaluasi->user->superior->name ?? '-' }}</td>
            </tr>

            <tr>
                <td colspan="2" class="field-label">10. Ringkasan Isi Materi Pelatihan yang Diberikan:</td>
            </tr>
            <tr>
                <td colspan="2" class="field-value" style="height: 40px;">
                    {{ $evaluasi->ringkasan_isi_materi }}
                </td>
            </tr>

            <tr>
                <td colspan="2" class="field-label">11. Hal-hal (ide/saran) yang dapat dikembangkan di PT KSP dari hasil Pelatihan ini :</td>
            </tr>
            <tr>
                <td colspan="2" class="field-value" style="height: 40px;">
                    {{ $evaluasi->ide_saran_pengembangan }}
                </td>
            </tr>

            <tr>
                <td colspan="2" class="section-12-header">
                    12. Berikan Penilaian Anda tentang Pelatihan ini :<br>
                    (1 = Sangat Kurang ; 2 = Kurang ; 3 = Cukup ; 4 = Baik ; 5 = Sangat Baik) pada kolom nilai sesuai dengan pilihan Anda.
                </td>
            </tr>

            <!-- A, B, C -->
            <tr>
                <td colspan="2" style="padding: 0; border: none;">
                    <table style="width: 100%; border-collapse: collapse; border: 1px solid #000;">
                        <tr>
                            <!-- Section A -->
                            <td style="width: 33.33%; vertical-align: top; border-right: 2px solid #000;">
                                <div class="eval-section-title">A. MATERI</div>
                                <table class="eval-mini-table">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>UNSUR YANG DINILAI</th>
                                            <th>NILAI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td>1</td><td class="item-desc">Sistematika penyajian</td><td>{{ $evaluasi->materi->materi_sistematika ?? '-' }}</td></tr>
                                        <tr><td>2</td><td class="item-desc">Jelas & mudah dipahami</td><td>{{ $evaluasi->materi->materi_pemahaman ?? '-' }}</td></tr>
                                        <tr><td>3</td><td class="item-desc">Menambah pengetahuan/wawasan</td><td>{{ $evaluasi->materi->materi_pengetahuan ?? '-' }}</td></tr>
                                        <tr><td>4</td><td class="item-desc">Manfaat dalam pekerjaan</td><td>{{ $evaluasi->materi->materi_manfaat ?? '-' }}</td></tr>
                                        <tr><td>5</td><td class="item-desc">Sesuai dengan tujuan pelatihan</td><td>{{ $evaluasi->materi->materi_tujuan ?? '-' }}</td></tr>
                                    </tbody>
                                </table>
                            </td>

                            <!-- Section B -->
                            <td style="width: 33.33%; vertical-align: top; border-right: 1px solid #000;">
                                <div class="eval-section-title">B. PENYELENGGARAAN</div>
                                <table class="eval-mini-table">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>UNSUR YANG DINILAI</th>
                                            <th>NILAI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td>1</td><td class="item-desc">Pengelolaan pelaksanaan</td><td>{{ $evaluasi->penyelenggaraan->penyelenggaraan_pengelolaan ?? '-' }}</td></tr>
                                        <tr><td>2</td><td class="item-desc">Keteraturan jadwal pelatihan</td><td>{{ $evaluasi->penyelenggaraan->penyelenggaraan_jadwal ?? '-' }}</td></tr>
                                        <tr><td>3</td><td class="item-desc">Persiapan pelaksanaan</td><td>{{ $evaluasi->penyelenggaraan->penyelenggaraan_persiapan ?? '-' }}</td></tr>
                                        <tr><td>4</td><td class="item-desc">Cepat & tanggap dalam pelayanan</td><td>{{ $evaluasi->penyelenggaraan->penyelenggaraan_pelayanan ?? '-' }}</td></tr>
                                        <tr><td>5</td><td class="item-desc">Koordinasi dengan instruktur</td><td>{{ $evaluasi->penyelenggaraan->penyelenggaraan_koordinasi ?? '-' }}</td></tr>
                                    </tbody>
                                </table>
                            </td>

                            <!-- Section C -->
                            <td style="width: 33.33%; vertical-align: top;">
                                <div class="eval-section-title">C. SARANA</div>
                                <table class="eval-mini-table">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>UNSUR YANG DINILAI</th>
                                            <th>NILAI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td>1</td><td class="item-desc">Media pelatihan (audio-visual)</td><td>{{ $evaluasi->sarana->sarana_media ?? '-' }}</td></tr>
                                        <tr><td>2</td><td class="item-desc">Training kit (tas, diktat, dll)</td><td>{{ $evaluasi->sarana->sarana_kit ?? '-' }}</td></tr>
                                        <tr><td>3</td><td class="item-desc">Kenyamanan & kerapihan ruang kelas</td><td>{{ $evaluasi->sarana->sarana_kenyamanan ?? '-' }}</td></tr>
                                        <tr><td>4</td><td class="item-desc">Kesesuaian sarana dengan pembelajaran</td><td>{{ $evaluasi->sarana->sarana_kesesuaian ?? '-' }}</td></tr>
                                        <tr><td>5</td><td class="item-desc">Lingkungan belajar</td><td>{{ $evaluasi->sarana->sarana_belajar ?? '-' }}</td></tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Section D -->
            <tr>
                <td colspan="2" class="instructor-title">D. KEMAMPUAN INSTRUKTUR</td>
            </tr>
            <tr>
                <td colspan="2" class="instructor-wrapper">
                    <table class="instructor-table">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>NAMA</th>
                                <th>Penguasaan Materi</th>
                                <th>Teknik Penyajian Materi</th>
                                <th>Sistematika Penyajian Materi</th>
                                <th>Pengaturan Waktu</th>
                                <th>Pengelolaan Proses Belajar Mengajar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($evaluasi->instrukturs as $i => $instruktur)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td style="text-align: left;">
                                        @if($instruktur->type === 'internal')
                                            {{ $instruktur->user->name }}
                                        @else
                                            {{ $instruktur->presenter->name }}
                                        @endif
                                    </td>
                                    <td>{{ $instruktur->instruktur_penguasaan ?? '-' }}</td>
                                    <td>{{ $instruktur->instruktur_teknik ?? '-' }}</td>
                                    <td>{{ $instruktur->instruktur_sistematika ?? '-' }}</td>
                                    <td>{{ $instruktur->instruktur_waktu ?? '-' }}</td>
                                    <td>{{ $instruktur->instruktur_proses ?? '-' }}</td>
                                </tr>
                            @endforeach
                            @if(count($evaluasi->instrukturs) < 4)
                                @for($i = count($evaluasi->instrukturs); $i < 4; $i++)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td></td><td></td><td></td><td></td><td></td><td></td>
                                    </tr>
                                @endfor
                            @endif
                        </tbody>
                    </table>
                </td>
            </tr>

            <!-- Complaints -->
            <tr>
                <td colspan="2" class="field-label">KOMPLAIN, SARAN DAN MASUKAN LAIN DARI ANDA TENTANG PELATIHAN INI :</td>
            </tr>
            <tr>
                <td colspan="2" class="complaints-section">{{ $evaluasi->komplain_saran_masukan }}</td>
            </tr>
            
            <!-- Signature section at the bottom of your PDF template -->
            <tr>
                <td colspan="2" style="border: none; padding-top: 15px;">
                    <table style="width: 100%; border: none;">
                        <tr>
                            <td style="width: 60%; vertical-align: top; border: none;"></td>
                            <td style="width: 40%; vertical-align: top; text-align: center; border: none;">
                                <div style="text-align: center; font-size: 8px;">
                                    {{ now()->format('d F Y') }}
                                </div>
                                
                                <div style="margin-top: 5px; text-align: center; font-size: 8px;">
                                    Peserta Pelatihan,
                                </div>
                                
                                <div style="height: 60px; line-height: 60px; text-align: center;">
                                    @if ($signature)
                                        <img src="{{ $signature }}" alt="Tanda Tangan" style="max-height: 50px; max-width: 100%;">
                                    @else
                                        <span style="font-style: italic; font-size: 8px;">(Menunggu Paraf)</span>
                                    @endif
                                </div>
                                
                                <div style="margin-top: 5px; text-align: center; font-size: 8px; font-weight: bold;">
                                    {{ strtoupper($evaluasi->user->name) }}
                                </div>
                                
                                <div style="text-align: center; font-size: 8px;">
                                    {{ $evaluasi->user->registration_id }}
                                </div>
                                
                                @php
                                    $participantSnapshot = $pelatihan->participants()
                                        ->where('user_id', $evaluasi->user_id)
                                        ->first();
                                    $jabatanFull = $participantSnapshot ? $participantSnapshot->jabatan_full : null;
                                @endphp
                                
                                @if($jabatanFull)
                                <div style="text-align: center; font-size: 8px; margin-top: 2px;">
                                    {{ $jabatanFull }}
                                </div>
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>