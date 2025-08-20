@extends('layout.main')

@section('title', 'Detail Pengajuan Knowledge Sharing')

@section('content')
<div class="page-heading">
    <h3>Detail Pengajuan</h3>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-header text-center">
            <h4 class="mb-1 fw-bold">Pengajuan {{ $pengajuan->perihal }}</h4>
        </div>

        <div class="card-body">
            @if($pengajuan->status === 'approved')
                <div class="alert alert-success mb-3">
                    <i class="bi bi-check-circle"></i> Pengajuan ini telah disetujui
                </div>
            @elseif($pengajuan->status === 'rejected')
                <div class="alert alert-danger mb-3">
                    <i class="bi bi-x-circle"></i> Pengajuan ini ditolak
                    @if($pengajuan->rejection_reason)
                        <br>
                        <small>Alasan: {{ $pengajuan->rejection_reason }}</small>
                    @endif
                </div>
            @elseif($pengajuan->status === 'pending')
                <div class="alert alert-warning mb-3">
                    <i class="bi bi-clock"></i> Menunggu persetujuan dari {{ $pengajuan->kepada }}
                </div>
            @endif
            <div class="row mb-2">
                <div class="col-sm-3"><strong>Kepada Yth</strong></div>
                <div class="col-sm-9">: {{ $pengajuan->kepada }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-3"><strong>Dari</strong></div>
                <div class="col-sm-9">: {{ $pengajuan->dari }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-3"><strong>Tanggal</strong></div>
                <div class="col-sm-9">
                    : {{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->format('d M Y') }}
                    -
                    {{ \Carbon\Carbon::parse($pengajuan->tanggal_selesai)->format('d M Y') }}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-3"><strong>Waktu</strong></div>
                <div class="col-sm-9">
                    : {{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->format('H:i') }}
                    -
                    {{ \Carbon\Carbon::parse($pengajuan->tanggal_selesai)->format('H:i') }} WIB
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-3"><strong>Lampiran</strong></div>
                <div class="col-sm-9">:
                    @if ($pengajuan->lampiran)
                        <a href="{{ asset('storage/' . $pengajuan->lampiran) }}" target="_blank">Lihat Lampiran (PDF)</a>
                    @else
                        <span class="text-muted">Tidak ada lampiran</span>
                    @endif
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-sm-3"><strong>Peserta</strong></div>
                <div class="col-sm-9">
                    @if(!empty($pengajuan->pesertaList))
                        <ul class="mb-0">
                            @foreach($pengajuan->pesertaList as $ps)
                                <li>{{ $ps }}</li>
                            @endforeach
                        </ul>
                    @else
                        <span class="text-muted">Tidak ada peserta</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-footer">
            <div class="row align-items-center">
                <div class="col">
                    <span class="fw-bold">Status: </span>
                    @if($pengajuan->status === 'pending')
                        <span class="badge bg-warning">Menunggu Persetujuan</span>
                    @elseif($pengajuan->status === 'approved')
                        <span class="badge bg-success">Disetujui</span>
                    @elseif($pengajuan->status === 'rejected')
                        <span class="badge bg-danger">Ditolak</span>
                        @if($pengajuan->rejection_reason)
                            <br>
                            <small class="text-muted">Alasan: {{ $pengajuan->rejection_reason }}</small>
                        @endif
                    @endif
                </div>
                <div class="col text-end">
                    @if(auth()->user()->name === $pengajuan->kepada && $pengajuan->status === 'pending')
                        <form action="{{ route('knowledge.pengajuan.approve', $pengajuan->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Setujui
                            </button>
                        </form>
                        <form action="{{ route('knowledge.pengajuan.reject', $pengajuan->id) }}" method="POST" class="d-inline ms-2" id="rejectForm">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="rejection_reason" id="rejection_reason">
                            <button type="button" class="btn btn-danger" onclick="showRejectModal()">
                                <i class="bi bi-x-circle"></i> Tolak
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('knowledge.pengajuan.index') }}" class="btn btn-secondary ms-2">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tolak -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Alasan Penolakan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="reject_reason">Mohon berikan alasan penolakan:</label>
                    <textarea class="form-control" id="reject_reason" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="submitReject()">Kirim</button>
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
    function showRejectModal() {
        let modal = new bootstrap.Modal(document.getElementById('rejectModal'));
        modal.show();
    }

    function submitReject() {
        let reason = document.getElementById('reject_reason').value;
        if (!reason) {
            alert('Mohon isi alasan penolakan');
            return;
        }
        document.getElementById('rejection_reason').value = reason;
        document.getElementById('rejectForm').submit();
    }
</script>
@endpush

@endsection
