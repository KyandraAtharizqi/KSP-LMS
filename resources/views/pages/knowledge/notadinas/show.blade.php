@extends('layout.main')

@section('title', 'Detail Nota Dinas')

@section('content')
<div class="page-heading">
    <h3>Detail Nota Dinas</h3>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-header text-center">
            <h4 class="mb-1 fw-bold">NOTA DINAS</h4>
            <h6 class="mb-0">No. {{ $notaDinas->kode }}</h6>
        </div>

        <div class="card-body">
            <div class="row mb-2">
                <div class="col-sm-3"><strong>Kepada Yth</strong></div>
                <div class="col-sm-9">: {{ $notaDinas->kepada }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-3"><strong>Dari</strong></div>
                <div class="col-sm-9">: {{ $notaDinas->dari }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-3"><strong>Perihal</strong></div>
                <div class="col-sm-9">: {{ $notaDinas->perihal }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-3"><strong>Tanggal</strong></div>
                <div class="col-sm-9">: {{ \Carbon\Carbon::parse($notaDinas->tanggal)->format('d M Y') }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-3"><strong>Lampiran</strong></div>
                <div class="col-sm-9">:
                    @if ($notaDinas->lampiran)
                        <a href="{{ asset('storage/' . $notaDinas->lampiran) }}" target="_blank">Lihat Lampiran (PDF)</a>
                    @else
                        <span class="text-muted">Tidak ada lampiran</span>
                    @endif
                </div>
            </div>

            <hr class="my-0 mx-0" /> 

            <div class="row my-2">
                <div class="mb-2">Dengan Hormat,</div>
                <div class="mb-2">Bersama ini disampaikan hasil Kegiatan Knowledge Sharing dengan judul "{{ $notaDinas->judul }}" 
                    dengan pemateri {{ $notaDinas->pemateri }}.</div>
                <div class="mb-2">Mohon dokumen tersebut dapat dimasukkan ke dalam nilai AKM sesuai aturan yang berlaku.</div>
                <div class="mb-2">Demikian disampaikan, atas perhatiannya diucapkan terima kasih.</div>
            </div>

            @if ($notaDinas->user && $notaDinas->user->signatureParaf && $notaDinas->user->signatureParaf->signature_path)
                <div class="mt-4 d-flex justify-content-end me-5">
                    <div class="text-end">
                        <img 
                            src="{{ asset('storage/' . $notaDinas->user->signatureParaf->signature_path) }}" 
                            alt="Tanda Tangan"
                            style="max-height: 80px;">
                        <div class="mt-1">{{ $notaDinas->user->name }}</div>
                        <div class="text-muted">{{ $notaDinas->user->jabatan_full ?? '' }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="card-footer text-end">
        <a href="{{ route('knowledge.notadinas.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>
@endsection
