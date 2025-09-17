@extends('layout.main')

@section('title', isset($knowledge) ? 'Edit Knowledge Sharing' : 'Tambah Knowledge Sharing')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize multi-date picker
        flatpickr('.tanggal-pelaksanaan', {
            mode: 'multiple',
            dateFormat: 'Y-m-d'
        });

        // Add/remove pemateri rows
        let pemateriIndex = {{ isset($knowledge) && $knowledge->pemateri ? count(json_decode($knowledge->pemateri, true)) : 1 }};
        document.getElementById('addPemateri').addEventListener('click', function () {
            pemateriIndex++;
            const container = document.getElementById('pemateriContainer');
            const html = `
                <div class="row mb-2 pemateri-row">
                    <div class="col-md-5">
                        <input type="text" name="pemateri[${pemateriIndex}][name]" class="form-control" placeholder="Nama Pemateri">
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="pemateri[${pemateriIndex}][registration_id]" class="form-control" placeholder="Registration ID">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm removePemateri">Hapus</button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        });

        document.addEventListener('click', function(e) {
            if(e.target && e.target.classList.contains('removePemateri')){
                e.target.closest('.pemateri-row').remove();
            }
        });
    });
</script>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">{{ isset($knowledge) ? 'Edit' : 'Tambah' }} Knowledge Sharing</h4>

    <form method="POST" action="{{ isset($knowledge) ? route('knowledge.update', $knowledge->id) : route('knowledge.store') }}">
        @csrf
        @if(isset($knowledge))
            @method('PUT')
        @endif

        <div class="card mb-4">
            <div class="card-body">

                {{-- Tipe --}}
                <div class="mb-3">
                    <label class="form-label">Tipe</label>
                    <select name="tipe" class="form-select" required>
                        <option value="">-- Pilih Tipe --</option>
                        <option value="lanjutan" {{ isset($knowledge) && $knowledge->tipe === 'lanjutan' ? 'selected' : '' }}>Lanjutan</option>
                        <option value="non_lanjutan" {{ isset($knowledge) && $knowledge->tipe === 'non_lanjutan' ? 'selected' : '' }}>Non Lanjutan</option>
                    </select>
                </div>

                {{-- Judul --}}
                <div class="mb-3">
                    <label class="form-label">Judul</label>
                    <input type="text" name="judul" class="form-control" value="{{ $knowledge->judul ?? '' }}" required>
                </div>

                {{-- Materi --}}
                <div class="mb-3">
                    <label class="form-label">Materi</label>
                    <textarea name="materi" class="form-control" rows="3">{{ $knowledge->materi ?? '' }}</textarea>
                </div>

                {{-- Pemateri --}}
                <div class="mb-3">
                    <label class="form-label">Pemateri</label>
                    <div id="pemateriContainer">
                        @if(isset($knowledge) && $knowledge->pemateri)
                            @foreach(json_decode($knowledge->pemateri, true) as $index => $p)
                            <div class="row mb-2 pemateri-row">
                                <div class="col-md-5">
                                    <input type="text" name="pemateri[{{ $index }}][name]" class="form-control" value="{{ $p['name'] ?? '' }}" placeholder="Nama Pemateri">
                                </div>
                                <div class="col-md-5">
                                    <input type="text" name="pemateri[{{ $index }}][registration_id]" class="form-control" value="{{ $p['registration_id'] ?? '' }}" placeholder="Registration ID">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger btn-sm removePemateri">Hapus</button>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="row mb-2 pemateri-row">
                                <div class="col-md-5">
                                    <input type="text" name="pemateri[0][name]" class="form-control" placeholder="Nama Pemateri">
                                </div>
                                <div class="col-md-5">
                                    <input type="text" name="pemateri[0][registration_id]" class="form-control" placeholder="Registration ID">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger btn-sm removePemateri">Hapus</button>
                                </div>
                            </div>
                        @endif
                    </div>
                    <button type="button" class="btn btn-sm btn-primary mt-1" id="addPemateri">+ Tambah Pemateri</button>
                </div>

                {{-- Tanggal Pelaksanaan --}}
                <div class="mb-3">
                    <label class="form-label">Tanggal Pelaksanaan</label>
                    <input type="text" name="tanggal_pelaksanaan[]" class="form-control tanggal-pelaksanaan" value="{{ isset($knowledge) && $knowledge->tanggal_pelaksanaan ? implode(',', json_decode($knowledge->tanggal_pelaksanaan)) : '' }}" placeholder="Pilih tanggal" readonly>
                    <small class="text-muted">Pilih satu atau beberapa tanggal</small>
                </div>

                {{-- Tempat --}}
                <div class="mb-3">
                    <label class="form-label">Tempat</label>
                    <input type="text" name="tempat" class="form-control" value="{{ $knowledge->tempat ?? '' }}">
                </div>

                {{-- Penyelenggara --}}
                <div class="mb-3">
                    <label class="form-label">Penyelenggara</label>
                    <input type="text" name="penyelenggara" class="form-control" value="{{ $knowledge->penyelenggara ?? '' }}">
                </div>

                {{-- Paraf & Signature --}}
                <div class="mb-3">
                    <label class="form-label">Paraf (Max 3)</label>
                    @for($i=0;$i<3;$i++)
                        <select name="parafs[{{ $i }}]" class="form-select mb-1">
                            <option value="">-- Pilih Paraf --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ isset($parafs[$i]) && $parafs[$i] == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->registration_id ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    @endfor
                </div>

                <div class="mb-3">
                    <label class="form-label">Signature (1)</label>
                    <select name="signature" class="form-select">
                        <option value="">-- Pilih Signature --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ isset($signature) && $signature == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->registration_id ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('knowledge.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">{{ isset($knowledge) ? 'Update' : 'Simpan' }}</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
