@extends('layout.main')

@section('content')
<div class="container">
    <h4 class="mb-4">Preview Evaluasi Level 3 - Peserta</h4>

    {{-- Pelatihan Info --}}
    <div class="card mb-4">
        <div class="card-body">
            <strong>Kode:</strong> {{ $evaluasi->pelatihan->kode_pelatihan }} <br>
            <strong>Judul:</strong> {{ $evaluasi->pelatihan->judul }} <br>
            <strong>Penyelenggara:</strong> {{ $evaluasi->pelatihan->penyelenggara }}
        </div>
    </div>

    {{-- Action Plan Section --}}
    <h4 class="mb-3">Action Plan</h4>
    <table class="table table-bordered">
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

    {{-- Feedback Section --}}
    <h4 class="mb-3">Feedback</h4>
    @if($evaluasi->feedbacks->isNotEmpty())
        @php $fb = $evaluasi->feedbacks->first(); @endphp
        <table class="table table-bordered">
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
    <table class="table table-bordered">
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

    <a href="{{ route('evaluasi-level-3.peserta.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
