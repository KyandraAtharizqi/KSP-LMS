@extends('layout.main')

@section('title', 'Detail Pengajuan Knowledge Sharing')

@section('content')
<div class="page-heading">
    <h3>Detail Pengajuan</h3>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-header text-center">
            <h4 class="mb-1 fw-bold">Pengajuan {{ $pengajuan->perihal }}</h4>
        </div>

        <div class="card-body">
            <div class="row mb-2">
                <div class="col-sm-3"><strong>Kepada Yth</strong></div>
                <div class="col-sm-9">: {{ $pengajuan->kepada }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-3"><strong>Dari</strong></div>
                <div class="col-sm-9">: {{ $pengajuan->dari }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-3"><strong>Tanggal</strong></div>
                <div class="col-sm-9">
                    : {{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->format('d M Y') }}
                    -
                    {{ \Carbon\Carbon::parse($pengajuan->tanggal_selesai)->format('d M Y') }}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-3"><strong>Lampiran</strong></div>
                <div class="col-sm-9">:
                    @if ($pengajuan->lampiran)
                        <a href="{{ asset('storage/' . $pengajuan->lampiran) }}" target="_blank">Lihat Lampiran (PDF)</a>
                    @else
                        <span class="text-muted">Tidak ada lampiran</span>
                    @endif
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-sm-3"><strong>Peserta</strong></div>
                <div class="col-sm-9">
                    @if(!empty($pengajuan->pesertaList))
                        <ul class="mb-0">
                            @foreach($pengajuan->pesertaList as $ps)
                                <li>{{ $ps }}</li>
                            @endforeach
                        </ul>
                    @else
                        <span class="text-muted">Tidak ada peserta</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <a href="{{ route('knowledge.pengajuan.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</div>
@endsection
