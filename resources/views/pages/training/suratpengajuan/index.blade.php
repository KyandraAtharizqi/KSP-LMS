@extends('layout.main')

@section('title', 'Surat Pengajuan Pelatihan')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showUnauthorizedAlert() {
        Swal.fire({
            icon: 'error',
            title: 'Akses Ditolak',
            text: 'Anda tidak diizinkan untuk melakukan aksi ini.',
            confirmButtonText: 'OK'
        });
    }
</script>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Daftar Surat Pengajuan Pelatihan</h4>

    <div class="card mb-4">
        <div class="card-body">
            {{-- Search Bar --}}
            <form method="GET" action="{{ route('training.suratpengajuan.index') }}" class="mb-3">
                <div class="input-group input-group-lg">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari judul atau kode pelatihan (contoh: Leadership / KSP-001)">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bx bx-search"></i> Cari
                    </button>
                    @if(request()->filled('q'))
                        <a href="{{ route('training.suratpengajuan.index') }}" class="btn btn-outline-secondary px-4">Reset</a>
                    @endif
                </div>
            </form>

            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('training.suratpengajuan.create') }}" class="btn btn-primary">+ Tambah Surat</a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Pelatihan (ID)</th>
                            <th>Judul</th>
                            <th>Tanggal</th>
                            <th>Tempat</th>
                            <th>Penyelenggara</th>
                            <th>Status</th>
                            <th>Dibuat Oleh</th>
                            <th class="text-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($examples as $example)
                            @php
                                $userApproval = $example->approvals
                                    ->where('user_id', auth()->id())
                                    ->whereIn('status', ['menunggu', 'pending'])
                                    ->first();

                                $latestRound = $example->approvals->max('round');
                                $ditolakInLatestRound = $example->approvals
                                    ->where('round', $latestRound)
                                    ->contains(fn($item) => $item->status === 'rejected');

                                $isCreator = auth()->id() === $example->created_by;

                                $minMenungguApproval = $example->approvals
                                    ->where('round', $latestRound)
                                    ->whereIn('status', ['menunggu', 'pending'])
                                    ->sortBy('sequence')
                                    ->first();
                            @endphp
                            <tr>
                                <td>{{ $example->kode_pelatihan }} ({{ $example->id }})</td>
                                <td>{{ $example->judul }}</td>
                                <td>{{ $example->tanggal_mulai?->format('d M Y') ?? '-' }}</td>
                                <td>{{ $example->tempat }}</td>
                                <td>{{ $example->penyelenggara }}</td>
                                <td>
                                    @if ($userApproval)
                                        <span class="badge bg-warning fw-bold">Menunggu {{ ucfirst($userApproval->type) }}</span>
                                    @elseif ($ditolakInLatestRound)
                                        <span class="badge bg-danger fw-bold">DITOLAK</span>
                                    @else
                                        @php
                                            $currentRoundApprovals = $example->approvals->where('round', $latestRound);
                                            $statuses = $currentRoundApprovals->pluck('status')->unique();
                                        @endphp
                                        @foreach ($statuses as $status)
                                            @php $statusLower = strtolower($status); @endphp
                                            @if ($statusLower === 'approved')
                                                <span class="badge bg-success fw-bold me-1">DISETUJUI</span>
                                            @elseif ($statusLower === 'rejected')
                                                <span class="badge bg-danger fw-bold me-1">DITOLAK</span>
                                            @elseif ($statusLower === 'pending' || $statusLower === 'menunggu')
                                                <span class="badge bg-warning text-dark fw-bold me-1">MENUNGGU</span>
                                            @else
                                                <span class="badge bg-secondary fw-bold me-1">{{ strtoupper($status) }}</span>
                                            @endif
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    {{ $example->creator?->name ?? '-' }}
                                </td>
                                <td class="text-nowrap">
                                    {{-- Preview always visible --}}
                                    <a href="{{ route('training.suratpengajuan.preview', $example->id) }}" class="btn btn-sm btn-primary mb-1">
                                        <i class="bx bx-show"></i> Preview
                                    </a>

                                    {{-- Download if fully approved --}}
                                    @php
                                        $latestRoundApprovals = $example->approvals->where('round', $latestRound);
                                    @endphp
                                    @if($latestRoundApprovals->every(fn($a) => $a->status === 'approved'))
                                        <a href="{{ route('surat.pengajuan.download', ['id' => $example->id]) }}" class="btn btn-sm btn-success mb-1">
                                            <i class="bx bx-download"></i> Download
                                        </a>
                                    @endif

                                    {{-- Approval / Reject actions for current user --}}
                                    @if ($userApproval &&
                                        in_array(strtolower($userApproval->status), ['menunggu', 'pending']) &&
                                        $userApproval->round === $latestRound &&
                                        $userApproval->sequence === $minMenungguApproval?->sequence)
                                        <form action="{{ route('training.suratpengajuan.approve', [$example->id, $userApproval->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-success mb-1" onclick="return confirm('Setujui surat ini?')">
                                                {{ $userApproval->type === 'paraf' ? 'Parafkan' : 'Tandatangani' }}
                                            </button>
                                        </form>
                                        <button class="btn btn-sm btn-danger mb-1" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $userApproval->id }}">Tolak</button>
                                    @endif

                                    {{-- Status tracker --}}
                                    <button class="btn btn-sm btn-outline-secondary mb-1" data-bs-toggle="modal" data-bs-target="#trackerModal-{{ $example->id }}">🔍 Lihat Status</button>

                                    {{-- Edit & Resubmit if rejected --}}
                                    @if ($isCreator && $ditolakInLatestRound)
                                        <a href="{{ route('training.suratpengajuan.edit', $example->id) }}" class="btn btn-sm btn-outline-warning mb-1">✏️ Edit & Ajukan Ulang</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center">Belum ada surat pengajuan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



