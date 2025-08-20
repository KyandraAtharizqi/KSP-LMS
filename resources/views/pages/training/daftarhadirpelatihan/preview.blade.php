@extends('layout.main')

@push('styles')
<style>
    .table-bordered-custom,
    .table-bordered-custom th,
    .table-bordered-custom td {
        border: 1px solid black;
        vertical-align: top;
        padding: 6px;
    }
    .table-bordered-custom {
        width: 100%;
        border-collapse: collapse;
    }
    .header-logo {
        height: 50px;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Preview Daftar Hadir Pelatihan</h4>
        <div>
            <a href="{{ route('training.daftarhadirpelatihan.index') }}" class="btn btn-secondary">Kembali</a>
            <a href="{{ route('training.daftarhadirpelatihan.download', [$pelatihan->id, $day]) }}" class="btn btn-primary">Download PDF</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body px-5 py-4" style="color: black;">

            {{-- BAGIAN KOP SURAT --}}
            <div class="row mb-4 align-items-center">
                <div class="col-3">
                    <img src="{{ asset('logoksp.png') }}" alt="Logo" style="height: 40px;">
                </div>
                <div class="col-6 text-center">
                    <h4 class="mb-0"><strong>DAFTAR HADIR PELATIHAN</strong></h4>
                    <div><strong>Kode Pelatihan:</strong> {{ $pelatihan->kode_pelatihan ?? '-' }}</div>
                    <div>{{ $pelatihan->judul ?? '-' }}</div>
                </div>
                <div class="col-3 text-end">
                    <img src="{{ asset('path/to/logo_ykan.png') }}" alt="Logo YKAN" class="header-logo">
                </div>
            </div>

            {{-- BAGIAN INFORMASI PELATIHAN --}}
            <table class="table table-bordered-custom mb-4">
                <tr>
                    <td><strong>Judul Pelatihan</strong></td>
                    <td>{{ $pelatihan->judul }}</td>
                </tr>
                <tr>
                    <td><strong>Tanggal</strong></td>
                    <td>{{ \Carbon\Carbon::parse($day)->format('l, d F Y') }}</td>
                </tr>
                <tr>
                    <td><strong>Penyelenggara</strong></td>
                    <td>{{ $pelatihan->penyelenggara ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Tempat</strong></td>
                    <td>{{ $pelatihan->tempat ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Presenter</strong></td>
                    <td>
                        @forelse($pelatihan->presenters as $presenter)
                            @if($presenter->type === 'internal' && $presenter->user)
                                {{ $presenter->user->name }} (Internal)
                            @elseif($presenter->type === 'external' && $presenter->presenter)
                                {{ $presenter->presenter->name }} (External)
                            @endif
                            @if(!$loop->last), @endif
                        @empty
                            -
                        @endforelse
                    </td>
                </tr>
            </table>

            {{-- BAGIAN TABEL KEHADIRAN --}}
            <table class="table table-bordered-custom">
                <thead>
                    <tr>
                        <th style="width:3%;">No</th>
                        <th style="width:10%;">Reg. ID</th>
                        <th style="width:15%;">Nama Peserta</th>
                        <th style="width:12%;">Jabatan</th>
                        <th style="width:12%;">Unit / Dept</th>
                        <th style="width:12%;">Divisi</th>
                        <th style="width:12%;">Direktorat</th>
                        <th style="width:6%;">Gol.</th>
                        <th style="width:8%;">Status</th>
                        <th style="width:8%;">Check In</th>
                        <th style="width:8%;">Check Out</th>
                        <th style="width:12%;">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pelatihan->participants as $i => $p)
                        @php $att = $attendances->get($p->user_id); @endphp
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>{{ $p->registration_id ?? '-' }}</td>
                            <td>{{ $p->user->name ?? '-' }}</td>
                            <td>{{ $p->jabatan_full ?? ($p->jabatan->name ?? '-') }}</td>
                            <td>{{ $p->department->name ?? '-' }}</td>
                            <td>{{ $p->division->name ?? '-' }}</td>
                            <td>{{ $p->directorate->name ?? '-' }}</td>
                            <td class="text-center">{{ $p->golongan ?? '-' }}</td>
                            <td class="text-center">{{ $att->status ?? 'absen' }}</td>
                            <td class="text-center">{{ $att->check_in_time ?? '-' }}</td>
                            <td class="text-center">{{ $att->check_out_time ?? '-' }}</td>
                            <td>{{ $att->note ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>
@endsection
