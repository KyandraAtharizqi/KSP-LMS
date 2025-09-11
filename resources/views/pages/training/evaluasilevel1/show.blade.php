@extends('layout.main')

@section('title', 'Evaluasi Pelatihan Level 1')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- HEADER --}}
    <div class="text-center mb-4">
        <h5 class="fw-bold">LEVEL 1 : EVALUASI PELATIHAN KARYAWAN (REAKSI)</h5>
    </div>

    {{-- Styles --}}
    <style>
        /* mini-tables for A/B/C */
        .eval-mini-table { 
            table-layout: fixed; 
            width: 100%; 
            border-collapse: collapse; 
        }
        .eval-mini-table th, .eval-mini-table td { 
            padding: .5rem .6rem; 
            vertical-align: middle; 
            border: 1px solid #dee2e6; 
        }

        /* No column (bigger now) */
        .eval-mini-table th:nth-child(1), 
        .eval-mini-table td:nth-child(1) {
            width: 12%; 
            text-align: center; 
            white-space: nowrap;
            font-weight: 600;
        }

        /* Unsur yang dinilai column */
        .eval-mini-table th:nth-child(2), 
        .eval-mini-table td:nth-child(2) {
            width: 65%; 
            text-align: left; 
            white-space: nowrap; 
            overflow: hidden; 
            text-overflow: ellipsis;
            font-size: .95rem;
        }

        /* Nilai column */
        .eval-mini-table th:nth-child(3), 
        .eval-mini-table td:nth-child(3) {
            width: 23%; 
            text-align: center; 
            white-space: nowrap;
        }

        /* Headers styled same as instructor table */
        .eval-mini-table thead th {
            background-color: #f8f9fa; /* Bootstrap table-light */
            font-weight: 700;
        }

        .section-12 { display: flex; gap: .5rem; align-items: flex-start; }
        .section-12 .num { flex: 0 0 40px; font-weight: 700; }
    </style>

    <table class="table table-bordered">
        <tbody>
            {{-- 1–11 --}}
            <tr>
                <td style="width: 40%">1. No. Kode Pelatihan</td>
                <td>{{ $pelatihan->kode_pelatihan }} ({{ $pelatihan->id }})</td>
            </tr>
            <tr>
                <td>2. Nama Pelatihan</td>
                <td>{{ $pelatihan->judul }}</td>
            </tr>
            <tr>
                <td>3. Tanggal Pelaksanaan</td>
                <td>
                    {{ $pelatihan->tanggal_mulai->format('d M Y') }}
                    – {{ $pelatihan->tanggal_selesai->format('d M Y') }}
                </td>
            </tr>
            <tr>
                <td>4. Tempat Pelatihan</td>
                <td>{{ $pelatihan->tempat }}</td>
            </tr>
            <tr>
                <td>5. Penyelenggara</td>
                <td>{{ $pelatihan->penyelenggara }}</td>
            </tr>
            <tr>
                <td>6. Nama Peserta / Karyawan</td>
                <td>{{ $evaluasi->user->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>7. NIK / Registration ID</td>
                <td>{{ $evaluasi->user->registration_id ?? '-' }}</td>
            </tr>
            <tr>
                <td>8. Jabatan</td>
                <td>{{ $evaluasi->user->jabatan_full ?? '-' }}</td>
            </tr>
            <tr>
                <td>9. Nama Atasan (Saat Pengajuan)</td>
                <td>{{ $evaluasi->superior?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>10. Ringkasan Isi Materi Pelatihan yang Diberikan</td>
                <td>{{ $evaluasi->ringkasan_isi_materi ?? '-' }}</td>
            </tr>
            <tr>
                <td>11. Hal-hal (ide/saran) yang dapat dikembangkan di PT KSP dari Pelatihan ini</td>
                <td>{{ $evaluasi->ide_saran_pengembangan ?? '-' }}</td>
            </tr>

            {{-- 12 Header --}}
            <tr>
                <td colspan="2">
                    <div class="section-12">
                        <div class="num">12.</div>
                        <div>
                            <div>Berikan Penilaian Anda tentang Pelatihan ini</div>
                            <div class="small">(1 = Sangat Kurang; 2 = Kurang; 3 = Cukup; 4 = Baik; 5 = Sangat Baik)</div>
                        </div>
                    </div>
                </td>
            </tr>

            {{-- 12 Evaluation Content --}}
            <tr>
                <td colspan="2" class="p-0">
                    <div class="p-3">
                        <table class="table table-borderless mb-0" style="width:100%;">
                            <tr>
                                {{-- A. Materi --}}
                                <td style="vertical-align: top; width: 33%;">
                                    <h6 class="fw-bold">A. Materi</h6>
                                    <table class="eval-mini-table">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Unsur yang dinilai</th>
                                                <th>Nilai</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr><td>1</td><td>Sistematika penyajian materi</td><td>{{ $evaluasi->materi->materi_sistematika ?? '-' }}</td></tr>
                                            <tr><td>2</td><td>Jelas & mudah dipahami</td><td>{{ $evaluasi->materi->materi_pemahaman ?? '-' }}</td></tr>
                                            <tr><td>3</td><td>Menambah pengetahuan/wawasan</td><td>{{ $evaluasi->materi->materi_pengetahuan ?? '-' }}</td></tr>
                                            <tr><td>4</td><td>Manfaat dalam pekerjaan</td><td>{{ $evaluasi->materi->materi_manfaat ?? '-' }}</td></tr>
                                            <tr><td>5</td><td>Sesuai dengan tujuan pelatihan</td><td>{{ $evaluasi->materi->materi_tujuan ?? '-' }}</td></tr>
                                        </tbody>
                                    </table>
                                </td>

                                {{-- B. Penyelenggaraan --}}
                                <td style="vertical-align: top; width: 33%;">
                                    <h6 class="fw-bold">B. Penyelenggaraan</h6>
                                    <table class="eval-mini-table">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Unsur yang dinilai</th>
                                                <th>Nilai</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr><td>1</td><td>Pengelolaan pelaksanaan</td><td>{{ $evaluasi->penyelenggaraan->penyelenggaraan_pengelolaan ?? '-' }}</td></tr>
                                            <tr><td>2</td><td>Keteraturan jadwal pelatihan</td><td>{{ $evaluasi->penyelenggaraan->penyelenggaraan_jadwal ?? '-' }}</td></tr>
                                            <tr><td>3</td><td>Persiapan pelaksanaan</td><td>{{ $evaluasi->penyelenggaraan->penyelenggaraan_persiapan ?? '-' }}</td></tr>
                                            <tr><td>4</td><td>Cepat & tanggap dalam pelayanan</td><td>{{ $evaluasi->penyelenggaraan->penyelenggaraan_pelayanan ?? '-' }}</td></tr>
                                            <tr><td>5</td><td>Koordinasi dengan instruktur</td><td>{{ $evaluasi->penyelenggaraan->penyelenggaraan_koordinasi ?? '-' }}</td></tr>
                                        </tbody>
                                    </table>
                                </td>

                                {{-- C. Sarana --}}
                                <td style="vertical-align: top; width: 33%;">
                                    <h6 class="fw-bold">C. Sarana</h6>
                                    <table class="eval-mini-table">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Unsur yang dinilai</th>
                                                <th>Nilai</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr><td>1</td><td>Media pelatihan (audio-visual)</td><td>{{ $evaluasi->sarana->sarana_media ?? '-' }}</td></tr>
                                            <tr><td>2</td><td>Training kit (tas, diktat, dll)</td><td>{{ $evaluasi->sarana->sarana_kit ?? '-' }}</td></tr>
                                            <tr><td>3</td><td>Kenyamanan & kerapihan ruang kelas</td><td>{{ $evaluasi->sarana->sarana_kenyamanan ?? '-' }}</td></tr>
                                            <tr><td>4</td><td>Kesesuaian sarana dengan belajar-mengajar</td><td>{{ $evaluasi->sarana->sarana_kesesuaian ?? '-' }}</td></tr>
                                            <tr><td>5</td><td>Lingkungan belajar</td><td>{{ $evaluasi->sarana->sarana_belajar ?? '-' }}</td></tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>

            {{-- D. INSTRUKTUR --}}
            <tr>
                <td colspan="2" class="fw-bold">D. Kemampuan Instruktur</td>
            </tr>
            <tr>
                <td colspan="2" class="p-0">
                    <table class="table table-bordered text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:7%">No</th>
                                <th>Nama</th>
                                <th>Penguasaan Materi</th>
                                <th>Teknik Penyajian</th>
                                <th>Sistematika</th>
                                <th>Pengaturan Waktu</th>
                                <th>Pengelolaan Proses</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($evaluasi->instrukturs as $i => $instruktur)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $instruktur->type === 'internal' ? $instruktur->user->name : $instruktur->presenter->name }}</td>
                                    <td>{{ $instruktur->instruktur_penguasaan ?? '-' }}</td>
                                    <td>{{ $instruktur->instruktur_teknik ?? '-' }}</td>
                                    <td>{{ $instruktur->instruktur_sistematika ?? '-' }}</td>
                                    <td>{{ $instruktur->instruktur_waktu ?? '-' }}</td>
                                    <td>{{ $instruktur->instruktur_proses ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>

            {{-- KOMPLAIN --}}
            <tr>
                <td colspan="2" class="fw-bold">Komplain, Saran, dan Masukan Lain</td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="p-2" style="min-height:100px;">
                        {{ $evaluasi->komplain_saran_masukan ?? '-' }}
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
