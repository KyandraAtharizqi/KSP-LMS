<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Dinas - {{ $notaDinas->kode }}</title>
    <style>
        @page {
            margin: 3cm; /* Atur margin halaman PDF */
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12px;
            margin: 0; /* Supaya tidak tabrakan dengan @page */
        }

        .container {
            width: 100%;
            padding: 0; /* Tidak perlu padding tambahan jika margin sudah diatur di @page */
        }

        .header, .footer {
            text-align: center;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 0;
        }

        .subtitle {
            font-size: 14px;
            margin-top: 2px;
        }

        .info-table {
            width: 100%;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .info-table td {
            vertical-align: top;
            padding: 5px;
        }

        .content {
            margin-top: 15px;
            text-align: justify;
        }

        .signature {
            margin-top: 200px;
            text-align: right;
            margin-right: 0;
        }

        .signature img {
            max-height: 80px;
        }

        .label {
            width: 25%;
        }

        .value {
            width: 75%;
        }
    </style>
</head>
<body>
    <div class="container">

        <div class="header">
            <div class="title">NOTA DINAS</div>
            <div class="subtitle">No. {{ $notaDinas->kode }}</div>
        </div>

        <table class="info-table" border="0" style="margin-top: 40px;">
            <tr>
                <td class="label"><strong>Kepada Yth</strong></td>
                <td class="value">: {{ $notaDinas->kepada }}</td>
            </tr>
            <tr>
                <td class="label"><strong>Dari</strong></td>
                <td class="value">: {{ $notaDinas->dari }}</td>
            </tr>
            <tr>
                <td class="label"><strong>Perihal</strong></td>
                <td class="value">: {{ $notaDinas->perihal }}</td>
            </tr>
            <tr>
                <td class="label"><strong>Tanggal</strong></td>
                <td class="value">: {{ \Carbon\Carbon::parse($notaDinas->tanggal)->format('d M Y') }}</td>
            </tr>
            <tr>
                <td class="label"><strong>Lampiran</strong></td>
                <td class="value">
                    @if ($notaDinas->lampiran)
                        : Ada Lampiran
                    @else
                        : Tidak ada lampiran
                    @endif
                </td>
            </tr>
        </table>

        <hr style="border-top: 0.5px solid #000; margin-top: 20px; margin-bottom: 20px;">

        <div class="content">
            <p>Dengan Hormat,</p>
            <p>Bersama ini disampaikan hasil Kegiatan Knowledge Sharing dengan judul 
                "{{ $notaDinas->judul }}" dengan pemateri {{ $notaDinas->pemateri }}.</p>
            <p>Mohon dokumen tersebut dapat dimasukkan ke dalam nilai AKM sesuai aturan yang berlaku.</p>
            <p>Demikian disampaikan, atas perhatiannya diucapkan terima kasih.</p>
        </div>

        @if ($notaDinas->user && $notaDinas->user->signatureParaf && $notaDinas->user->signatureParaf->signature_path)
        <div class="signature">
            <img src="{{ public_path('storage/' . $notaDinas->user->signatureParaf->signature_path) }}" alt="Tanda Tangan"><br>
            <strong>{{ $notaDinas->user->name }}</strong><br>
            <small>{{ $notaDinas->user->jabatan_full ?? '' }}</small>
        </div>
        @endif

    </div>
</body>
</html>
