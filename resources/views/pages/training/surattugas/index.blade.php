@extends('layout.main')

@section('title', 'Surat Tugas Pelatihan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold">Surat Tugas Pelatihan</h4>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Kode Tugas</th>
                        <th>Kode Pelatihan</th>
                        <th>Judul</th>
                        <th>Tanggal Pelatihan</th>
                        <th>Durasi</th>
                        <th>Tempat</th>
                        <th>Status</th>
                        <th>Dibuat Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suratTugasList as $surat)
                        @php
                            $user = auth()->user();

                            $currentApproval = $surat->signaturesAndParafs
                                ->where('user_id', $user->id)
                                ->where('status', 'pending')
                                ->sortBy('sequence')
                                ->first();

                            $approvalNeeded = $surat->signaturesAndParafs->isEmpty() &&
                                $surat->pelatihan &&
                                $surat->pelatihan->getApprovalStatus()['status'] === 'approved';

                            $canAssign = false;

                            if ($user->role === 'admin') {
                                $canAssign = true;
                            } else {
                                $isHumanCapitalManager = optional($user->department)->nama === 'Human Capital' &&
                                    strtolower(optional($user->jabatan)->nama) === 'manager';

                                $isHcFinanceDirector = optional($user->directorate)->nama === 'Human Capital & Finance' &&
                                    strtolower(optional($user->jabatan)->nama) === 'director';

                                if ($isHumanCapitalManager || $isHcFinanceDirector) {
                                    $canAssign = true;
                                }
                            }
                        @endphp

                        <tr>
                            <td>{{ $surat->kode_tugas }}</td>
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
                            
                            <td>
                                {{-- Tombol Preview selalu muncul --}}
                                <a href="{{ route('training.surattugas.preview', $surat->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bx bx-show"></i> Preview
                                </a>

                                {{-- JIKA SURAT SUDAH SELESAI (is_accepted == true) --}}
                                @if ($surat->is_accepted)
                                    {{-- Maka hanya tampilkan tombol Download --}}
                                    <a href="{{ route('training.surattugas.download', $surat->id) }}" class="btn btn-sm btn-success">
                                        <i class="bx bx-download"></i> Download
                                    </a>
                                @else
                                    {{-- JIKA SURAT MASIH DALAM PROSES --}}

                                    {{-- Tampilkan tombol Assign jika diperlukan --}}
                                    @if ($approvalNeeded)
                                        @if ($canAssign)
                                            <a href="{{ route('training.surattugas.assign.view', $surat->id) }}"
                                            class="btn btn-sm btn-info">
                                                <i class="bx bx-user-plus"></i> Assign
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-info" onclick="alert('You are not authorized to assign.')">
                                                <i class="bx bx-user-plus"></i> Assign
                                            </button>
                                        @endif
                                    @endif

                                    {{-- Tampilkan tombol Approve/Reject untuk user yang berwenang --}}
                                    @if ($currentApproval)
                                        <a href="{{ route('training.surattugas.approve.view', [$surat->id, $currentApproval->id]) }}" class="btn btn-sm btn-success">
                                            <i class="bx bx-check"></i> Approve
                                        </a>
                                        <a href="{{ route('training.surattugas.reject.view', [$surat->id, $currentApproval->id]) }}" class="btn btn-sm btn-danger">
                                            <i class="bx bx-x"></i> Reject
                                        </a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada surat tugas ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
