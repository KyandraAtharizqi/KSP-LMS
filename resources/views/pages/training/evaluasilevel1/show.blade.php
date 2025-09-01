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
                <dd class="col-sm-9">{{ $evaluasi->user->name ?? '-' }}</dd>

                <dt class="col-sm-3">Registration ID</dt>
                <dd class="col-sm-9">{{ $evaluasi->user->registration_id ?? '-' }}</dd>

                <dt class="col-sm-3">Jabatan</dt>
                <dd class="col-sm-9">{{ $evaluasi->user->jabatan_full?? '-' }}</dd>

                <dt class="col-sm-3">Department</dt>
                <dd class="col-sm-9">{{ $evaluasi->user->department->name ?? '-' }}</dd>
            </dl>

            <hr>

            <dl class="row">
                <dt class="col-sm-3">Ringkasan Materi Pelatihan</dt>
                <dd class="col-sm-9">{{ $evaluasi->ringkasan_isi_materi }}</dd>

                <dt class="col-sm-3">Ide/Saran</dt>
                <dd class="col-sm-9">{{ $evaluasi->ide_saran_pengembangan }}</dd>
            </dl>

            {{-- Materi --}}
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
                            $materiValues = [
                                $evaluasi->materi->materi_sistematika ?? '-',
                                $evaluasi->materi->materi_pemahaman ?? '-',
                                $evaluasi->materi->materi_pengetahuan ?? '-',
                                $evaluasi->materi->materi_manfaat ?? '-',
                                $evaluasi->materi->materi_tujuan ?? '-'
                            ];
                        @endphp
                        @foreach ($materiItems as $i => $item)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>{{ $item }}</td>
                                <td class="text-center">{{ $materiValues[$i] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Penyelenggaraan --}}
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
                            $penyelenggaraanValues = [
                                $evaluasi->penyelenggaraan->penyelenggaraan_pengelolaan ?? '-',
                                $evaluasi->penyelenggaraan->penyelenggaraan_jadwal ?? '-',
                                $evaluasi->penyelenggaraan->penyelenggaraan_persiapan ?? '-',
                                $evaluasi->penyelenggaraan->penyelenggaraan_pelayanan ?? '-',
                                $evaluasi->penyelenggaraan->penyelenggaraan_koordinasi ?? '-'
                            ];
                        @endphp
                        @foreach ($penyelenggaraanItems as $i => $item)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>{{ $item }}</td>
                                <td class="text-center">{{ $penyelenggaraanValues[$i] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Sarana --}}
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
                                'Kesesuaian sarana dengan proses belajar-mengajar',
                                'Lingkungan belajar'
                            ];
                            $saranaValues = [
                                $evaluasi->sarana->sarana_media ?? '-',
                                $evaluasi->sarana->sarana_kit ?? '-',
                                $evaluasi->sarana->sarana_kenyamanan ?? '-',
                                $evaluasi->sarana->sarana_kesesuaian ?? '-',
                                $evaluasi->sarana->sarana_belajar ?? '-'
                            ];
                        @endphp
                        @foreach ($saranaItems as $i => $item)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>{{ $item }}</td>
                                <td class="text-center">{{ $saranaValues[$i] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Instruktur --}}
            <div class="mb-4">
                <h5 class="mb-3">D. Kemampuan Instruktur</h5>
                <table class="table table-bordered table-striped align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
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
                                <td>
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
                    </tbody>
                </table>
            </div>

            <dl class="row">
                <dt class="col-sm-3">KOMPLAIN, SARAN, DAN MASUKAN LAIN</dt>
                <dd class="col-sm-9">{{ $evaluasi->komplain_saran_masukan }}</dd>
            </dl>
        </div>
    </div>
</div>
@endsection
