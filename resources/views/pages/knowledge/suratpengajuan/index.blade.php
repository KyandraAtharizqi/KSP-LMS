@extends('layout.main')

@section('title', 'Surat Pengajuan Knowledge Sharing')

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
    <h4 class="fw-bold mb-4">Daftar Surat Pengajuan Knowledge Sharing</h4>

    <div class="card mb-4">
        <div class="card-body">
            {{-- Search Bar --}}
            <form method="GET" action="{{ route('knowledge.index') }}" class="mb-3">
                <div class="input-group input-group-lg">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari judul atau kode knowledge / kode pelatihan">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bx bx-search"></i> Cari
                    </button>
                    @if(request()->filled('q'))
                        <a href="{{ route('knowledge.index') }}" class="btn btn-outline-secondary px-4">Reset</a>
                    @endif
                </div>
            </form>

            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('knowledge.create') }}" class="btn btn-primary">+ Tambah Knowledge Sharing</a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Knowledge</th>
                            <th>Tipe</th>
                            <th>Judul</th>
                            <th>Kode Pelatihan</th>
                            <th>Tanggal Pelaksanaan</th>
                            <th>Tempat</th>
                            <th>Penyelenggara</th>
                            <th>Status</th>
                            <th>Dibuat Oleh</th>
                            <th class="text-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($knowledges as $knowledge)
                            @php
                                $userApproval = $knowledge->signatures->where('user_id', auth()->id())->whereIn('status', ['pending', 'menunggu'])->first();
                                $latestRound = $knowledge->signatures->max('round') ?? 1;
                                $ditolakInLatestRound = $knowledge->signatures->where('round', $latestRound)->contains(fn($s) => $s->status === 'rejected');
                                $isCreator = auth()->id() === $knowledge->created_by;
                                $minMenungguApproval = $knowledge->signatures->where('round', $latestRound)->whereIn('status', ['pending','menunggu'])->sortBy('sequence')->first();
                            @endphp
                            <tr>
                                <td>{{ $knowledge->kode_knowledge }}</td>
                                <td>{{ ucfirst($knowledge->tipe) }}</td>
                                <td>{{ $knowledge->judul }}</td>
                                <td>{{ $knowledge->kode_pelatihan ?? '-' }}</td>
                                <td>
                                    @if($knowledge->tanggal_pelaksanaan)
                                        {{ implode(', ', array_map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m/Y'), $knowledge->tanggal_pelaksanaan)) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $knowledge->tempat ?? '-' }}</td>
                                <td>{{ $knowledge->penyelenggara ?? '-' }}</td>
                                <td>
                                    @if($userApproval)
                                        <span class="badge bg-warning fw-bold">Menunggu</span>
                                    @elseif($ditolakInLatestRound)
                                        <span class="badge bg-danger fw-bold">DITOLAK</span>
                                    @else
                                        <span class="badge bg-success fw-bold">DISETUJUI</span>
                                    @endif
                                </td>
                                <td>{{ $knowledge->creator?->name ?? '-' }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('knowledge.show', $knowledge->id) }}" class="btn btn-sm btn-primary mb-1">
                                        <i class="bx bx-show"></i> Preview
                                    </a>
                                    <a href="{{ route('knowledge.edit', $knowledge->id) }}" class="btn btn-sm btn-warning mb-1">Edit</a>
                                    <form action="{{ route('knowledge.destroy', $knowledge->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Yakin ingin menghapus?')">Delete</button>
                                    </form>

                                    {{-- Approval Tracker Modal --}}
                                    <button class="btn btn-sm btn-outline-secondary mb-1" data-bs-toggle="modal" data-bs-target="#trackerModal-{{ $knowledge->id }}">🔍 Lihat Status</button>

                                    {{-- Resubmit if rejected --}}
                                    @if ($isCreator && $ditolakInLatestRound)
                                        <a href="{{ route('knowledge.edit', $knowledge->id) }}" class="btn btn-sm btn-outline-warning mb-1">✏️ Edit & Ajukan Ulang</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center">Belum ada Knowledge Sharing.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Approval Tracker Modals --}}
@foreach($knowledges as $knowledge)
<div class="modal fade" id="trackerModal-{{ $knowledge->id }}" tabindex="-1" aria-labelledby="trackerLabel-{{ $knowledge->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="trackerLabel-{{ $knowledge->id }}">
                    📋 Riwayat Persetujuan - {{ $knowledge->kode_knowledge }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                @php
                    $steps = $knowledge->parafs->concat($knowledge->signatures)->sortBy(['round','sequence']);
                @endphp
                @if($steps->isEmpty())
                    <p class="text-muted">Belum ada riwayat persetujuan.</p>
                @else
                    @foreach($steps->groupBy('round') as $round => $roundSteps)
                        <h6 class="mt-3">🌀 Round {{ $round }}</h6>
                        <ul class="list-group mb-3">
                            @foreach($roundSteps as $step)
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <strong>{{ ucfirst($step->type) }} - {{ $step->user?->name ?? '-' }} ({{ $step->user?->registration_id ?? '-' }})</strong>
                                        <br><small>Step {{ $step->sequence }}</small><br>
                                        @if($step->status === 'approved')
                                            <span class="badge bg-success">✅ Disetujui - {{ $step->signed_at?->format('d M Y H:i') ?? '-' }}</span>
                                        @elseif($step->status === 'rejected')
                                            <span class="badge bg-danger">❌ Ditolak - {{ $step->signed_at?->format('d M Y H:i') ?? '-' }}</span>
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

@endsection
