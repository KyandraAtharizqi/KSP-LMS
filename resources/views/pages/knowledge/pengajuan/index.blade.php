@extends('layout.main')

@section('content')
<div class="page-heading"><h3>Pengajuan Knowledge Sharing</h3></div>

<div class="page-content">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Pengajuan</h5>
            <a href="{{ route('knowledge.pengajuan.create') }}" class="btn btn-primary btn-sm">
                + Tambah Pengajuan
            </a>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Perihal</th>
                        <th>Pemateri</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuan as $item)
                        <tr>
                            <td>{{ $item->perihal }}</td>
                            <td>{{ $item->pemateri }}</td>
                            <td>
                                <div class="row mb-2">
                                    <div class="col-sm-9">
                                        {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}
                                        -
                                        {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($item->status == 'pending')
                                    <span class="badge bg-warning">Menunggu</span>
                                @elseif($item->status == 'approved')
                                    <span class="badge bg-success">Disetujui</span>
                                @elseif($item->status == 'rejected')
                                    <span class="badge bg-danger">Ditolak</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('knowledge.pengajuan.preview', $item->id) }}" class="btn btn-sm btn-info me-1 my-2">Detail</a>

                                {{-- Tombol Edit hanya untuk pembuat pengajuan --}}
                                @if($item->created_by == Auth::id() && $item->status == 'pending')
                                    <a href="{{ route('knowledge.pengajuan.edit', $item->id) }}" class="btn btn-sm btn-warning my-1">Edit</a>
                                @endif

                                {{-- Tombol Approve/Reject untuk kepada (atasan) --}}
                                @if(auth()->user()->name === $item->kepada && $item->status === 'pending')
                                    <form action="{{ route('knowledge.pengajuan.approve', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success my-1 me-1">Setujui</button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-danger my-1" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $item->id }}">
                                        Tolak
                                    </button>
                                @endif

                                {{-- Tombol Delete hanya untuk pembuat pengajuan --}}
                                @if($item->created_by == Auth::id() && $item->status == 'pending')
                                    <form action="{{ route('knowledge.pengajuan.destroy', $item->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus pengajuan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada pengajuan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal untuk Penolakan --}}
@foreach($pengajuan as $item)
<div class="modal fade" id="rejectModal-{{ $item->id }}" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rejectModalLabel">Alasan Penolakan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('knowledge.pengajuan.reject', $item->id) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="modal-body">
          <div class="mb-3">
            <label for="rejection_reason" class="form-label">Tuliskan alasan penolakan:</label>
            <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Tolak Pengajuan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach

@endsection