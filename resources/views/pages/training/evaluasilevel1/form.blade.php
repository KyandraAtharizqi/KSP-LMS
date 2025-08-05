@extends('layout.main')

@section('title', 'Isi Evaluasi Pelatihan Level 1')

@section('content')

<div class="page-heading">
    <h3>Evaluasi Pelatihan Level 1 - {{ $pelatihan->judul }}</h3>
</div>

<div class="page-content">
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
                    {{ $pelatihan->tanggal_mulai->format('d M Y') }} - {{ $pelatihan->tanggal_selesai->format('d M Y') }}
                </dd>

                <dt class="col-sm-3">Tempat</dt>
                <dd class="col-sm-9">{{ $pelatihan->tempat }}</dd>

                <dt class="col-sm-3">Penyelenggara</dt>
                <dd class="col-sm-9">{{ $pelatihan->penyelenggara }}</dd>

                <dt class="col-sm-3">Nama Peserta</dt>
                <dd class="col-sm-9">{{ Auth::user()->name }}</dd>

                <dt class="col-sm-3">NIK</dt>
                <dd class="col-sm-9">{{ Auth::user()->nik }}</dd>

                <dt class="col-sm-3">Unit Kerja</dt>
                <dd class="col-sm-9">{{ Auth::user()->jabatan->name ?? '-' }}</dd>

                <dt class="col-sm-3">Nama Atasan Langsung</dt>
                <dd class="col-sm-9">{{ Auth::user()->superior->name ?? '-' }}</dd>
            </dl>
        </div>
    </div>

    <form action="{{ route('training.evaluasilevel1.store', $pelatihan->id) }}" method="POST">
        @csrf

        {{-- Hidden fields --}}
        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
        <input type="hidden" name="pelatihan_id" value="{{ $pelatihan->id }}">
        <input type="hidden" name="registration_id" value="{{ $registration_id }}">
        <input type="hidden" name="kode_pelatihan" value="{{ $pelatihan->kode_pelatihan }}">
        <input type="hidden" name="nama_pelatihan" value="{{ $pelatihan->judul }}">
        <input type="hidden" name="tanggal_pelaksanaan" value="{{ $pelatihan->tanggal_mulai->format('Y-m-d') }}">
        <input type="hidden" name="tempat" value="{{ $pelatihan->tempat }}">
        <input type="hidden" name="name" value="{{ Auth::user()->name }}">
        <input type="hidden" name="department" value="{{ Auth::user()->department->name ?? '-' }}">
        <input type="hidden" name="jabatan_full" value="{{ Auth::user()->jabatan->name ?? '-' }}">
        <input type="hidden" name="superior_id" value="{{ Auth::user()->superior_id }}">

        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">Form Evaluasi</h5></div>
            <div class="card-body">
                <dl class="row mb-4">
                    <dt class="col-sm-3">Ringkasan Isi Materi</dt>
                    <dd class="col-sm-9"><textarea name="ringkasan_isi_materi" class="form-control" rows="3" required></textarea></dd>

                    <dt class="col-sm-3">Ide/Saran untuk Pengembangan</dt>
                    <dd class="col-sm-9"><textarea name="ide_saran_pengembangan" class="form-control" rows="3" required></textarea></dd>
                </dl>

                <hr class="my-4">

                {{-- A. Materi --}}
                <div class="mb-4">
                    <h5>A. Materi</h5>
                    <table class="table table-bordered">
                        <thead><tr><th>No</th><th>Unsur</th><th>Nilai (1-5)</th></tr></thead>
                        <tbody>
                            @php
                                $materiFields = ['materi_sistematika', 'materi_pemahaman', 'materi_pengetahuan', 'materi_manfaat', 'materi_tujuan'];
                                $materiLabels = ['Sistematika penyajian materi', 'Jelas dan mudah dipahami', 'Menambah pengetahuan', 'Manfaat dalam pekerjaan', 'Sesuai tujuan pelatihan'];
                            @endphp
                            @foreach($materiLabels as $i => $label)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td>{{ $label }}</td>
                                    <td><input type="number" class="form-control" name="{{ $materiFields[$i] }}" min="1" max="5" required></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- B. Penyelenggaraan --}}
                <div class="mb-4">
                    <h5>B. Penyelenggaraan</h5>
                    <table class="table table-bordered">
                        <thead><tr><th>No</th><th>Unsur</th><th>Nilai (1-5)</th></tr></thead>
                        <tbody>
                            @php
                                $penyelenggaraanFields = ['pengelolaan', 'jadwal', 'persiapan', 'pelayanan', 'koordinasi'];
                                $penyelenggaraanLabels = ['Pengelolaan pelaksanaan', 'Keteraturan jadwal', 'Persiapan pelaksanaan', 'Pelayanan peserta', 'Koordinasi dengan instruktur'];
                            @endphp
                            @foreach($penyelenggaraanLabels as $i => $label)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td>{{ $label }}</td>
                                    <td><input type="number" class="form-control" name="penyelenggaraan_{{ $penyelenggaraanFields[$i] }}" min="1" max="5" required></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- C. Sarana --}}
                <div class="mb-4">
                    <h5>C. Sarana</h5>
                    <table class="table table-bordered">
                        <thead><tr><th>No</th><th>Unsur</th><th>Nilai (1-5)</th></tr></thead>
                        <tbody>
                            @php
                                $saranaFields = ['media', 'kit', 'kenyamanan', 'kesesuaian', 'belajar'];
                                $saranaLabels = ['Media (audio-visual)', 'Training kit', 'Kenyamanan ruang', 'Kesesuaian sarana', 'Lingkungan belajar'];
                            @endphp
                            @foreach($saranaLabels as $i => $label)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td>{{ $label }}</td>
                                    <td><input type="number" class="form-control" name="sarana_{{ $saranaFields[$i] }}" min="1" max="5" required></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- D. Instruktur --}}
                <div class="mb-4">
                    <h5>D. Kemampuan Instruktur</h5>
                    <table class="table table-bordered table-striped">
                        <thead><tr>
                            <th>No</th><th>Nama</th><th>Penguasaan</th><th>Teknik</th><th>Sistematika</th><th>Waktu</th><th>Proses</th>
                        </tr></thead>
                        <tbody>
                            @foreach ($presenters as $i => $presenter)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        <input type="hidden" name="instrukturs[{{ $i }}][type]" value="external">
                                        <input type="hidden" name="instrukturs[{{ $i }}][presenter_id]" value="{{ $presenter->id }}">
                                        <input type="text" value="{{ $presenter->name }}" class="form-control" readonly>
                                    </td>
                                    <td><input type="number" name="instrukturs[{{ $i }}][instruktur_penguasaan]" class="form-control" min="1" max="5" required></td>
                                    <td><input type="number" name="instrukturs[{{ $i }}][instruktur_teknik]" class="form-control" min="1" max="5" required></td>
                                    <td><input type="number" name="instrukturs[{{ $i }}][instruktur_sistematika]" class="form-control" min="1" max="5" required></td>
                                    <td><input type="number" name="instrukturs[{{ $i }}][instruktur_waktu]" class="form-control" min="1" max="5" required></td>
                                    <td><input type="number" name="instrukturs[{{ $i }}][instruktur_proses]" class="form-control" min="1" max="5" required></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <dl class="row mb-0">
                    <dt class="col-sm-3">Komplain / Masukan Lain</dt>
                    <dd class="col-sm-9">
                        <textarea name="komplain_saran_masukan" rows="3" class="form-control" required></textarea>
                    </dd>
                </dl>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">Kirim Evaluasi</button>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection
