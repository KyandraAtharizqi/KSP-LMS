<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Evaluasi Level 1 - {{ $pelatihan->kode_pelatihan }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; line-height: 1.4; }
        h3, h4, h5 { margin: 0; padding: 0; }
        .text-center { text-align: center; }
        .mb-2 { margin-bottom: 10px; }
        .mb-3 { margin-bottom: 15px; }
        .mb-4 { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; vertical-align: top; }
        th { background-color: #f0f0f0; }
        th.text-center, td.text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="text-center mb-4">
        <h3>Evaluasi Level 1 - Peserta</h3>
        <p>{{ $pelatihan->judul }} ({{ $pelatihan->kode_pelatihan }})</p>
    </div>

    <table>
        <tr>
            <th>Kode Pelatihan</th>
            <td>{{ $pelatihan->kode_pelatihan }}</td>
        </tr>
        <tr>
            <th>Nama Pelatihan</th>
            <td>{{ $pelatihan->judul }}</td>
        </tr>
        <tr>
            <th>Tanggal Pelaksanaan</th>
            <td>{{ $pelatihan->tanggal_mulai->format('d M Y') }} - {{ $pelatihan->tanggal_selesai->format('d M Y') }}</td>
        </tr>
        <tr>
            <th>Tempat</th>
            <td>{{ $pelatihan->tempat }}</td>
        </tr>
        <tr>
            <th>Penyelenggara</th>
            <td>{{ $pelatihan->penyelenggara }}</td>
        </tr>
        <tr>
            <th>Nama Peserta</th>
            <td>{{ $evaluasi->user->name }}</td>
        </tr>

        <tr>
            <th>Registration ID</th>
            <td>{{ $evaluasi->user->registration_id }}</td>
        </tr>

        <tr>
            <th>Registration ID</th>
            <td>{{ $evaluasi->user->registration_id }}</td>
        </tr>

        <tr>
            <th>Department</th>
            <td>{{ $evaluasi->user->department->name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Nama Atasan Langsung</th>
            <td>{{ $evaluasi->user->superior->name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Ringkasan Materi Pelatihan</th>
            <td>{{ $evaluasi->ringkasan_isi_materi }}</td>
        </tr>
        <tr>
            <th>Ide/Saran</th>
            <td>{{ $evaluasi->ide_saran_pengembangan }}</td>
        </tr>
    </table>

    @php
        $sections = [
            'Materi' => ['materi' => [
                'Sistematika penyajian materi' => $evaluasi->materi->materi_sistematika ?? '-',
                'Jelas dan mudah dipahami' => $evaluasi->materi->materi_pemahaman ?? '-',
                'Menambah pengetahuan/wawasan' => $evaluasi->materi->materi_pengetahuan ?? '-',
                'Manfaat dalam pekerjaan' => $evaluasi->materi->materi_manfaat ?? '-',
                'Sesuai dengan tujuan pelatihan' => $evaluasi->materi->materi_tujuan ?? '-'
            ]],
            'Penyelenggaraan' => ['penyelenggaraan' => [
                'Pengelolaan pelaksanaan' => $evaluasi->penyelenggaraan->penyelenggaraan_pengelolaan ?? '-',
                'Keteraturan jadwal pelatihan' => $evaluasi->penyelenggaraan->penyelenggaraan_jadwal ?? '-',
                'Persiapan pelaksanaan' => $evaluasi->penyelenggaraan->penyelenggaraan_persiapan ?? '-',
                'Cepat & tanggap dalam pelayanan' => $evaluasi->penyelenggaraan->penyelenggaraan_pelayanan ?? '-',
                'Koordinasi dengan instruktur' => $evaluasi->penyelenggaraan->penyelenggaraan_koordinasi ?? '-'
            ]],
            'Sarana' => ['sarana' => [
                'Media pelatihan (audio-visual)' => $evaluasi->sarana->sarana_media ?? '-',
                'Training kit (tas, diktat, dll)' => $evaluasi->sarana->sarana_kit ?? '-',
                'Kenyamanan & kerapihan ruang kelas' => $evaluasi->sarana->sarana_kenyamanan ?? '-',
                'Kesesuaian sarana dengan proses belajar-mengajar' => $evaluasi->sarana->sarana_kesesuaian ?? '-',
                'Lingkungan belajar' => $evaluasi->sarana->sarana_belajar ?? '-'
            ]],
        ];
    @endphp

    @foreach ($sections as $title => $data)
        <div class="mb-4">
            <h5>{{ $title }}</h5>
            <table>
                <thead>
                    <tr>
                        <th style="width:5%">No</th>
                        <th>Unsur yang Dinilai</th>
                        <th style="width:15%">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i=1; @endphp
                    @foreach ($data[array_key_first($data)] as $item => $value)
                        <tr>
                            <td class="text-center">{{ $i++ }}</td>
                            <td>{{ $item }}</td>
                            <td class="text-center">{{ $value }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    <div class="mb-4">
        <h5>Instruktur</h5>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Penguasaan Materi</th>
                    <th>Teknik Penyajian Materi</th>
                    <th>Sistematika Pengajian Materi</th>
                    <th>Pengaturan Waktu</th>
                    <th>Pengelolaan Proses</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($evaluasi->instrukturs as $i => $instruktur)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>
                            @if($instruktur->type === 'internal')
                                {{ $instruktur->user->name }}
                            @else
                                {{ $instruktur->presenter->name }}
                            @endif
                        </td>
                        <td class="text-center">{{ $instruktur->instruktur_penguasaan ?? '-' }}</td>
                        <td class="text-center">{{ $instruktur->instruktur_teknik ?? '-' }}</td>
                        <td class="text-center">{{ $instruktur->instruktur_sistematika ?? '-' }}</td>
                        <td class="text-center">{{ $instruktur->instruktur_waktu ?? '-' }}</td>
                        <td class="text-center">{{ $instruktur->instruktur_proses ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <table>
        <tr>
            <th>Komplain / Saran / Masukan</th>
            <td>{{ $evaluasi->komplain_saran_masukan }}</td>
        </tr>
    </table>

</body>
</html>
