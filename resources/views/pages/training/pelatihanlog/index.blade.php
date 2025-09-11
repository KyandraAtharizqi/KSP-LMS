@extends('layout.main')

@section('title', 'Pelatihan Log / Rekap')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Pelatihan Log - {{ $month }}/{{ $year }}</h3>

        <div>
            <a href="{{ route('training.pelatihanlog.index', ['month' => $month, 'year' => $year]) }}" 
               class="btn btn-outline-primary btn-sm {{ request()->routeIs('training.pelatihanlog.index') ? 'active' : '' }}">
                Detail Log
            </a>
            <a href="{{ route('training.pelatihanlog.rekap', ['month' => $month, 'year' => $year]) }}" 
               class="btn btn-outline-success btn-sm {{ request()->routeIs('training.pelatihanlog.rekap') ? 'active' : '' }}">
                Rekapitulasi
            </a>
        </div>
    </div>

    <form method="GET" class="row mb-3">
        <div class="col-md-2">
            <select name="month" class="form-select">
                @for($m=1; $m<=12; $m++)
                    <option value="{{ $m }}" {{ $m==$month?'selected':'' }}>{{ $m }}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" name="year" class="form-control" value="{{ $year }}">
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
                <th>Kode Pelatihan</th>
                <th>Judul Pelatihan</th>
                <th>Pengajuan Dept</th>
                <th>Current Dept</th>
                <th>Pengajuan Jabatan</th>
                <th>Current Jabatan</th>
                <th>Jam</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $i => $log)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $log->tanggal->format('d-m-Y') }}</td>
                <td>{{ $log->user->name }}</td>
                <td>{{ $log->registration_id }}</td>
                <td>{{ $log->kode_pelatihan }}</td>
                <td>{{ $log->pelatihan->judul ?? '-' }}</td>
                <td>{{ $log->pengajuan_department }}</td>
                <td>{{ $log->current_department }}</td>
                <td>{{ $log->pengajuan_jabatan_full }}</td>
                <td>{{ $log->current_jabatan_full }}</td>
                <td>{{ $log->jam }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="text-center">Tidak ada data untuk bulan ini</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
