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
    <h4 class="fw-bold">Surat Tugas Pelatihan</h4>

    <div class="card">
        <div class="card-body">
            {{-- Search Bar --}}
            <form method="GET" action="{{ route('training.surattugas.index') }}" class="mb-3">
                <div class="input-group input-group-lg">
                    <input
                        type="text"
                        name="q"
                        id="q"
                        value="{{ request('q') }}"
                        class="form-control"
                        placeholder="Cari judul atau kode pelatihan (contoh: Leadership / KSP-001)">
                    
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bx bx-search"></i> Cari
                    </button>

                    @if(request()->filled('q'))
                        <a href="{{ route('training.surattugas.index') }}" class="btn btn-outline-secondary px-4">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        @php
            // cache the logged in user for reuse in the loop
            $authUser = auth()->user();
        @endphp

        <div class="table-responsive">
            <table class="table table-striped">
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
                            // Current user
                            $user = $authUser;

                            // Determine if this user can assign approval routing
                            $canAssign = false;
                            if ($user->role === 'admin') {
                                $canAssign = true;
                            } else {
                                $isHumanCapitalManager =
                                    optional($user->department)->name === 'Human Capital' &&
                                    strcasecmp(optional($user->jabatan)->name, 'manager') === 0;

                                $isHcFinanceDirector =
                                    optional($user->directorate)->name === 'Human Capital & Finance' &&
                                    strcasecmp(optional($user->jabatan)->name, 'director') === 0;

                                if ($isHumanCapitalManager || $isHcFinanceDirector) {
                                    $canAssign = true;
                                }
                            }

                            // All approval steps
                            $allApprovals = $surat->signaturesAndParafs;

                            // Show Assign if none yet and not accepted
                            $approvalNeeded = $allApprovals->isEmpty() && !$surat->is_accepted;

                            // Sequential gating: which step is active?
                            $activeStep = $allApprovals
                                ->sortBy([
                                    ['round', 'asc'],
                                    ['sequence', 'asc'],
                                ])
                                ->first(function($step) {
                                    return $step->status !== 'approved'; // first non-approved
                                });

                            // Show approval controls only if active step is mine and pending
                            $currentApproval = null;
                            if ($activeStep && $activeStep->status === 'pending' && $activeStep->user_id == $user->id) {
                                $currentApproval = $activeStep;
                            }
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
                                    <span class="badge bg-success">Selesai</span>
                                @elseif ($surat->signaturesAndParafs->where('status', 'rejected')->count())
                                    <span class="badge bg-danger">Ditolak</span>
                                @elseif ($approvalNeeded)
                                    <span class="badge bg-secondary">Belum Ditugaskan</span>
                                @else
                                    <span class="badge bg-warning text-dark">Dalam Proses</span>
                                @endif
                            </td>
                            <td>{{ $surat->creator?->name ?? '-' }}</td>
                            <td class="text-nowrap">
                                {{-- Preview always visible if user can view --}}
                                <a href="{{ route('training.surattugas.preview', $surat->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bx bx-show"></i> Preview
                                </a>

                                {{-- Download if completed --}}
                                @if ($surat->is_accepted)
                                    <a href="{{ route('training.surattugas.download', $surat->id) }}" class="btn btn-sm btn-success">
                                        <i class="bx bx-download"></i> Download
                                    </a>
                                @else
                                    {{-- Assign (only if no approval assigned yet) --}}
                                    @if ($approvalNeeded)
                                        @if ($canAssign)
                                            <a href="{{ route('training.surattugas.assign.view', $surat->id) }}"
                                               class="btn btn-sm btn-info">
                                                <i class="bx bx-user-plus"></i> Assign
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-sm btn-info" onclick="showUnauthorizedAlert()">
                                                <i class="bx bx-user-plus"></i> Assign
                                            </button>
                                        @endif
                                    @endif

                                    {{-- Approve + Reject grouped side-by-side (only if this user is the active pending approver) --}}
                                    @if ($currentApproval)
                                        <div class="btn-group btn-group-sm ms-1" role="group" aria-label="Approval Actions">
                                            <a href="{{ route('training.surattugas.approve.view', [$surat->id, $currentApproval->id]) }}"
                                               class="btn btn-success">
                                                <i class="bx bx-check"></i> Approve
                                            </a>
                                            <a href="{{ route('training.surattugas.reject.view', [$surat->id, $currentApproval->id]) }}"
                                               class="btn btn-danger">
                                                <i class="bx bx-x"></i> Reject
                                            </a>
                                        </div>
                                    @endif
                                @endif
                            </td>
                        </tr>
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
@endsection
