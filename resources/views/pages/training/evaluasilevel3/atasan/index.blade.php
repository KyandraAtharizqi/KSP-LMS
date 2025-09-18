@extends('layout.main')

@section('content')
<div class="container">
    <h4 class="mb-4">Evaluasi Level 3 - Atasan</h4>

    <div class="card">
        <div class="card-header">
            <h5>Daftar Evaluasi Peserta</h5>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode Pelatihan</th>
                        <th>Judul Pelatihan</th>
                        <th>Peserta</th>
                        <th>Status Persetujuan</th>
                        <th>Status Evaluasi Atasan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($evaluasis as $evaluasi)
                        @php
                            $atasanEvaluasi = $evaluasi->atasanEvaluation;
                        @endphp
                        <tr>
                            <td>{{ $evaluasi->pelatihan->kode_pelatihan }}</td>
                            <td>{{ $evaluasi->pelatihan->judul }}</td>
                            <td>{{ $evaluasi->user->name }}</td>

                            {{-- Approval Status of Peserta --}}
                            <td>
                                @if($evaluasi->is_submitted)
                                    @if($evaluasi->is_accepted == 0)
                                        <span class="badge bg-warning">Menunggu Persetujuan</span>
                                    @elseif($evaluasi->is_accepted == 1)
                                        <span class="badge bg-success">Disetujui</span>
                                    @else
                                        <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">Belum Diisi</span>
                                @endif
                            </td>

                            {{-- Supervisor Evaluation Status --}}
                            <td>
                                @if($atasanEvaluasi && $atasanEvaluasi->is_submitted)
                                    <span class="badge bg-success">Sudah Diisi</span>
                                @elseif($atasanEvaluasi)
                                    <span class="badge bg-warning">Belum Diisi</span>
                                @else
                                    <span class="badge bg-secondary">Belum tersedia</span>
                                @endif
                            </td>

                            {{-- Action buttons --}}
                            <td class="text-center">
                                @if($evaluasi->is_submitted && $evaluasi->is_accepted == 0)
                                    <a href="{{ route('evaluasi-level-3.atasan.approval', $evaluasi->id) }}" class="btn btn-info btn-sm">
                                        Review & Approve
                                    </a>
                                @elseif($evaluasi->is_accepted == 1)
                                    @if($atasanEvaluasi && !$atasanEvaluasi->is_submitted)
                                        <a href="{{ route('evaluasi-level-3.atasan.create', $evaluasi->id) }}" class="btn btn-primary btn-sm">
                                            Isi Evaluasi
                                        </a>
                                    @elseif($atasanEvaluasi && $atasanEvaluasi->is_submitted)
                                        <a href="{{ route('evaluasi-level-3.atasan.preview', $atasanEvaluasi->id) }}" class="btn btn-sm btn-outline-secondary mb-1"><i class="bx bx-show"></i> Preview</a>
                                        <a href="{{ route('evaluasi-level-3.atasan.downloadPdf', $atasanEvaluasi->id) }}" class="btn btn-sm btn-success mb-1" target="_blank"><i class="bx bx-download"></i> PDF</a>
                                    @else
                                        <span class="text-muted">Belum tersedia</span>
                                    @endif
                                @else
                                    <span class="text-muted">Tidak Bisa Diisi</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Tidak ada evaluasi peserta tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
