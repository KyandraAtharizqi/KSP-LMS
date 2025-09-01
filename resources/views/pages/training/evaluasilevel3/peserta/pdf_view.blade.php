<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Evaluasi Level 3 - Peserta</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; line-height: 1.4; }
        h3, h4 { margin: 0; padding: 0; }
        .text-center { text-align: center; }
        .mb-2 { margin-bottom: 10px; }
        .mb-3 { margin-bottom: 15px; }
        .mb-4 { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; vertical-align: top; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <div class="text-center mb-4">
        <h3><strong>Evaluasi Level 3 - Peserta</strong></h3>
        <p>{{ $evaluasi->pelatihan->judul }} ({{ $evaluasi->pelatihan->kode_pelatihan }})</p>
    </div>

    {{-- Pelatihan Info --}}
    <table>
        <tr>
            <th>Kode Pelatihan</th>
            <td>{{ $evaluasi->pelatihan->kode_pelatihan }}</td>
        </tr>
        <tr>
            <th>Judul</th>
            <td>{{ $evaluasi->pelatihan->judul }}</td>
        </tr>
        <tr>
            <th>Penyelenggara</th>
            <td>{{ $evaluasi->pelatihan->penyelenggara }}</td>
        </tr>
    </table>

    {{-- Action Plan --}}
    <h4 class="mb-3">Action Plan</h4>
    <table>
        <thead>
            <tr>
                <th>Action Plan</th>
                <th>Diaplikasikan</th>
                <th>Frekuensi</th>
                <th>Hasil</th>
            </tr>
        </thead>
        <tbody>
            @foreach($evaluasi->actionPlans as $plan)
            <tr>
                <td>{{ $plan->action_plan }}</td>
                <td class="text-center">{{ $plan->diaplikasikan ? 'Ya' : 'Tidak' }}</td>
                <td>{{ $plan->frekuensi }}</td>
                <td>{{ $plan->hasil }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Feedback --}}
    @if($evaluasi->feedbacks->isNotEmpty())
        @php $fb = $evaluasi->feedbacks->first(); @endphp
        <h4 class="mb-3">Feedback</h4>
        <table>
            <tr>
                <th>Saya Sudah Mampu</th>
                <td>{{ $fb->telah_mampu ? 'Ya' : 'Tidak' }}</td>
            </tr>
            <tr>
                <th>Butuh Bantuan untuk Cara Mengaplikasikan</th>
                <td>{{ $fb->membantu_mengaplikasikan ? 'Ya' : 'Tidak' }}</td>
            </tr>
            <tr>
                <th>Materi Tidak Bisa Diaplikasikan Karena</th>
                <td>{{ $fb->tidak_diaplikasikan_karena ?: '-' }}</td>
            </tr>
            <tr>
                <th>Memberikan Informasi Mengenai</th>
                <td>{{ $fb->memberikan_informasi_mengenai ?: '-' }}</td>
            </tr>
            <tr>
                <th>Lain-lain</th>
                <td>{{ $fb->lain_lain ?: '-' }}</td>
            </tr>
        </table>
    @endif

    {{-- General Section --}}
    <h4 class="mb-3">General</h4>
    <table>
        <tr>
            <th>Setelah pelatihan ini saya mampu untuk...</th>
            <td>{{ $evaluasi->manfaat_pelatihan ?: '-' }}</td>
        </tr>
        <tr>
            <th>Secara keseluruhan, saya menilai training tersebut dapat meningkatkan kinerja...</th>
            <td>
                {{ $evaluasi->kinerja }}
                @if($evaluasi->kinerja == 0) (Tidak sama sekali)
                @elseif($evaluasi->kinerja == 1) (Cukup membantu)
                @elseif($evaluasi->kinerja == 2) (Sangat membantu)
                @endif
            </td>
        </tr>
        <tr>
            <th>Saran dan Masukan Penyelenggara Training Sejenis</th>
            <td>{{ $evaluasi->saran ?: '-' }}</td>
        </tr>
    </table>
</body>
</html>
