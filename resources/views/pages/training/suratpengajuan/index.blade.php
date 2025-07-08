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
                        <th>No. Surat</th>
                        <th>Judul</th>
                        <th>Tanggal</th>
                        <th>Tempat</th>
                        <th>Penyelenggara</th>
                        <th>Status Tanda Tangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($examples as $example)
                        <tr>
                            <td>{{ $example->surat_id ?? '-' }}</td>
                            <td>{{ $example->judul }}</td>
                            <td>{{ $example->tanggal_mulai->format('d M Y') ?? '-' }}</td>
                            <td>{{ $example->tempat }}</td>
                            <td>{{ $example->penyelenggara }}</td>

                            <td>
                                @php
                                    $userSignature = $example->signatures
                                        ->where('user_id', auth()->id())
                                        ->first();
                                @endphp

                                @if ($userSignature)
                                    @if ($userSignature->status === 'pending')
                                        <form action="{{ route('training.suratpengajuan.sign', [$example->id, $userSignature->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-warning" onclick="return confirm('Tandatangani surat ini?')">
                                                Tandatangani ({{ ucfirst($userSignature->role_type) }})
                                            </button>
                                        </form>
                                    @elseif ($userSignature->status === 'accepted')
                                        <span class="badge bg-success">Ditandatangani ({{ ucfirst($userSignature->role_type) }})</span>
                                    @elseif ($userSignature->status === 'rejected')
                                        <span class="badge bg-danger">Ditolak ({{ ucfirst($userSignature->role_type) }})</span>
                                    @endif
                                @else
                                    @php
                                        $statusList = $example->signatures->pluck('status')->unique()->implode(', ');
                                    @endphp
                                    <span class="badge bg-secondary">{{ ucfirst($statusList) }}</span>
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('training.suratpengajuan.preview', $example->id) }}" class="btn btn-sm btn-outline-primary">
                                    Lihat Surat
                                </a>
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
@endsection
