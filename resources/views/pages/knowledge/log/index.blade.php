@extends('layout.main')

@section('title', 'Knowledge Log / Rekap')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Knowledge Sharing Log - {{ date('m/Y') }}</h3>

        <div>
            <a href="{{ route('knowledge.log.index') }}" 
               class="btn btn-outline-primary btn-sm active">
                Detail Log
            </a>
            <a href="#" 
               class="btn btn-outline-success btn-sm disabled">
                Rekapitulasi
            </a>
        </div>
    </div>

    <form method="GET" class="row mb-3">
        <div class="col-md-2">
            <select name="month" class="form-select">
                @for($m=1; $m<=12; $m++)
                    <option value="{{ $m }}" {{ $m==date('n')?'selected':'' }}>{{ $m }}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" name="year" class="form-control" value="{{ date('Y') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama</th>
                <th>Registration ID</th>
                <th>Kode Knowledge</th>
                <th>Judul Knowledge Sharing</th>
                <th>Pengajuan Dept</th>
                <th>Current Dept</th>
                <th>Pengajuan Jabatan</th>
                <th>Current Jabatan</th>
                <th>Jam</th>
            </tr>
        </thead>
        <tbody>
            {{-- Sementara kosong karena belum ada ID knowledge yang bisa ditarik --}}
            {{-- @forelse($logs as $i => $log)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $log->tanggal->format('d-m-Y') }}</td>
                <td>{{ $log->user->name }}</td>
                <td>{{ $log->registration_id }}</td>
                <td>{{ $log->kode_knowledge }}</td>
                <td>{{ $log->knowledge->judul ?? '-' }}</td>
                <td>{{ $log->pengajuan_department }}</td>
                <td>{{ $log->current_department }}</td>
                <td>{{ $log->pengajuan_jabatan_full }}</td>
                <td>{{ $log->current_jabatan_full }}</td>
                <td>{{ $log->jam }}</td>
            </tr>
            @empty --}}
            <tr>
                <td colspan="11" class="text-center">Tidak ada data untuk bulan ini</td>
            </tr>
            {{-- @endforelse --}}
        </tbody>
    </table>
</div>
@endsection