@extends('layout.main')

@section('title', 'Surat Tugas Pelatihan')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showUnauthorizedAlert() {
        Swal.fire({
            icon: 'error',
            title: 'Akses Ditolak',
            text: 'Anda tidak diizinkan untuk melakukan assign pada surat ini.',
            confirmButtonText: 'OK'
        });
    }
</script>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Daftar Surat Tugas Pelatihan</h4>

    <div class="card mb-4">
        <div class="card-body">
            {{-- Search Bar --}}
            <form method="GET" action="{{ route('training.surattugas.index') }}" class="mb-3">
                <div class="input-group input-group-lg">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari judul atau kode pelatihan (contoh: Leadership / KSP-001)">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bx bx-search"></i> Cari
                    </button>
                    @if(request()->filled('q'))
                        <a href="{{ route('training.surattugas.index') }}" class="btn btn-outline-secondary px-4">Reset</a>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Pelatihan</th>
                            <th>Judul</th>
                            <th>Tanggal Pelatihan</th>
                            <th>Durasi</th>
                            <th>Tempat</th>
                            <th>Status</th>
                            <th>Dibuat Oleh</th>
                            <th class="text-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($suratList as $surat)
                            @php
                                $user = auth()->user();
                                $canAssign = $user->role === 'admin' || $user->role === 'department_admin';

                                // detect model type
                                $isTugas = $surat instanceof \App\Models\SuratTugasPelatihan;
                                $tugas = $isTugas ? $surat : $surat->suratTugas;
                                $pengajuan = $isTugas ? $surat->pelatihan : $surat;

                                $allApprovals = $tugas?->signaturesAndParafs ?? collect();
                                $latestRound = $allApprovals->max('round') ?? 1;
                                $latestApprovals = $allApprovals->where('round', $latestRound);

                                // approvalNeeded now only cares about the latest round
                                $approvalNeeded = !$tugas || $latestApprovals->where('status', 'rejected')->isNotEmpty();

                                // active step for this user (latest round only)
                                $activeStep = $latestApprovals
                                    ->sortBy('sequence')
                                    ->first(fn($step) => $step->status === 'pending');

                                $currentApproval = ($activeStep && $activeStep->user_id == $user->id) ? $activeStep : null;
                            @endphp

                            <tr>
                                <td>{{ $pengajuan->kode_pelatihan }} ({{ $pengajuan->id }})</td>
                                <td>{{ $pengajuan->judul }}</td>
                                <td>
                                    @if ($tugas)
                                        {{ $tugas->tanggal_mulai?->format('d M Y') ?? '-' }}
                                        s.d
                                        {{ $tugas->tanggal_selesai?->format('d M Y') ?? '-' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $tugas?->durasi ?? '-' }}</td>
                                <td>{{ $tugas?->tempat ?? '-' }}</td>
                                <td>
                                    @if (!$tugas)
                                        <span class="badge bg-secondary fw-bold">Belum Ditugaskan</span>
                                    @elseif ($tugas->is_accepted)
                                        <span class="badge bg-success fw-bold">Selesai</span>
                                    @elseif ($latestApprovals->where('status','rejected')->isNotEmpty())
                                        <span class="badge bg-danger fw-bold">Ditolak</span>
                                    @elseif ($approvalNeeded)
                                        <span class="badge bg-secondary fw-bold">Belum Ditugaskan</span>
                                    @else
                                        <span class="badge bg-warning text-dark fw-bold">Dalam Proses</span>
                                    @endif
                                </td>
                                <td>{{ $pengajuan->creator?->name ?? '-' }}</td>
                                <td class="text-nowrap">
                                    {{-- Preview --}}
                                    <a href="{{ route('training.surattugas.preview', $tugas->id) }}" class="btn btn-sm btn-primary mb-1">
                                        <i class="bx bx-show"></i> Preview
                                    </a>

                                    {{-- Download --}}
                                    @if ($tugas && $tugas->is_accepted)
                                        <a href="{{ route('training.surattugas.download', $pengajuan->id) }}" class="btn btn-sm btn-success mb-1">
                                            <i class="bx bx-download"></i> Download
                                        </a>
                                    @endif

                                    {{-- Assign --}}
                                    @if ($approvalNeeded && $canAssign)
                                        <a href="{{ route('training.surattugas.assign.view', $pengajuan->id) }}" class="btn btn-sm btn-info mb-1">
                                            <i class="bx bx-user-plus"></i> Assign
                                        </a>
                                    @endif

                                    {{-- Approve / Reject --}}
                                    {{-- Approve / Reject --}}
                                    @if ($currentApproval)
                                        <div class="btn-group btn-group-sm mb-1" role="group">
                                            <a href="{{ route('training.surattugas.approve.view', [$tugas->id, $currentApproval->id]) }}" class="btn btn-success">
                                                <i class="bx bx-check"></i> Approve
                                            </a>
                                            <a href="{{ route('training.surattugas.reject.view', [$tugas->id, $currentApproval->id]) }}" class="btn btn-danger">
                                                <i class="bx bx-x"></i> Reject
                                            </a>
                                        </div>
                                    @endif


                                    {{-- Tracker / History --}}
                                    <button
                                        class="btn btn-sm btn-outline-secondary mb-1"
                                        data-bs-toggle="modal"
                                        data-bs-target="#trackerModal-{{ $pengajuan->id }}">
                                        🔍 Lihat Status
                                    </button>
                                </td>
                            </tr>

                            {{-- Tracker modal --}}
                            <div class="modal fade" id="trackerModal-{{ $pengajuan->id }}" tabindex="-1" aria-labelledby="trackerLabel-{{ $pengajuan->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="trackerLabel-{{ $pengajuan->id }}">
                                                📋 Riwayat Persetujuan - {{ $pengajuan->kode_pelatihan }} ({{ $pengajuan->id }})
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body">
                                            @php 
                                                $summary = [];
                                                if ($tugas) {
                                                    $latestRound = $allApprovals->max('round') ?? 1;
                                                    $currentRoundSteps = $allApprovals->where('round', $latestRound);

                                                    if ($currentRoundSteps->where('status', 'rejected')->isNotEmpty()) {
                                                        $rejected = $currentRoundSteps->firstWhere('status', 'rejected');
                                                        $summary = [
                                                            'status' => 'rejected',
                                                            'message' => "❌ Ditolak oleh {$rejected->user->name}",
                                                            'reason'  => $rejected->rejection_reason ?? '-',
                                                        ];
                                                    } elseif ($currentRoundSteps->where('status','pending')->isNotEmpty()) {
                                                        $next = $currentRoundSteps->where('status','pending')->sortBy('sequence')->first();
                                                        $summary = [
                                                            'status' => 'in_approval',
                                                            'message' => "⏳ Menunggu persetujuan dari {$next->user->name}",
                                                        ];
                                                    } else {
                                                        $summary = [
                                                            'status' => 'approved',
                                                            'message' => '✅ Disetujui sepenuhnya',
                                                        ];
                                                    }
                                                }
                                            @endphp
                                            <div class="alert
                                                @if(($summary['status'] ?? '') === 'approved') alert-success
                                                @elseif(($summary['status'] ?? '') === 'rejected') alert-danger
                                                @elseif(($summary['status'] ?? '') === 'in_approval') alert-warning
                                                @else alert-secondary @endif">
                                                {{ $summary['message'] ?? 'Belum ada status' }}
                                                @isset($summary['reason'])
                                                    <br><small>Alasan: {{ $summary['reason'] }}</small>
                                                @endisset
                                            </div>

                                            @if ($allApprovals->isEmpty())
                                                <p class="text-muted">Belum ada riwayat persetujuan.</p>
                                            @else
                                                @foreach ($allApprovals->groupBy('round') as $round => $steps)
                                                    <h6 class="mt-3">Round {{ $round }}</h6>
                                                    <ul class="list-group mb-3">
                                                        @foreach ($steps->sortBy('sequence') as $step)
                                                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                                                <div class="ms-2 me-auto">
                                                                    <div>
                                                                        <strong>
                                                                            {{ ucfirst($step->type) }} - {{ $step->user->name ?? '-' }}
                                                                            ({{ $step->user->registration_id ?? '-' }}, {{ $step->user->jabatan_full ?? '-' }})
                                                                        </strong>
                                                                    </div>
                                                                    <small>Step {{ $step->sequence }}</small><br>
                                                                    @if ($step->status === 'approved')
                                                                        <span class="badge bg-success">
                                                                            ✅ Disetujui - {{ $step->signed_at?->format('d M Y H:i') ?? ($step->updated_at?->format('d M Y H:i') ?? '-') }}
                                                                        </span>
                                                                    @elseif ($step->status === 'rejected')
                                                                        <span class="badge bg-danger">
                                                                            ❌ Ditolak - {{ $step->signed_at?->format('d M Y H:i') ?? ($step->updated_at?->format('d M Y H:i') ?? '-') }}
                                                                        </span>
                                                                        <br><small>Alasan: {{ $step->rejection_reason ?? '-' }}</small>
                                                                    @else
                                                                        <span class="badge bg-secondary">⏳ Menunggu Tindakan</span>
                                                                    @endif
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endforeach
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada surat tugas ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
