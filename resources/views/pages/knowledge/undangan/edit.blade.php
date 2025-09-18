@extends('layout.main')

@section('title', 'Edit Tanggal Undangan Knowledge Sharing')

@section('content')
<div class="page-heading">
    <h3>Edit Tanggal Undangan Knowledge Sharing</h3>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-header">
            <h5>{{ $undangan->perihal }}</h5>
        </div>

        <div class="card-body">
            <form action="{{ route('knowledge.undangan.update', $undangan->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                                   id="tanggal_mulai" name="tanggal_mulai" 
                                   value="{{ old('tanggal_mulai', $undangan->tanggal_mulai?->format('Y-m-d')) }}" required>
                            @error('tanggal_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tanggal_selesai" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                                   id="tanggal_selesai" name="tanggal_selesai" 
                                   value="{{ old('tanggal_selesai', $undangan->tanggal_selesai?->format('Y-m-d')) }}" required>
                            @error('tanggal_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="jam_mulai" class="form-label">Jam Mulai</label>
                            <input type="time" class="form-control @error('jam_mulai') is-invalid @enderror" 
                                   id="jam_mulai" name="jam_mulai" 
                                   value="{{ old('jam_mulai', $undangan->jam_mulai) }}">
                            @error('jam_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="jam_selesai" class="form-label">Jam Selesai</label>
                            <input type="time" class="form-control @error('jam_selesai') is-invalid @enderror" 
                                   id="jam_selesai" name="jam_selesai" 
                                   value="{{ old('jam_selesai', $undangan->jam_selesai) }}">
                            @error('jam_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <h6>Detail Undangan:</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Pemateri:</strong> {{ $undangan->pemateri }}</p>
                            <p><strong>Kepada:</strong> {{ $undangan->kepada }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Judul:</strong> {{ $undangan->judul }}</p>
                            <p><strong>Dari:</strong> {{ $undangan->dari }}</p>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('knowledge.undangan.index') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