{{-- Approval Tracking Modals --}}
@foreach($examples as $example)
<div class="modal fade" id="trackerModal-{{ $example->id }}" tabindex="-1" aria-labelledby="trackerLabel-{{ $example->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="trackerLabel-{{ $example->id }}">
                    📋 Riwayat Persetujuan - {{ $example->kode_pelatihan }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                @php
                    $approvals = $example->approvals ?? collect();
                    $latestRound = $approvals->max('round') ?? 1;
                    $summary = $example->getApprovalStatus() ?? ['status' => 'pending', 'message' => 'Belum ada status'];
                @endphp

                {{-- Status Summary --}}
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

                {{-- Approval Steps Grouped by Round --}}
                @if ($approvals->isEmpty())
                    <p class="text-muted">Belum ada riwayat persetujuan.</p>
                @else
                    @foreach ($approvals->groupBy('round')->sortKeys() as $round => $steps)
                        <h6 class="mt-3">🌀 Round {{ $round }}</h6>
                        <ul class="list-group mb-3">
                            @foreach ($steps->sortBy('sequence') as $step)
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div>
                                            <strong>
                                                {{ ucfirst($step->type) }} - {{ $step->user->name ?? '-' }}
                                                ({{ $step->user->registration_id ?? '-' }},
                                                {{ $step->user->jabatan_full ?? '-' }})
                                            </strong>
                                        </div>
                                        <small>Step {{ $step->sequence }}</small><br>
                                        @if ($step->status === 'approved')
                                            <span class="badge bg-success">
                                                ✅ Disetujui -
                                                {{ $step->signed_at ? \Carbon\Carbon::parse($step->signed_at)->format('d M Y H:i') : ($step->updated_at ? \Carbon\Carbon::parse($step->updated_at)->format('d M Y H:i') : '-') }}
                                            </span>
                                        @elseif ($step->status === 'rejected')
                                            <span class="badge bg-danger">
                                                ❌ Ditolak -
                                                {{ $step->signed_at ? \Carbon\Carbon::parse($step->signed_at)->format('d M Y H:i') : ($step->updated_at ? \Carbon\Carbon::parse($step->updated_at)->format('d M Y H:i') : '-') }}
                                            </span>
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
@endforeach


{{-- Reject Modals --}}
@foreach($examples as $example)
    @php
        $userApproval = $example->approvals
            ->where('user_id', auth()->id())
            ->whereIn('status', ['menunggu', 'pending'])
            ->first();

        $latestRound = $example->approvals->max('round');
        $minMenungguApproval = $example->approvals
            ->where('round', $latestRound)
            ->whereIn('status', ['menunggu', 'pending'])
            ->sortBy('sequence')
            ->first();
    @endphp
    @if (
        $userApproval &&
        in_array(strtolower($userApproval->status), ['menunggu', 'pending']) &&
        $userApproval->round === $latestRound &&
        $userApproval->sequence === $minMenungguApproval?->sequence
    )
    <div class="modal fade" id="rejectModal-{{ $userApproval->id }}" tabindex="-1" aria-labelledby="rejectLabel-{{ $userApproval->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('training.suratpengajuan.reject', [$example->id, $userApproval->id]) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectLabel-{{ $userApproval->id }}">Tolak Surat - {{ $example->kode_pelatihan }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Alasan Penolakan</label>
                            <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Tolak</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach
@endsection
