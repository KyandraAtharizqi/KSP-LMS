@extends('layout.main')

@section('title', 'Undangan Knowledge Sharing')

@section('content')
<div class="page-heading">
    <h3>Undangan Knowledge Sharing</h3>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-body">
            <!-- Header Undangan -->
            <div class="text-center mb-4">
                <h4 class="text-primary">UNDANGAN</h4>
                <h5>{{ $undangan->perihal }}</h5>
                <hr>
            </div>

            <!-- Informasi Undangan -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td style="width: 30%"><strong>Kepada</strong></td>
                            <td style="width: 5%">:</td>
                            <td>{{ $undangan->kepada }}</td>
                        </tr>
                        <tr>
                            <td><strong>Dari</strong></td>
                            <td>:</td>
                            <td>{{ $undangan->dari }}</td>
                        </tr>
                        <tr>
                            <td><strong>Perihal</strong></td>
                            <td>:</td>
                            <td>{{ $undangan->perihal }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td style="width: 30%"><strong>Kode</strong></td>
                            <td style="width: 5%">:</td>
                            <td>{{ $undangan->kode }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal</strong></td>
                            <td>:</td>
                            <td>{{ $undangan->tanggal_mulai->format('d F Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Konten Undangan -->
            <div class="mb-4">
                <p>Dengan hormat,</p>
                <p>Bersama ini kami mengundang Bapak/Ibu untuk menghadiri acara <strong>Knowledge Sharing</strong> dengan rincian sebagai berikut:</p>
                
                <div class="card bg-light">
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td style="width: 20%"><strong>Judul</strong></td>
                                <td style="width: 5%">:</td>
                                <td>{{ $undangan->judul }}</td>
                            </tr>
                            <tr>
                                <td><strong>Pemateri</strong></td>
                                <td>:</td>
                                <td>{{ $undangan->pemateri }}</td>
                            </tr>
                            <tr>
                                <td><strong>Hari/Tanggal</strong></td>
                                <td>:</td>
                                <td>
                                    {{ $undangan->tanggal_mulai->format('l, d F Y') }}
                                    @if($undangan->tanggal_mulai->format('Y-m-d') !== $undangan->tanggal_selesai->format('Y-m-d'))
                                        s.d {{ $undangan->tanggal_selesai->format('l, d F Y') }}
                                    @endif
                                </td>
                            </tr>
                            @if($undangan->jam_mulai || $undangan->jam_selesai)
                            <tr>
                                <td><strong>Waktu</strong></td>
                                <td>:</td>
                                <td>
                                    @if($undangan->jam_mulai && $undangan->jam_selesai)
                                        {{ $undangan->jam_mulai }} - {{ $undangan->jam_selesai }} WIB
                                    @elseif($undangan->jam_mulai)
                                        {{ $undangan->jam_mulai }} WIB
                                    @elseif($undangan->jam_selesai)
                                        s.d {{ $undangan->jam_selesai }} WIB
                                    @endif
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
                
                <p class="mt-3">Demikian undangan ini kami sampaikan. Atas perhatian dan kehadiran Bapak/Ibu, kami ucapkan terima kasih.</p>
            </div>

            <!-- Daftar Peserta -->
            @if($undangan->peserta && count($undangan->peserta) > 0)
            <div class="mb-4">
                <h6>Peserta yang Diundang:</h6>
                <div class="row">
                    @foreach($undangan->peserta as $index => $peserta)
                        @php
                            $user = \App\Models\User::find($peserta['id']);
                        @endphp
                        <div class="col-md-6 col-lg-4 mb-2">
                            <div class="card border-0 bg-light">
                                <div class="card-body py-2 px-3">
                                    <small>
                                        <strong>{{ $index + 1 }}. {{ $user ? $user->name : ($peserta['name'] ?? 'N/A') }}</strong><br>
                                        <span class="text-muted">{{ $user ? $user->jabatan_full : '' }}</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Lampiran -->
            @if($undangan->lampiran)
            <div class="mb-4">
                <h6>Lampiran:</h6>
                <a href="{{ asset('storage/' . $undangan->lampiran) }}" target="_blank" class="btn btn-outline-primary">
                    <i class="bx bx-file"></i> Lihat Lampiran
                </a>
            </div>
            @endif

            <!-- Footer Actions -->
            <div class="d-flex gap-2 mt-4">
                <a href="{{ route('knowledge.undangan.index') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back"></i> Kembali
                </a>
                
                @if(auth()->user()->role == 'admin' || auth()->user()->id == $undangan->created_by || auth()->user()->name == $undangan->kepada)
                    @if($undangan->status_undangan === 'draft')
                        <a href="{{ route('knowledge.undangan.edit', $undangan->id) }}" class="btn btn-warning">
                            <i class="bx bx-edit"></i> Edit Tanggal
                        </a>
                    @endif
                @endif
                
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="bx bx-printer"></i> Cetak
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
@media print {
    .btn, .page-heading, .card {
        display: none !important;
    }
    
    .card-body {
        display: block !important;
        box-shadow: none !important;
        border: none !important;
    }
}
</style>
@endpush
