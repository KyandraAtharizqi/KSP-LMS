@extends('layout.main')

@section('content')
<div class="container">
    <h4 class="mb-4">Approval Evaluasi Level 3 - Atasan</h4>

    {{-- Pelatihan & Peserta Info --}}
    <div class="card mb-4">
        <div class="card-body">
            <strong>Kode Pelatihan:</strong> {{ $evaluasi->pelatihan->kode_pelatihan }} <br>
            <strong>Judul:</strong> {{ $evaluasi->pelatihan->judul }} <br>
            <strong>Penyelenggara:</strong> {{ $evaluasi->pelatihan->penyelenggara }} <br>
            <strong>Peserta:</strong> {{ $evaluasi->user->name }} ({{ $evaluasi->user->nik }}) <br>
            <strong>Unit Kerja:</strong> {{ $evaluasi->user->jabatan->name ?? '-' }}
        </div>
    </div>

    {{-- Action Plan --}}
    <h4 class="mb-3">Action Plan (Peserta)</h4>
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
            @foreach($evaluasi->actionPlans ?? [] as $plan)
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
    <h4 class="mb-3">Feedback (Peserta)</h4>
    @if($evaluasi->feedbacks->isNotEmpty())
        @php $fb = $evaluasi->feedbacks->first(); @endphp
        <table class="table table-bordered">
            <tr>
                <th>Telah Mampu</th>
                <td>{{ $fb->telah_mampu ? 'Ya' : 'Tidak' }}</td>
            </tr>
            <tr>
                <th>Tidak Diaplikasikan Karena</th>
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

    {{-- General --}}
    <h4 class="mb-3">General</h4>
    <table class="table table-bordered">
        <tr>
            <th>Manfaat Pelatihan</th>
            <td>{{ $evaluasi->manfaat_pelatihan ?: '-' }}</td>
        </tr>
        <tr>
            <th>Kinerja Peserta</th>
            <td>
                {{ $evaluasi->kinerja }}
                @if($evaluasi->kinerja == 0) (Tidak sama sekali)
                @elseif($evaluasi->kinerja == 1) (Cukup membantu)
                @elseif($evaluasi->kinerja == 2) (Sangat membantu)
                @endif
            </td>
        </tr>
        <tr>
            <th>Saran</th>
            <td>{{ $evaluasi->saran ?: '-' }}</td>
        </tr>
    </table>

{{-- Approval Actions --}}
<h4 class="mb-3">Tindakan Atasan</h4>
@if(!$evaluasi->is_accepted)
   {{-- Debug info --}}
   <div class="alert alert-info">
       <strong>Debug Info:</strong><br>
       Generated Route: {{ route('evaluasi-level-3.atasan.submitApproval', $evaluasi->id) }}<br>
       Evaluasi ID: {{ $evaluasi->id }}<br>
       Current URL: {{ request()->url() }}<br>
       Is Accepted: {{ $evaluasi->is_accepted ? 'Yes' : 'No' }}
   </div>

   {{-- Check if there are any validation errors --}}
   @if ($errors->any())
       <div class="alert alert-danger">
           <ul class="mb-0">
               @foreach ($errors->all() as $error)
                   <li>{{ $error }}</li>
               @endforeach
           </ul>
       </div>
   @endif

   {{-- Check for flash messages --}}
   @if(session('error'))
       <div class="alert alert-danger">{{ session('error') }}</div>
   @endif
   
   @if(session('success'))
       <div class="alert alert-success">{{ session('success') }}</div>
   @endif

   <form action="{{ route('evaluasi-level-3.atasan.submitApproval', $evaluasi->id) }}" method="POST" class="mb-3" id="approvalForm">
        @csrf

        {{-- Add a hidden field to help debug --}}
        <input type="hidden" name="debug" value="form_submitted">

        {{-- Approve --}}
        <button type="submit" name="status" value="approved" class="btn btn-success" onclick="console.log('Terima button clicked'); console.log('Form action: ', document.getElementById('approvalForm').action);">
            Terima
        </button>

        {{-- Reject --}}
        <button type="button" class="btn btn-danger" data-bs-toggle="collapse" data-bs-target="#rejectReason">Tolak / Rollback</button>

        <div class="collapse mt-2" id="rejectReason">
            <textarea name="rejection_reason" class="form-control mb-2" placeholder="Alasan penolakan / rollback"></textarea>
            <button type="submit" name="status" value="rejected" class="btn btn-danger">Kirim Penolakan</button>
        </div>
    </form>

    {{-- JavaScript debugging --}}
    <script>
        // Log when the page loads
        console.log('Page loaded, form action:', document.getElementById('approvalForm').action);
        
        document.getElementById('approvalForm').addEventListener('submit', function(e) {
            console.log('=== FORM SUBMISSION DEBUG ===');
            console.log('Form action:', this.action);
            console.log('Form method:', this.method);
            
            // Show all form data
            const formData = new FormData(this);
            console.log('Form data:');
            for (let [key, value] of formData.entries()) {
                console.log(`  ${key}: ${value}`);
            }
            
            // Don't prevent the form from submitting, just log
            console.log('Form will now submit...');
        });
    </script>

@else
    <span class="badge bg-success">Sudah Disetujui</span>
@endif

<a href="{{ route('evaluasi-level-3.atasan.index') }}" class="btn btn-secondary mt-3">Kembali</a>
</div>
@endsection
