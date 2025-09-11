@extends('layout.main')

@section('title', 'Isi Evaluasi Pelatihan Level 1')

@section('content')

<div class="page-heading">
    <h3>Evaluasi Pelatihan Level 1 - {{ $pelatihan->judul }}</h3>
</div>

<div class="page-content">

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <h5>Validation Errors:</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Success message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Informasi Pelatihan --}}
    <div class="card mb-3">
        <div class="card-header"><h5 class="mb-0">Informasi Pelatihan</h5></div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Kode Pelatihan</dt><dd class="col-sm-9">{{ $pelatihan->kode_pelatihan }}</dd>
                <dt class="col-sm-3">Nama Pelatihan</dt><dd class="col-sm-9">{{ $pelatihan->judul }}</dd>
                <dt class="col-sm-3">Tanggal Pelaksanaan</dt>
                <dd class="col-sm-9">{{ $pelatihan->tanggal_mulai->format('d M Y') }} - {{ $pelatihan->tanggal_selesai->format('d M Y') }}</dd>
                <dt class="col-sm-3">Tempat</dt><dd class="col-sm-9">{{ $pelatihan->tempat }}</dd>
                <dt class="col-sm-3">Penyelenggara</dt><dd class="col-sm-9">{{ $pelatihan->penyelenggara }}</dd>
                <dt class="col-sm-3">Nama Peserta</dt><dd class="col-sm-9">{{ $participant->user->name ?? '-' }}</dd>
                <dt class="col-sm-3">NIK</dt><dd class="col-sm-9">{{ $participant->user->nik ?? '-' }}</dd>
                <dt class="col-sm-3">Jabatan Full</dt><dd class="col-sm-9">{{ $participant->jabatan_full ?? ($participant->jabatan->name ?? '-') }}</dd>
                <dt class="col-sm-3">Department</dt><dd class="col-sm-9">{{ $participant->department->name ?? '-' }}</dd>
                <dt class="col-sm-3">Nama Atasan Langsung</dt><dd class="col-sm-9">{{ $participant->superior->name ?? '-' }}</dd>
            </dl>
        </div>
    </div>

    <form action="{{ route('training.evaluasilevel1.store', $pelatihan->id) }}" method="POST">
        @csrf

        {{-- Hidden fields --}}
        <input type="hidden" name="user_id" value="{{ $participant->user_id }}">
        <input type="hidden" name="pelatihan_id" value="{{ $pelatihan->id }}">
        <input type="hidden" name="registration_id" value="{{ $participant->registration_id }}">
        <input type="hidden" name="kode_pelatihan" value="{{ $pelatihan->kode_pelatihan }}">
        <input type="hidden" name="superior_id" value="{{ Auth::user()->superior_id ?? '' }}">

        {{-- Form Evaluasi --}}
        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">Form Evaluasi</h5></div>
            <div class="card-body">

                {{-- Ringkasan dan Ide --}}
                <div class="mb-4">
                    <label for="ringkasan_isi_materi" class="form-label">Ringkasan Isi Materi Pelatihan yang Diberikan</label>
                    <textarea name="ringkasan_isi_materi" class="form-control @error('ringkasan_isi_materi') is-invalid @enderror" rows="3" required>{{ old('ringkasan_isi_materi') }}</textarea>
                    @error('ringkasan_isi_materi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label for="ide_saran_pengembangan" class="form-label">Hal-hal (Ide/Saran) yang dapat dikembangkan di PT KSP dari hasil Pelatihan ini</label>
                    <textarea name="ide_saran_pengembangan" class="form-control @error('ide_saran_pengembangan') is-invalid @enderror" rows="3" required>{{ old('ide_saran_pengembangan') }}</textarea>
                    @error('ide_saran_pengembangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <hr>

                {{-- A. Materi --}}
                <h5>A. Materi</h5>
                <table class="table table-bordered">
                    <thead><tr><th>No</th><th>Unsur</th><th>Nilai (1-5)</th></tr></thead>
                    <tbody>
                        @php
                            $materiLabels = ['Sistematika penyajian materi','Jelas dan mudah dipahami','Menambah pengetahuan','Manfaat dalam pekerjaan','Sesuai tujuan pelatihan'];
                            $materiFields = ['materi_sistematika','materi_pemahaman','materi_pengetahuan','materi_manfaat','materi_tujuan'];
                        @endphp
                        @foreach($materiLabels as $i => $label)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>{{ $label }}</td>
                                <td>
                                    <input type="number" name="{{ $materiFields[$i] }}" min="1" max="5" class="form-control @error($materiFields[$i]) is-invalid @enderror" value="{{ old($materiFields[$i]) }}" required>
                                    @error($materiFields[$i])<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- B. Penyelenggaraan --}}
                <h5>B. Penyelenggaraan</h5>
                <table class="table table-bordered">
                    <thead><tr><th>No</th><th>Unsur</th><th>Nilai (1-5)</th></tr></thead>
                    <tbody>
                        @php
                            $penyelenggaraanLabels = ['Pengelolaan pelaksanaan','Keteraturan jadwal pelatihan','Persiapan pelaksanaan','Cepat & tanggap dalam pelayanan','Koordinasi dengan instruktur'];
                            $penyelenggaraanFields = ['pengelolaan','jadwal','persiapan','pelayanan','koordinasi'];
                        @endphp
                        @foreach($penyelenggaraanLabels as $i => $label)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>{{ $label }}</td>
                                <td>
                                    <input type="number" name="penyelenggaraan_{{ $penyelenggaraanFields[$i] }}" min="1" max="5" class="form-control @error('penyelenggaraan_'.$penyelenggaraanFields[$i]) is-invalid @enderror" value="{{ old('penyelenggaraan_'.$penyelenggaraanFields[$i]) }}" required>
                                    @error('penyelenggaraan_'.$penyelenggaraanFields[$i])<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- C. Sarana --}}
                <h5>C. Sarana</h5>
                <table class="table table-bordered">
                    <thead><tr><th>No</th><th>Unsur</th><th>Nilai (1-5)</th></tr></thead>
                    <tbody>
                        @php
                            $saranaLabels = ['Media pelatihan (audio-visual)','Training kit (tas, diktat, dll)','Kenyamanan & kerapihan ruang kelas','Kesesuaian sarana dengan proses','Belajar-mengajar'];
                            $saranaFields = ['media','kit','kenyamanan','kesesuaian','belajar'];
                        @endphp
                        @foreach($saranaLabels as $i => $label)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>{{ $label }}</td>
                                <td>
                                    <input type="number" name="sarana_{{ $saranaFields[$i] }}" min="1" max="5" class="form-control @error('sarana_'.$saranaFields[$i]) is-invalid @enderror" value="{{ old('sarana_'.$saranaFields[$i]) }}" required>
                                    @error('sarana_'.$saranaFields[$i])<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- D. Kemampuan Instruktur --}}
                <h5>D. Kemampuan Instruktur</h5>
                @php
                    $uniquePresenters = $presenters->unique(function($item) {
                        return $item->type . '_' . ($item->type === 'internal' ? $item->user_id : $item->presenter_id);
                    })->values();
                @endphp

                @if($uniquePresenters->count() > 0)
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th><th>Nama</th><th>Penguasaan</th><th>Teknik</th><th>Sistematika</th><th>Waktu</th><th>Proses</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($uniquePresenters as $i => $presenter)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        @if($presenter->type === 'internal')
                                            <input type="hidden" name="instrukturs[{{ $i }}][type]" value="internal">
                                            <input type="hidden" name="instrukturs[{{ $i }}][user_id]" value="{{ $presenter->user_id }}">
                                            {{ $presenter->user->name ?? '-' }}
                                        @else
                                            <input type="hidden" name="instrukturs[{{ $i }}][type]" value="external">
                                            <input type="hidden" name="instrukturs[{{ $i }}][presenter_id]" value="{{ $presenter->presenter_id }}">
                                            {{ $presenter->presenter_name }}
                                        @endif
                                    </td>
                                    <td><input type="number" name="instrukturs[{{ $i }}][instruktur_penguasaan]" min="1" max="5" class="form-control" required></td>
                                    <td><input type="number" name="instrukturs[{{ $i }}][instruktur_teknik]" min="1" max="5" class="form-control" required></td>
                                    <td><input type="number" name="instrukturs[{{ $i }}][instruktur_sistematika]" min="1" max="5" class="form-control" required></td>
                                    <td><input type="number" name="instrukturs[{{ $i }}][instruktur_waktu]" min="1" max="5" class="form-control" required></td>
                                    <td><input type="number" name="instrukturs[{{ $i }}][instruktur_proses]" min="1" max="5" class="form-control" required></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-warning">Tidak ada instruktur/presenter untuk pelatihan ini.</div>
                @endif

                {{-- Komplain / Masukan --}}
                <div class="mb-4">
                    <label for="komplain_saran_masukan" class="form-label">Komplain / Masukan Lain</label>
                    <textarea name="komplain_saran_masukan" rows="3" class="form-control @error('komplain_saran_masukan') is-invalid @enderror" required>{{ old('komplain_saran_masukan') }}</textarea>
                    @error('komplain_saran_masukan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Kirim Evaluasi</button>
                </div>

            </div>
        </div>
    </form>

</div>
@endsection
