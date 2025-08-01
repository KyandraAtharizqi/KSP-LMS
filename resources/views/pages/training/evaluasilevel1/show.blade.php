@extends('layout.main')

@section('title', 'Detail Evaluasi Pelatihan')

@section('content')
<div class="page-heading">
    <h3>Detail Evaluasi - {{ $pelatihan->judul }}</h3>
</div>

<div class="page-content">
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0">LEVEL 1: EVALUASI PELATIHAN KARYAWAN (REAKSI)</h5>
        </div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Kode Pelatihan</dt>
                <dd class="col-sm-9">{{ $pelatihan->kode_pelatihan }}</dd>

                <dt class="col-sm-3">Nama Pelatihan</dt>
                <dd class="col-sm-9">{{ $pelatihan->judul }}</dd>

                <dt class="col-sm-3">Tanggal Pelaksanaan</dt>
                <dd class="col-sm-9">
                    {{ $pelatihan->tanggal_mulai->format('d M Y') }} 
                    - 
                    {{ $pelatihan->tanggal_selesai->format('d M Y') }}
                </dd>

                <dt class="col-sm-3">Tempat</dt>
                <dd class="col-sm-9">{{ $pelatihan->tempat }}</dd>

                <dt class="col-sm-3">Penyelenggara</dt>
                <dd class="col-sm-9">{{ $pelatihan->penyelenggara }}</dd>

                <dt class="col-sm-3">Nama Peserta/Karyawan</dt>
                <dd class="col-sm-9">{{ Auth::user()->name }}</dd>

                <dt class="col-sm-3">NIK</dt>
                <dd class="col-sm-9">{{ Auth::user()->nik }}</dd>

                <dt class="col-sm-3">Unit Kerja</dt>
                <dd class="col-sm-9">{{ Auth::user()->jabatan->name ?? '-' }}</dd>

                <dt class="col-sm-3">Nama Atasan Langsung</dt>
                <dd class="col-sm-9">{{ Auth::user()->directorate->name }}</dd>

                <dt class="col-sm-3">Ringkasan Materi Pelatihan</dt>
                <dd class="col-sm-9">{{ $evaluasi->ringkasan }}</dd>

                <dt class="col-sm-3">Ide/Saran</dt>
                <dd class="col-sm-9">{{ $evaluasi->ide_saran }}</dd>

                <div class="mb-4">
                    <h5 class="mb-3">A. Materi</h5>
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%">No</th>
                                <th>Unsur yang Dinilai</th>
                                <th class="text-center align-middle" style="width: 15%">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>

                            @php
                                $materiItems = [
                                    'Sistematika penyajian materi',
                                    'Jelas dan mudah dipahami',
                                    'Menambah pengetahuan/wawasan',
                                    'Manfaat dalam pekerjaan',
                                    'Sesuai dengan tujuan pelatihan'
                                ];
                            @endphp
                        
                            @foreach (json_decode($evaluasi->materi, true) as $i => $val)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td>{{ $materiItems[$i] ?? 'Unsur tidak ditemukan' }}</td>
                                    <td class="text-center">{{ $val }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mb-4">
                    <h5 class="mb-3">B. Penyelenggaraan</h5>
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%">No</th>
                                <th>Unsur yang Dinilai</th>
                                <th class="text-center align-middle" style="width: 15%">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $penyelenggaraanItems = [
                                    'Pengelolaan pelaksanaan',
                                    'Keteraturan jadwal pelatihan',
                                    'Persiapan pelaksanaan',
                                    'Cepat & tanggap dalam pelayanan',
                                    'Koordinasi dengan instruktur'
                                ];
                            @endphp
                            @foreach (json_decode($evaluasi->penyelenggaraan, true) as $i => $val)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td>{{ $penyelenggaraanItems[$i] ?? 'Unsur tidak ditemukan' }}</td>
                                    <td class="text-center">{{ $val }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mb-4">
                    <h5 class="mb-3">C. Sarana</h5>
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%">No</th>
                                <th>Unsur yang Dinilai</th>
                                <th class="text-center align-middle" style="width: 15%">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $saranaItems = [
                                    'Media pelatihan (audio-visual)',
                                    'Training kit (tas, diktat, dll)',
                                    'Kenyamanan & kerapihan ruang kelas',
                                    'Kesesuaian sarana dengan proses belajar-mengajar'
                                ];
                            @endphp
                            @foreach (json_decode($evaluasi->sarana, true) as $i => $val)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td>{{ $saranaItems[$i] ?? 'Unsur tidak ditemukan' }}</td>
                                    <td class="text-center">{{ $val }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mb-4">
                    <h5 class="mb-3">D. Kemampuan Instruktur</h5>
                    <table class="table table-bordered table-striped align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center align-middle">No</th>
                                <th class="text-center align-middle">Nama</th>
                                <th class="text-center align-middle">Penguasaan Materi</th>
                                <th class="text-center align-middle">Teknik Penyajian Materi</th>
                                <th class="text-center align-middle">Sistematika Pengajian Materi</th>
                                <th class="text-center align-middle">Pengaturan Waktu Pengajian Materi</th>
                                <th class="text-center align-middle">Pengelolaan Proses Belajar Mengajar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (json_decode($evaluasi->instruktur, true) as $i => $ins)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $ins['nama'] }}</td>
                                    <td>{{ $ins['penguasaan'] ?? '-' }}</td>
                                    <td>{{ $ins['teknik'] ?? '-' }}</td>
                                    <td>{{ $ins['sistematika'] ?? '-' }}</td>
                                    <td>{{ $ins['waktu'] ?? '-' }}</td>
                                    <td>{{ $ins['proses'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <dt class="col-sm-3">KOMPLAIN, SARAN, DAN MASUKAN LAIN DARI ANDA TENTANG PELATIHAN INI</dt>
                <dd class="col-sm-9">{{ $evaluasi->komentar }}</dd>
            </dl>
        </div>
    </div>
</div>
@endsection
