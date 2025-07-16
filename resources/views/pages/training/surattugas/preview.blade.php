@extends('layout.main')

{{-- Menambahkan sedikit CSS untuk garis tabel agar mirip dengan di kertas --}}
@push('styles')
<style>
    .table-bordered-custom,
    .table-bordered-custom th,
    .table-bordered-custom td {
        border: 1px solid black;
        vertical-align: top;
    }
    .table-bordered-custom {
        width: 100%;
        border-collapse: collapse;
    }
    .header-logo {
        height: 50px;
    }
    .signature-space {
        height: 80px;
    }
    .no-border, .no-border tr, .no-border td {
        border: none;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Preview Surat Tugas Pelatihan</h4>
        <div>
            <a href="{{ route('training.surattugas.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>

    <div class="card">
        {{-- Penambahan style="color: black;" untuk memastikan semua teks berwarna hitam --}}
        <div class="card-body px-5 py-4" style="color: black;">
            
            {{-- BAGIAN KOP SURAT --}}
            <div class="row mb-4 align-items-center">
                <div class="col-3">
                    {{-- Ganti 'path/to/logo_ksp.png' dengan path logo Anda --}}
                    <img src="{{ asset('logoksp.png') }}" alt="Logo" style="height: 40px;">
                </div>
                <div class="col-6 text-center">
                    <h4 class="mb-0"><strong>SURAT TUGAS PELATIHAN</strong></h4>
                    {{-- Ganti dengan variabel nomor surat jika ada --}}
                </div>
                <div class="col-3 text-end">
                    {{-- Ganti 'path/to/logo_ykan.png' dengan path logo Anda --}}
                    <img src="{{ asset('path/to/logo_ykan.png') }}" alt="Logo YKAN" class="header-logo">
                </div>
            </div>

            {{-- BAGIAN ISI SURAT --}}
            <table class="table table-bordered-custom">
                <tr>
                    <td colspan="2">Kepada Yth.</td>
                </tr>
                <tr>
                    <td><strong>Peserta</strong></td>
                    <td>
                        <ol class="mb-0 ps-3">
                            @forelse ($surat->pelatihan->participants ?? [] as $participant)
                                <li>
                                    {{-- Menggabungkan nama, jabatan, dan departemen dalam satu baris --}}
                                    {{ $participant->user->name }} 
                                    ({{ $participant->user->department->name ?? '-' }})
                                </li>
                            @empty
                                <li>-</li>
                            @endforelse
                        </ol>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">Untuk mengikuti kegiatan pelatihan dengan informasi sebagai berikut:</td>
                </tr>
                <tr>
                    <td><strong>Judul</strong></td>
                    <td>{{ $surat->judul }}</td>
                </tr>
                <tr>
                    <td><strong>Tujuan / Sasaran</strong></td>
                    <td>
                        <ol class="mb-0 ps-3">
                           <li>MEMENUHI GAP KOMPETENSI</li>
                           <li>MEMBERIKAN PEMAHAMAN TERKAIT</li>
                           <li>MENINGKATKAN KOMPETENSI TERKAIT</li>
                        </ol>
                    </td>
                </tr>
                <tr>
                    <td><strong>Nama Instruktur / Lembaga</strong></td>
                    <td>{{ $surat->pelatihan?->penyelenggara ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Hari, Tanggal Pelaksanaan Pelatihan</strong></td>
                    <td>
                        {{ $surat->pelatihan?->tanggal_mulai?->format('l, d F Y') ?? $surat->tanggal?->format('l, d F Y') }}
                        s.d.
                        {{ $surat->pelatihan?->tanggal_selesai?->format('l, d F Y') ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td><strong>Tempat Pelaksanaan Pelatihan</strong></td>
                    <td>{{ $surat->tempat }}</td>
                </tr>
                <tr>
                    <td><strong>Waktu Pelaksanaan Pelatihan</strong></td>
                    {{-- Ganti dengan variabel waktu jika ada --}}
                    <td>09:00 - 17:00 WIB</td>
                </tr>
                <tr>
                    <td><strong>Instruksi Saat Kegiatan</strong></td>
                    <td>
                        <ol class="mb-0 ps-3">
                            <li>Agar mengikuti jadwal yang telah ditentukan sampai dengan selesai.</li>
                            <li>Memperhatikan aspek-aspek Keselamatan dan Kesehatan Kerja (K3) selama kegiatan berlangsung.</li>
                        </ol>
                    </td>
                </tr>
                 <tr>
                    <td><strong>Hal-hal yang perlu diperhatikan</strong></td>
                    <td>
                        <ol class="mb-0 ps-3">
                           <li>Copy Materi pelatihan diserahkan ke Human Capital Department.</li>
                           <li>Copy Sertifikat diserahkan ke Human Capital Department.</li>
                           <li>Agar menerapkan materi pelatihan di lingkungan kerja PT. KSP.</li>
                        </ol>
                    </td>
                </tr>
                 <tr>
                    <td><strong>Catatan</strong></td>
                    <td>Apabila tidak mengikuti tugas ini maka akan dikenakan sanksi sesuai dengan PKB Pasal 86 ayat 2 point a butir 5.</td>
                </tr>
            </table>

            <br>

            {{-- BAGIAN TANDA TANGAN & TEMBUSAN --}}
            <div class="row mt-4">
                <div class="col-7">
                    <p><strong>Tembusan:</strong></p>
                    <ul class="ps-3">
                        <li>Direktur Utama</li>
                        <li>Manager Terkait</li>
                        <li>Arsip</li>
                    </ul>
                </div>
                <div class="col-5 text-center">
                    <p>Cilegon, {{ $surat->tanggal?->format('d F Y') }}</p>
                    <p>Dibuat Oleh,</p>


                    @foreach ($surat->signatures as $sig)
                        <div class="mb-3">
                            <p class="mb-0"><strong>{{ $sig->user->jabatan?->nama ?? ucfirst($sig->type) }}</strong></p>
                            
                            <div class="signature-space">
                                @if ($sig->status === 'approved' && $sig->signed_at && $sig->user?->signature_path)
                                    <img src="{{ asset('storage/signatures/' . $sig->user->signature_path) }}" alt="Tanda Tangan" style="height: 80px;">
                                @elseif ($sig->status === 'rejected')
                                    {{-- Kelas 'text-danger' dihapus --}}
                                    <p>(Ditolak)</p>
                                @else
                                    {{-- Kelas 'text-muted' dihapus --}}
                                    <p>(Menunggu Tanda Tangan)</p>
                                @endif
                            </div>

                            <p><strong><u>{{ $sig->user->name }}</u></strong></p>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>
@endsection