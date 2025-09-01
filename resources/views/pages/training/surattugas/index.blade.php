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
                        @forelse ($suratTugasList as $surat)
                            @php
                                $user = auth()->user();
                                $canAssign = $user->role === 'admin' || $user->role === 'department_admin';

                                // Use approvals (same as pengajuan page style)
                                $allApprovals = $surat->approvals;

                                $latestRound = $allApprovals->max('round');
                                $ditolakInLatestRound = $allApprovals
                                    ->where('round', $latestRound)
                                    ->contains(fn($step) => $step->status === 'rejected');

                                $approvalNeeded = ($allApprovals->isEmpty() || $ditolakInLatestRound) && !$surat->is_accepted;

                                $latestRound = $allApprovals->max('round');

                                $activeStep = $allApprovals
                                    ->where('round', $latestRound)
                                    ->sortBy('sequence')
                                    ->first(fn($step) => $step->status === 'pending');

                                $currentApproval = ($activeStep && $activeStep->user_id == $user->id) ? $activeStep : null;

                            @endphp
                            <tr>
                                <td>{{ $surat->pelatihan?->kode_pelatihan ?? '-' }}</td>
                                <td>{{ $surat->judul }}</td>
                                <td>
                                    {{ $surat->pelatihan?->tanggal_mulai?->format('d M Y') ?? '-' }}
                                    s.d.
                                    {{ $surat->pelatihan?->tanggal_selesai?->format('d M Y') ?? '-' }}
                                </td>
                                <td>{{ $surat->pelatihan?->durasi ?? '-' }} hari</td>
                                <td>{{ $surat->tempat }}</td>
                                <td>
                                    @if ($surat->is_accepted)
                                        <span class="badge bg-success fw-bold">Selesai</span>
                                    @elseif ($ditolakInLatestRound)
                                        <span class="badge bg-danger fw-bold">Ditolak</span>
                                    @elseif ($approvalNeeded)
                                        <span class="badge bg-secondary fw-bold">Belum Ditugaskan</span>
                                    @else
                                    <span class="badge bg-warning text-dark fw-bold">Dalam Proses</span>
                                    @endif
                                </td>
                                <td>{{ $surat->creator?->name ?? '-' }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('training.surattugas.preview', $surat->id) }}" class="btn btn-sm btn-primary mb-1">
                                        <i class="bx bx-show"></i> Preview
                                    </a>

                                    @if ($surat->is_accepted)
                                        <a href="{{ route('training.surattugas.download', $surat->id) }}" class="btn btn-sm btn-success mb-1">
                                            <i class="bx bx-download"></i> Download
                                        </a>

                                        {{-- Move Lihat Status AFTER Download --}}
                                        <button
                                            class="btn btn-sm btn-outline-secondary mb-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#trackerModal-{{ $surat->id }}">
                                            🔍 Lihat Status
                                        </button>
                                    @else
                                        @if ($approvalNeeded && $canAssign)
                                            <a href="{{ route('training.surattugas.assign.view', $surat->id) }}" class="btn btn-sm btn-info mb-1">
                                                <i class="bx bx-user-plus"></i> Assign
                                            </a>
                                        @endif

                                        @if ($currentApproval)
                                            <div class="btn-group btn-group-sm ms-1" role="group">
                                                <a href="{{ route('training.surattugas.approve.view', [$surat->id, $currentApproval->id]) }}" class="btn btn-success mb-1">
                                                    <i class="bx bx-check"></i> Approve
                                                </a>
                                                <a href="{{ route('training.surattugas.reject.view', [$surat->id, $currentApproval->id]) }}" class="btn btn-danger mb-1">
                                                    <i class="bx bx-x"></i> Reject
                                                </a>
                                            </div>
                                        @endif

                                        {{-- Show Lihat Status after all other actions --}}
                                        <button
                                            class="btn btn-sm btn-outline-secondary mb-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#trackerModal-{{ $surat->id }}">
                                            🔍 Lihat Status
                                        </button>
                                    @endif
                                </td>

                            </tr>

                            {{-- Tracker / History modal (pengajuan style) --}}
                            <div class="modal fade" id="trackerModal-{{ $surat->id }}" tabindex="-1" aria-labelledby="trackerLabel-{{ $surat->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="trackerLabel-{{ $surat->id }}">📋 Riwayat Persetujuan - {{ $surat->pelatihan?->kode_pelatihan ?? $surat->kode_pelatihan }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body">
                                            {{-- summary line using model helper, mirroring pengajuan --}}
                                            @php $summary = $surat->getApprovalStatus(); @endphp
                                            <div class="alert
                                                @if(($summary['status'] ?? '') === 'approved') alert-success
                                                @elseif(($summary['status'] ?? '') === 'rejected') alert-danger
                                                @elseif(($summary['status'] ?? '') === 'in_approval') alert-warning
                                                @else alert-secondary @endif">
                                                {{ $summary['message'] ?? 'Status tidak tersedia' }}
                                                @isset($summary['reason'])
                                                    <br><small>Alasan: {{ $summary['reason'] }}</small>
                                                @endisset
                                            </div>

                                            @if ($allApprovals->isEmpty())
                                                <p class="text-muted">Belum ada riwayat persetujuan.</p>
                                            @else
                                                <ul class="list-group">
                                                    @foreach ($allApprovals->sortBy([['round','asc'],['sequence','asc']]) as $step)
                                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                                            <div class="ms-2 me-auto">
                                                                <div>
                                                                    <strong>
                                                                        {{ ucfirst($step->type) }} -
                                                                        {{ $step->user->name ?? '-' }}
                                                                        ({{ $step->user->registration_id ?? '-' }},
                                                                        {{ $step->user->jabatan_full ?? '-' }})
                                                                    </strong>
                                                                </div>
                                                                <small>Round {{ $step->round }}, Step {{ $step->sequence }}</small><br>

                                                                @if ($step->status === 'approved')
                                                                    <span class="badge bg-success">
                                                                        ✅ Disetujui -
                                                                        {{ $step->signed_at ? \Carbon\Carbon::parse($step->signed_at)->format('d M Y H:i') : ($step->updated_at?->format('d M Y H:i') ?? '-') }}
                                                                    </span>
                                                                @elseif ($step->status === 'rejected')
                                                                    <span class="badge bg-danger">
                                                                        ❌ Ditolak -
                                                                        {{ $step->signed_at ? \Carbon\Carbon::parse($step->signed_at)->format('d M Y H:i') : ($step->updated_at?->format('d M Y H:i') ?? '-') }}
                                                                    </span><br>
                                                                    <small>Alasan: {{ $step->rejection_reason ?? '-' }}</small>
                                                                @else
                                                                    <span class="badge bg-secondary">⏳ Menunggu Tindakan</span>
                                                                @endif
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
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
