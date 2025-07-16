<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Tugas - {{ $surat->kode_tugas }}</title>
    <style>
        /* CSS dasar untuk tampilan PDF */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: black;
        }
        .table-bordered-custom {
            width: 100%;
            border-collapse: collapse;
        }
        .table-bordered-custom th,
        .table-bordered-custom td {
            border: 1px solid black;
            padding: 8px;
            vertical-align: top;
        }
        .text-center { text-align: center; }
        .mb-0 { margin-bottom: 0; }
        .mt-4 { margin-top: 1.5rem; }
        .signature-space { height: 70px; }
        strong { font-weight: bold; }
        ol, ul {
            padding-left: 20px;
            margin: 0;
        }
        .row::after {
            content: "";
            clear: both;
            display: table;
        }
        .col-7 {
            float: left;
            width: 65%;
        }
        .col-5 {
            float: right;
            width: 35%;
        }
    </style>
</head>
<body>

    <table style="width:100%; border:none; margin-bottom: 1.5rem;">
        <tr>
            <td style="width:20%;">
                {{-- <img src="{{ public_path('logoksp.png') }}" alt="Logo KSP" style="height: 50px;"> --}}
            </td>
            <td style="width:60%; text-align:center;">
                <h4 style="margin:0; font-size: 16px;"><strong>SURAT TUGAS PELATIHAN</strong></h4>
                <p style="margin:0;">Nomor: {{ $surat->kode_tugas ?? 'STP/XXX/HC-KSP/VII/2025' }}</p>
            </td>
            <td style="width:20%; text-align:right;">
                 {{-- <img src="{{ public_path('logoykan.png') }}" alt="Logo YKAN" style="height: 50px;"> --}}
            </td>
        </tr>
    </table>
    
    <table class="table-bordered-custom">
        <tr>
            <td style="width: 30%;"><strong>Peserta</strong></td>
            <td>
                {{-- <ol>
                    @forelse ($surat->pelatihan->participants ?? [] as $participant)
                        <li>
                            {{ $participant->user->name ?? 'Nama tidak ada' }}
                            ({{ $participant->jabatan->name ?? 'Jabatan tidak ada' }},
                            {{ $participant->department->name ?? 'Departemen tidak ada' }})
                        </li>
                    @empty
                        <li>-</li>
                    @endforelse
                </ol> --}}
            </td>
        </tr>
        <tr>
            <td><strong>Judul Pelatihan</strong></td>
            <td>{{ $surat->judul ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Tempat & Tanggal</strong></td>
            <td>
                {{ $surat->tempat ?? '-' }}, 
                {{ optional($surat->pelatihan->tanggal_mulai)->format('d M Y') }} s.d. {{ optional($surat->pelatihan->tanggal_selesai)->format('d M Y') }}
            </td>
        </tr>
        <tr>
            <td><strong>Penyelenggara</strong></td>
            <td>{{ $surat->pelatihan->penyelenggara ?? '-' }}</td>
        </tr>
    </table>

    <p style="margin-top: 1rem;">Demikian surat tugas ini dibuat untuk dilaksanakan dengan sebaik-baiknya dan penuh tanggung jawab.</p>

    <div class="row mt-4">
        <div class="col-7">
            <p><strong>Tembusan:</strong></p>
            <ul>
                <li>Direktur Utama</li>
                <li>Manager Terkait</li>
                <li>Arsip</li>
            </ul>
        </div>
        <div class="col-5 text-center">
            <p>Cilegon, {{ optional($surat->tanggal)->format('d F Y') }}</p>

            {{-- Menggunakan relasi yang benar: signaturesAndParafs --}}
            @foreach ($surat->signaturesAndParafs->where('type', 'signature') as $sig)
                <div style="margin-bottom: 1rem;">
                    <p class="mb-0"><strong>{{ $sig->user->jabatan->name ?? ucfirst($sig->type) }}</strong></p>
                    
                    <div class="signature-space">
                        @if ($sig->status === 'approved' && $sig->user->signature_path)
                            {{-- Untuk PDF, gunakan public_path() untuk mengakses gambar dari storage --}}
                            {{-- <img src="{{ public_path('storage/' . $sig->user->signature_path) }}" alt="Tanda Tangan" style="height: 70px;"> --}}
                        @endif
                    </div>

                    <p><strong><u>{{ $sig->user->name ?? '-' }}</u></strong></p>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>
