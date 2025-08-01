@extends('layout.main')

@section('content')
<div class="page-heading"><h3>Daftar Surat Pengajuan Pelatihan</h3></div>

<div class="page-content">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <h5>Surat Pengajuan</h5>
                <a href="{{ route('training.suratpengajuan.create') }}" class="btn btn-primary">+ Tambah Surat</a>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kode Pelatihan</th>
                        <th>Judul</th>
                        <th>Tanggal</th>
                        <th>Tempat</th>
                        <th>Penyelenggara</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($examples as $example)
                        @php
                            $userApproval = $example->approvals
                                ->where('user_id', auth()->id())
                                ->where('status', 'menunggu')
                                ->first();

                            $latestRound = $example->approvals->max('round');
                            $ditolakInLatestRound = $example->approvals
                                ->where('round', $latestRound)
                                ->contains(fn($item) => $item->status === 'ditolak');

                            $isCreator = auth()->id() === $example->created_by;

                            $minMenungguApproval = $example->approvals
                                ->where('round', $latestRound)
                                ->where('status', 'menunggu')
                                ->sortBy('sequence')
                                ->first();
                        @endphp
                        <tr>
                            <td>{{ $example->kode_pelatihan }}</td>
                            <td>{{ $example->judul }}</td>
                            <td>{{ $example->tanggal_mulai?->format('d M Y') ?? '-' }}</td>
                            <td>{{ $example->tempat }}</td>
                            <td>{{ $example->penyelenggara }}</td>
                            <td>
                                @if ($userApproval)
                                    <span class="badge bg-warning">Menunggu {{ ucfirst($userApproval->type) }}</span>
                                @elseif ($ditolakInLatestRound)
                                    <span class="badge bg-danger">Ditolak</span>
                                @else
                                    @php
                                        $statuses = $example->approvals->pluck('status')->unique();
                                    @endphp
                                    @foreach ($statuses as $status)
                                        @php $statusLower = strtolower($status); @endphp

                                        @if ($statusLower === 'approved')
                                            <span class="badge bg-success me-1">DISETUJUI</span>
                                        @elseif ($statusLower === 'rejected')
                                            <span class="badge bg-danger me-1">DITOLAK</span>
                                        @elseif ($statusLower === 'pending')
                                            <span class="badge bg-warning text-dark me-1">MENUNGGU</span>
                                        @else
                                            <span class="badge bg-secondary me-1">{{ strtoupper($status) }}</span>
                                        @endif
                                    @endforeach

                                @endif
                            </td>

                            <td>
                                <a href="{{ route('training.suratpengajuan.preview', $example->id) }}" class="btn btn-sm btn-outline-primary mb-1">
                                    Lihat Surat
                                </a>

                                <a href="{{ route('surat.pengajuan.download', ['id' => $example->id]) }}" class="btn btn-sm btn-info mb-1">
                                    Unduh PDF
                                </a>

                                <button class="btn btn-sm btn-outline-secondary mb-1" data-bs-toggle="modal" data-bs-target="#trackerModal-{{ $example->id }}">
                                    🔍 Lihat Status
                                </button>

                                @if (
                                    $userApproval &&
                                    $userApproval->status === 'menunggu' &&
                                    $userApproval->round === $latestRound &&
                                    $userApproval->id === $minMenungguApproval?->id
                                )
                                    <form action="{{ route('training.suratpengajuan.approve', [$example->id, $userApproval->id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-success mb-1" onclick="return confirm('Setujui surat ini?')">
                                            {{ $userApproval->type === 'paraf' ? 'Parafkan' : 'Tandatangani' }}
                                        </button>
                                    </form>

                                    <button class="btn btn-sm btn-danger mb-1" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $userApproval->id }}">
                                        Tolak
                                    </button>
                                @endif

                                @if ($isCreator && $ditolakInLatestRound)
                                    <a href="{{ route('training.suratpengajuan.edit', $example->id) }}" class="btn btn-sm btn-outline-warning mb-1">
                                        ✏️ Edit & Ajukan Ulang
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">Belum ada surat pengajuan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Approval Tracking Modals --}}
@foreach($examples as $example)
<div class="modal fade" id="trackerModal-{{ $example->id }}" tabindex="-1" aria-labelledby="trackerLabel-{{ $example->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="trackerLabel-{{ $example->id }}">📋 Riwayat Persetujuan - {{ $example->kode_pelatihan }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    @foreach ($example->approvals->sortBy(['round', 'sequence']) as $step)
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div>
                                    <strong>
                                        {{ ucfirst($step->type) }} - 
                                        {{ $step->user->name }} 
                                        ({{ $step->user->registration_id ?? '-' }},
                                        {{ $step->user->jabatan_full ?? '-' }})
                                    </strong>
                                </div>
                                <small>Round {{ $step->round }}, Step {{ $step->sequence }}</small><br>
                                @if ($step->status === 'disetujui')
                                    <span class="badge bg-success">
                                        ✅ Disetujui -
                                        {{ $step->signed_at ? \Carbon\Carbon::parse($step->signed_at)->format('d M Y H:i') : '-' }}
                                    </span>
                                @elseif ($step->status === 'ditolak')
                                    <span class="badge bg-danger">
                                        ❌ Ditolak -
                                        {{ $step->signed_at ? \Carbon\Carbon::parse($step->signed_at)->format('d M Y H:i') : '-' }}
                                    </span><br>
                                    <small>Alasan: {{ $step->rejection_reason }}</small>
                                @else
                                    <span class="badge bg-secondary">⏳ Menunggu Tindakan</span>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
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
            ->where('status', 'menunggu')
            ->first();

        $latestRound = $example->approvals->max('round');
        $minMenungguApproval = $example->approvals
            ->where('round', $latestRound)
            ->where('status', 'menunggu')
            ->sortBy('sequence')
            ->first();
    @endphp
    @if (
        $userApproval &&
        $userApproval->status === 'menunggu' &&
        $userApproval->round === $latestRound &&
        $userApproval->id === $minMenungguApproval?->id
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
