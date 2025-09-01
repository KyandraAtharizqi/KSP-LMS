@extends('layout.main')

@section('title', 'Evaluasi Pelatihan Level 1')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Daftar Evaluasi Level 1</h4>

    <div class="card mb-4">
        <div class="card-body">
            {{-- Search Bar --}}
            <form method="GET" action="{{ route('training.evaluasilevel1.index') }}" class="mb-3">
                <div class="input-group input-group-lg">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                        placeholder="Cari judul atau kode pelatihan (contoh: Leadership / KSP-001)">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bx bx-search"></i> Cari
                    </button>
                    @if(request()->filled('q'))
                        <a href="{{ route('training.evaluasilevel1.index') }}" class="btn btn-outline-secondary px-4">Reset</a>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Pelatihan</th>
                            <th>Nama Pelatihan</th>
                            <th>Tanggal Pelaksanaan</th>
                            <th>Tempat</th>
                            <th>Penyelenggara</th>
                            <th>Peserta</th>
                            <th>Atasan</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $user = auth()->user();
                            $isAdmin = in_array($user->role, ['admin', 'department_admin']);
                        @endphp

                        @forelse ($pelatihans as $pelatihan)
                            @if ($isAdmin)
                                {{-- For admin: loop every evaluation record that exists --}}
                                @foreach ($pelatihan->evaluasiLevel1 as $evaluasi)
                                    <tr>
                                        <td><strong>{{ $pelatihan->kode_pelatihan }}</strong></td>
                                        <td>{{ $pelatihan->judul }}</td>
                                        <td>
                                            {{ $pelatihan->tanggal_mulai->format('d M Y') }} -
                                            {{ $pelatihan->tanggal_selesai->format('d M Y') }}
                                        </td>
                                        <td>{{ $pelatihan->tempat }}</td>
                                        <td>{{ $pelatihan->penyelenggara }}</td>
                                        <td>{{ $evaluasi->user->name ?? '-' }}</td>

                                        {{-- ✅ Superior dropdown (admin only) --}}
                                        <td>
                                            <form action="{{ route('training.evaluasilevel1.updateSuperior', $evaluasi->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <select name="superior_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                                    <option value="">-- Pilih Atasan --</option>
                                                    @foreach ($availableSuperiors as $superior)
                                                        <option value="{{ $superior->id }}"
                                                            {{ $evaluasi->superior_id == $superior->id ? 'selected' : '' }}>
                                                            {{ $superior->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        </td>

                                        <td class="text-center">
                                            @if (!$evaluasi->is_submitted)
                                                <span class="badge bg-warning text-dark fw-bold">Belum Terkirim</span>
                                            @else
                                                <span class="badge bg-success fw-bold">Terkirim</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($evaluasi->is_submitted)
                                                <a href="{{ route('training.evaluasilevel1.show', $pelatihan->id) }}" class="btn btn-sm btn-outline-secondary mb-1">
                                                    <i class="bx bx-show"></i> Lihat
                                                </a>
                                                <a href="{{ route('training.evaluasilevel1.pdf', $pelatihan->id) }}" class="btn btn-sm btn-success mb-1">
                                                    <i class="bx bx-download"></i> PDF
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                {{-- For participants: show only their evaluation --}}
                                @php
                                    $evaluasi = $pelatihan->evaluasiLevel1->firstWhere('user_id', $user->id);
                                    $isParticipant = $pelatihan->participants->contains('user_id', $user->id);
                                @endphp

                                @if ($evaluasi || $isParticipant)
                                    <tr>
                                        <td><strong>{{ $pelatihan->kode_pelatihan }}</strong></td>
                                        <td>{{ $pelatihan->judul }}</td>
                                        <td>
                                            {{ $pelatihan->tanggal_mulai->format('d M Y') }} -
                                            {{ $pelatihan->tanggal_selesai->format('d M Y') }}
                                        </td>
                                        <td>{{ $pelatihan->tempat }}</td>
                                        <td>{{ $pelatihan->penyelenggara }}</td>
                                        <td>{{ $evaluasi?->user->name ?? $user->name }}</td>
                                        <td>{{ $evaluasi?->superior?->name ?? '-' }}</td>
                                        <td class="text-center">
                                            @if ($evaluasi)
                                                @if (!$evaluasi->is_submitted && $evaluasi->user_id === $user->id)
                                                    <span class="badge bg-warning text-dark fw-bold">Belum Selesai</span>
                                                @elseif ($evaluasi->is_submitted)
                                                    <span class="badge bg-success fw-bold">Terkirim</span>
                                                @endif
                                            @elseif ($isParticipant)
                                                <span class="badge bg-info fw-bold">Perlu Diisi</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($evaluasi)
                                                @if (!$evaluasi->is_submitted && $evaluasi->user_id === $user->id)
                                                    <a href="{{ route('training.evaluasilevel1.create', $pelatihan->id) }}" class="btn btn-sm btn-primary">
                                                        Isi Evaluasi
                                                    </a>
                                                @elseif ($evaluasi->is_submitted)
                                                    <a href="{{ route('training.evaluasilevel1.show', $pelatihan->id) }}" class="btn btn-sm btn-outline-secondary mb-1">
                                                        <i class="bx bx-show"></i> Lihat
                                                    </a>
                                                    <a href="{{ route('training.evaluasilevel1.pdf', $pelatihan->id) }}" class="btn btn-sm btn-success mb-1">
                                                        <i class="bx bx-download"></i> PDF
                                                    </a>
                                                @endif
                                            @elseif ($isParticipant)
                                                <a href="{{ route('training.evaluasilevel1.create', $pelatihan->id) }}" class="btn btn-sm btn-primary">
                                                    Isi Evaluasi
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endif
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada evaluasi tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
