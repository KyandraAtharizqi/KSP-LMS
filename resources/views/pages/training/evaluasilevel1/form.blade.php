@extends('layout.main')

@section('title', 'Isi Evaluasi Pelatihan Level 1')

@section('content')

<div class="page-heading">
    <h3>Evaluasi Pelatihan Level 1 - {{ $pelatihan->judul }}</h3>
</div>

<div class="page-content">
    {{-- Debug Information - Remove this in production --}}
    @if(config('app.debug'))
        <div class="alert alert-info">
            <strong>Debug Info:</strong><br>
            Presenters count: {{ count($presenters) }}<br>
            Registration ID: {{ $registration_id ?? 'Not set' }}<br>
            User ID: {{ auth()->id() }}
        </div>
    @endif

    {{-- Display validation errors --}}
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

    {{-- Display success message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

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

                {{-- Use participant snapshot data --}}
                <dt class="col-sm-3">Nama Peserta</dt>
                <dd class="col-sm-9">{{ $participant->user->name ?? '-' }}</dd>

                <dt class="col-sm-3">NIK</dt>
                <dd class="col-sm-9">{{ $participant->user->nik ?? '-' }}</dd>

                <dt class="col-sm-3">Registration ID</dt>
                <dd class="col-sm-9">{{ $participant->registration_id }}</dd>

                <dt class="col-sm-3">Jabatan Full</dt>
                <dd class="col-sm-9">{{ $participant->jabatan_full ?? ($participant->jabatan->name ?? '-') }}</dd>

                <dt class="col-sm-3">Department</dt>
                <dd class="col-sm-9">{{ $participant->department->name ?? '-' }}</dd>

                <dt class="col-sm-3">Directorate</dt>
                <dd class="col-sm-9">{{ $participant->directorate->name ?? '-' }}</dd>

                <dt class="col-sm-3">Division</dt>
                <dd class="col-sm-9">{{ $participant->division->name ?? '-' }}</dd>

                <dt class="col-sm-3">Golongan</dt>
                <dd class="col-sm-9">{{ $participant->golongan ?? '-' }}</dd>

                <dt class="col-sm-3">Nama Atasan Langsung</dt>
                <dd class="col-sm-9">{{ $participant->superior->name ?? '-' }}</dd>
            </dl>
        </div>
    </div>

    <form action="{{ route('training.evaluasilevel1.store', $pelatihan->id) }}" method="POST">
        @csrf

        {{-- Hidden fields --}}
        <input type="hidden" name="user_id" value="{{ $participant->user_id }}">
        <input type="hidden" name="pelatihan_id" value="{{ $pelatihan->id }}">
        <input type="hidden" name="registration_id" value="{{ $participant->registration_id }}">
        <input type="hidden" name="kode_pelatihan" value="{{ $participant->kode_pelatihan }}">
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
                    <dt class="col-sm-3">Ringkasan Isi Materi Pelatihan yang Diberikan</dt>
                    <dd class="col-sm-9">
                        <textarea name="ringkasan_isi_materi" class="form-control @error('ringkasan_isi_materi') is-invalid @enderror" rows="3" required>{{ old('ringkasan_isi_materi') }}</textarea>
                        @error('ringkasan_isi_materi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </dd>

                    <dt class="col-sm-3"> Hal-hal (Ide/Saran) yang dapat dikembangkan di PT KSP dari hasil Pelatihan ini</dt>
                    <dd class="col-sm-9">
                        <textarea name="ide_saran_pengembangan" class="form-control @error('ide_saran_pengembangan') is-invalid @enderror" rows="3" required>{{ old('ide_saran_pengembangan') }}</textarea>
                        @error('ide_saran_pengembangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </dd>
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
                                    <td>
                                        <input type="number" class="form-control @error($materiFields[$i]) is-invalid @enderror" 
                                               name="{{ $materiFields[$i] }}" min="1" max="5" 
                                               value="{{ old($materiFields[$i]) }}" required>
                                        @error($materiFields[$i])
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
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
                                $penyelenggaraanLabels = ['Pengelolaan pelaksanaan', 'Keteraturan jadwal pelatihan', 'Persiapan pelaksanaan', 'Cepat & tanggap dalam pelayanan', 'Koordinasi dengan instruktur'];
                            @endphp
                            @foreach($penyelenggaraanLabels as $i => $label)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td>{{ $label }}</td>
                                    <td>
                                        <input type="number" class="form-control @error('penyelenggaraan_'.$penyelenggaraanFields[$i]) is-invalid @enderror" 
                                               name="penyelenggaraan_{{ $penyelenggaraanFields[$i] }}" min="1" max="5" 
                                               value="{{ old('penyelenggaraan_'.$penyelenggaraanFields[$i]) }}" required>
                                        @error('penyelenggaraan_'.$penyelenggaraanFields[$i])
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
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
                                $saranaLabels = ['Media pelatihan (audio-visual)', 'Training kit (tas, diktat, (dll)', 'Kenyamanan & kerapihan ruang kelas', 'Kesesuaian sarana dengan proses', 'Belajar-mengajar'];
                            @endphp
                            @foreach($saranaLabels as $i => $label)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td>{{ $label }}</td>
                                    <td>
                                        <input type="number" class="form-control @error('sarana_'.$saranaFields[$i]) is-invalid @enderror" 
                                               name="sarana_{{ $saranaFields[$i] }}" min="1" max="5" 
                                               value="{{ old('sarana_'.$saranaFields[$i]) }}" required>
                                        @error('sarana_'.$saranaFields[$i])
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- D. Instruktur --}}
                <div class="mb-4">
                    <h5>D. Kemampuan Instruktur</h5>
                    @if(count($presenters) > 0)
                        <table class="table table-bordered table-striped">
                            <thead><tr>
                                <th>No</th><th>Nama</th><th>Penguasaan</th><th>Teknik</th><th>Sistematika</th><th>Waktu</th><th>Proses</th>
                            </tr></thead>
                            <tbody>
                                @foreach ($presenters as $i => $presenter)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>
                                            <input type="hidden" name="instrukturs[{{ $i }}][type]" value="{{ $presenter->type }}">
                                            @if($presenter->type === 'internal')
                                                <input type="hidden" name="instrukturs[{{ $i }}][user_id]" value="{{ $presenter->user_id }}">
                                            @else
                                                <input type="hidden" name="instrukturs[{{ $i }}][presenter_id]" value="{{ $presenter->presenter_id }}">
                                            @endif
                                            <input type="text" value="{{ $presenter->presenter_name }}" class="form-control" readonly>
                                        </td>
                                        <td>
                                            <input type="number" name="instrukturs[{{ $i }}][instruktur_penguasaan]" 
                                                   class="form-control @error('instrukturs.'.$i.'.instruktur_penguasaan') is-invalid @enderror" 
                                                   min="1" max="5" value="{{ old('instrukturs.'.$i.'.instruktur_penguasaan') }}" required>
                                            @error('instrukturs.'.$i.'.instruktur_penguasaan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" name="instrukturs[{{ $i }}][instruktur_teknik]" 
                                                   class="form-control @error('instrukturs.'.$i.'.instruktur_teknik') is-invalid @enderror" 
                                                   min="1" max="5" value="{{ old('instrukturs.'.$i.'.instruktur_teknik') }}" required>
                                            @error('instrukturs.'.$i.'.instruktur_teknik')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" name="instrukturs[{{ $i }}][instruktur_sistematika]" 
                                                   class="form-control @error('instrukturs.'.$i.'.instruktur_sistematika') is-invalid @enderror" 
                                                   min="1" max="5" value="{{ old('instrukturs.'.$i.'.instruktur_sistematika') }}" required>
                                            @error('instrukturs.'.$i.'.instruktur_sistematika')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" name="instrukturs[{{ $i }}][instruktur_waktu]" 
                                                   class="form-control @error('instrukturs.'.$i.'.instruktur_waktu') is-invalid @enderror" 
                                                   min="1" max="5" value="{{ old('instrukturs.'.$i.'.instruktur_waktu') }}" required>
                                            @error('instrukturs.'.$i.'.instruktur_waktu')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" name="instrukturs[{{ $i }}][instruktur_proses]" 
                                                   class="form-control @error('instrukturs.'.$i.'.instruktur_proses') is-invalid @enderror" 
                                                   min="1" max="5" value="{{ old('instrukturs.'.$i.'.instruktur_proses') }}" required>
                                            @error('instrukturs.'.$i.'.instruktur_proses')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-warning">
                            No presenters/instructors found for this training. Please contact administrator.
                        </div>
                    @endif
                </div>

                <dl class="row mb-0">
                    <dt class="col-sm-3">Komplain / Masukan Lain</dt>
                    <dd class="col-sm-9">
                        <textarea name="komplain_saran_masukan" rows="3" 
                                  class="form-control @error('komplain_saran_masukan') is-invalid @enderror" 
                                  required>{{ old('komplain_saran_masukan') }}</textarea>
                        @error('komplain_saran_masukan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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