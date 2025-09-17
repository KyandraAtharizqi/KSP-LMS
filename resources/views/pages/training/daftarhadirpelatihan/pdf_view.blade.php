{{-- resources/views/pages/training/daftarhadirpelatihan/pdf_view.blade.php --}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Hadir Pelatihan - {{ $pelatihan->judul }}</title>
    <style>
        /* DomPDF-safe styles */
        @page { margin: 20px 25px; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: middle; }
        .header-left img,
        .header-right img { height: 40px; }
        .header-center { text-align: center; }
        .title { margin: 0; font-size: 18px; font-weight: bold; }
        .subtitle { margin: 2px 0 0; }
        .kode { margin: 2px 0 0; font-weight: bold; }

        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #000; padding: 6px; font-size: 11px; }
        th { background: #f0f0f0; text-align: center; }
        .no-border td, .no-border th { border: none; padding: 0; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
    </style>
</head>
<body>

    {{-- KOP --}}
    <table class="header-table no-border">
        <tr>
            <td class="header-left" style="width:25%;">
                <img src="{{ public_path('logo.png') }}" alt="Logo">
            </td>
            <td class="header-center" style="width:50%;">
                <h2 class="title">DAFTAR HADIR PELATIHAN</h2>
                <div class="kode">Kode Pelatihan: {{ $pelatihan->kode_pelatihan ?? '-' }}</div>
                <div class="subtitle">{{ $pelatihan->judul ?? '-' }}</div>
            </td>
            <td class="header-right text-right" style="width:25%; text-align:right;">
                <img src="{{ public_path('logo.png') }}" alt="Logo YKAN">
            </td>
        </tr>
    </table>

    {{-- INFORMASI PELATIHAN --}}
    <table>
        <tr>
            <th style="width:28%;">Judul Pelatihan</th>
            <td>{{ $pelatihan->judul ?? '-' }}</td>
        </tr>
        <tr>
            <th>Tanggal</th>
            <td>{{ \Carbon\Carbon::parse($day)->format('l, d F Y') }}</td>
        </tr>
        <tr>
            <th>Penyelenggara</th>
            <td>{{ $pelatihan->penyelenggara ?? '-' }}</td>
        </tr>
        <tr>
            <th>Tempat</th>
            <td>{{ $pelatihan->tempat ?? '-' }}</td>
        </tr>
        <tr>
            <th>Presenter</th>
            <td>
                @php $presenters = $pelatihan->presenters ?? collect(); @endphp
                @if($presenters->isEmpty())
                    -
                @else
                    @foreach($presenters as $idx => $presenter)
                        @if($presenter->type === 'internal' && $presenter->user)
                            {{ $presenter->user->name }} (Internal)
                        @elseif($presenter->type === 'external' && $presenter->presenter)
                            {{ $presenter->presenter->name }} (External)
                        @else
                            -
                        @endif
                        @if(!$loop->last), @endif
                    @endforeach
                @endif
            </td>
        </tr>
    </table>

    {{-- TABEL KEHADIRAN --}}
    <table>
        <thead>
            <tr>
                <th style="width:4%;">No</th>
                <th style="width:10%;">Reg. ID</th>
                <th style="width:16%;">Nama Peserta</th>
                <th style="width:12%;">Jabatan</th>
                <th style="width:12%;">Unit / Dept</th>
                <th style="width:12%;">Divisi</th>
                <th style="width:12%;">Direktorat</th>
                <th style="width:8%;">Gol.</th>
                <th style="width:8%;">Status</th>
                <th style="width:8%;">Check In</th>
                <th style="width:8%;">Check Out</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pelatihan->participants as $i => $p)
                @php $att = $attendances->get($p->user_id); @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $p->registration_id ?? '-' }}</td>
                    <td class="text-left">{{ $p->user->name ?? '-' }}</td>
                    <td class="text-left">{{ $p->jabatan_full ?? ($p->jabatan->name ?? '-') }}</td>
                    <td class="text-left">{{ $p->department->name ?? '-' }}</td>
                    <td class="text-left">{{ $p->division->name ?? '-' }}</td>
                    <td class="text-left">{{ $p->directorate->name ?? '-' }}</td>
                    <td class="text-center">{{ $p->golongan ?? '-' }}</td>
                    <td class="text-center">{{ $att->status ?? 'absen' }}</td>
                    <td class="text-center">{{ $att->check_in_time ?? '-' }}</td>
                    <td class="text-center">{{ $att->check_out_time ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</table>
</body>
</html>