@extends('layout.main')

@section('title', 'Isi Evaluasi Pelatihan Level 1')

@section('content')

<div class="page-heading">
    <h3>Evaluasi Pelatihan Level 1 - {{ $pelatihan->judul }}</h3>
</div>

<div class="page-content">
    <!-- Pelatihan Info -->
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0">Informasi Pelatihan</h5>
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

            </dl>
        </div>
    </div>

    <form action="{{ route('training.evaluation1.store', $pelatihan->id) }}" method="POST">
    @csrf

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Form Evaluasi</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-4">
                    <dt class="col-sm-3">Ringkasan Isi Materi Pelatihan yang Diberikan</dt>
                    <dd class="col-sm-9">
                        <textarea name="ringkasan" id="ringkasan" rows="3" class="form-control" required></textarea>
                    </dd>

                    <dt class="col-sm-3">Ide/Saran untuk Dikembangkan di PT KSP</dt>
                    <dd class="col-sm-9">
                        <textarea name="ide_saran" id="ide_saran" rows="3" class="form-control" required></textarea>
                    </dd>

                    <hr class="my-6">

                    <h5 class="text-lg font-semibold mb-4">Berikan Penilaian Anda tentang Pelatihan ini</h5>
                    <p class="text-sm mb-3">1 (Sangat Kurang), 2 (Kurang), 3 (Cukup), 4 (Baik), 5 (Sangat Baik) pada kolom nilai sesuai dengan pilihan Anda</p>

                    <!-- A. Materi -->
                    <div class="mb-4">
                        <h5 class="mb-3">A. Materi</h5>
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th>Unsur yang Dinilai</th>
                                    <th style="width: 15%">Nilai (1-5)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ([
                                    'Sistematika penyajian materi',
                                    'Jelas dan mudah dipahami',
                                    'Menambah pengetahuan/wawasan',
                                    'Manfaat dalam pekerjaan',
                                    'Sesuai dengan tujuan pelatihan'
                                ] as $i => $item)
                                    <tr>
                                        <td class="text-center">{{ $i + 1 }}</td>
                                        <td>{{ $item }}</td>
                                        <td><input type="number" name="materi[{{ $i }}]" min="1" max="5" class="form-control" required></td>
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
                                    <th style="width: 15%">Nilai (1-5)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ([
                                    'Pengelolaan pelaksanaan',
                                    'Keteraturan jadwal pelatihan',
                                    'Persiapan pelaksanaan',
                                    'Cepat & tanggap dalam pelayanan',
                                    'Koordinasi dengan instruktur'
                                ] as $i => $item)
                                    <tr>
                                        <td class="text-center">{{ $i + 1 }}</td>
                                        <td>{{ $item }}</td>
                                        <td><input type="number" name="penyelenggaraan[{{ $i }}]" min="1" max="5" class="form-control" required></td>
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
                                    <th style="width: 15%">Nilai (1-5)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ([
                                    'Media pelatihan (audio-visual)',
                                    'Training kit (tas, diktat, dll)',
                                    'Kenyamanan & kerapihan ruang kelas',
                                    'Kesesuaian sarana dengan proses belajar-mengajar'
                                ] as $i => $item)
                                    <tr>
                                        <td class="text-center">{{ $i + 1 }}</td>
                                        <td>{{ $item }}</td>
                                        <td><input type="number" name="sarana[{{ $i }}]" min="1" max="5" class="form-control" required></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- D. Kemampuan Instruktur -->
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
                                @foreach ($presenters as $i => $presenter)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>
                                            <input type="text" name="instruktur[{{ $i }}][nama]" value="{{ $presenter }}" class="form-control" readonly>
                                        </td>
                                        <td>
                                            <input type="number" name="instruktur[{{ $i }}][penguasaan]" min="1" max="5" class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="number" name="instruktur[{{ $i }}][teknik]" min="1" max="5" class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="number" name="instruktur[{{ $i }}][sistematika]" min="1" max="5" class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="number" name="instruktur[{{ $i }}][waktu]" min="1" max="5" class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="number" name="instruktur[{{ $i }}][proses]" min="1" max="5" class="form-control" required>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Komplain/Saran -->
                    <dt class="col-sm-3">KOMPLAIN, SARAN, DAN MASUKAN LAIN DARI ANDA TENTANG PELATIHAN INI :</dt>
                    <dd class="col-sm-9">
                        <textarea name="komentar" id="komentar" rows="3" class="form-control" required></textarea>
                    </dd>

                </dl>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        Kirim Evaluasi
                    </button>
                </div>
            </div>
        </div>
    </form>

</div>
@endsection
