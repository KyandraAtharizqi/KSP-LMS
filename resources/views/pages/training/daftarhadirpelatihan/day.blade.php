@php
    use Illuminate\Support\Facades\Storage;
@endphp

@extends('layout.main')

@section('title', 'Daftar Hadir - ' . $pelatihan->judul . ' (' . $date->format('d M Y') . ')')

@section('content')
<div class="page-heading">
    <h3>Daftar Hadir - {{ $pelatihan->judul }} ({{ $date->format('d M Y') }})</h3>
</div>

<div class="page-content">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('training.daftarhadirpelatihan.show', $pelatihan->id) }}" class="btn btn-secondary btn-sm">
            &larr; Kembali ke Daftar Hari
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Import & Export Section -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Import / Export Daftar Hadir</h5>
        </div>
        <div class="card-body">
            @if(!isset($status) || !$status->is_submitted)
                <!-- Import Form (only if not submitted) -->
                <form action="{{ route('training.daftarhadirpelatihan.import', [$pelatihan->id, $date->toDateString()]) }}"
                      method="POST" enctype="multipart/form-data" class="d-flex gap-2 flex-wrap">
                    @csrf
                    <input type="file" name="file" class="form-control" accept=".csv" required>
                    <button type="submit" class="btn btn-primary">Import CSV</button>
                </form>
            @else
                <div class="alert alert-info mb-0">
                    Import CSV sudah dinonaktifkan karena daftar hadir sudah disubmit.
                </div>
            @endif

            <!-- Export Button -->
            <div class="mt-3">
                <a href="{{ route('training.daftarhadirpelatihan.export', [$pelatihan->id, $date->toDateString()]) }}"
                   class="btn btn-outline-info btn-sm">
                    Export CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Kehadiran Peserta</h5>
            @if(isset($status) && $status->is_submitted)
                <span class="badge bg-success">Finalized</span>
            @else
                <span class="badge bg-warning text-dark">Draft</span>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('training.daftarhadirpelatihan.save', [$pelatihan->id, $date->toDateString()]) }}" method="POST">
                @csrf

                @php
                    $attMap = $attendances->keyBy('user_id');
                    $locked = isset($status) && $status->is_submitted;
                @endphp

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Reg ID</th>
                                <th>Nama</th>
                                <th>Status</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Foto In</th>
                                <th>Foto Out</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pelatihan->participants as $participant)
                                @php
                                    $attendance = $attMap->get($participant->user_id);

                                    $attArr = [
                                        'status'              => $attendance->status ?? 'absen',
                                        'check_in_time'       => $attendance->check_in_time ?? null,
                                        'check_out_time'      => $attendance->check_out_time ?? null,
                                        'check_in_photo'      => $attendance->check_in_photo ?? null,
                                        'check_out_photo'     => $attendance->check_out_photo ?? null,
                                        'check_in_timestamp'  => $attendance->check_in_timestamp ?? null,
                                        'check_out_timestamp' => $attendance->check_out_timestamp ?? null,
                                        'note'                => $attendance->note ?? null,
                                    ];

                                    $inTimeVal  = $attArr['check_in_time']  ? substr($attArr['check_in_time'], 0, 5)  : '';
                                    $outTimeVal = $attArr['check_out_time'] ? substr($attArr['check_out_time'], 0, 5) : '';
                                @endphp
                                <tr>
                                    <td>{{ $participant->registration_id }}</td>
                                    <td>{{ $participant->user->name }}</td>
                                    <td>
                                        <select name="attendance[{{ $participant->user_id }}][status]"
                                                class="form-control form-select"
                                                {{ $locked ? 'disabled' : '' }}>
                                            <option value="hadir" {{ $attArr['status'] === 'hadir' ? 'selected' : '' }}>Hadir</option>
                                            <option value="izin"  {{ $attArr['status'] === 'izin'  ? 'selected' : '' }}>Izin</option>
                                            <option value="sakit" {{ $attArr['status'] === 'sakit' ? 'selected' : '' }}>Sakit</option>
                                            <option value="absen" {{ $attArr['status'] === 'absen' ? 'selected' : '' }}>Absen</option>
                                        </select>
                                    </td>
                                    <td style="width:140px;">
                                        <input type="time"
                                               name="attendance[{{ $participant->user_id }}][check_in_time]"
                                               class="form-control"
                                               value="{{ $inTimeVal }}"
                                               {{ $locked ? 'disabled' : '' }}>
                                        @if(!empty($attArr['check_in_timestamp']))
                                            <small class="text-muted d-block">
                                                {{ \Carbon\Carbon::parse($attArr['check_in_timestamp'])->format('d M Y H:i') }}
                                            </small>
                                        @endif
                                    </td>
                                    <td style="width:140px;">
                                        <input type="time"
                                               name="attendance[{{ $participant->user_id }}][check_out_time]"
                                               class="form-control"
                                               value="{{ $outTimeVal }}"
                                               {{ $locked ? 'disabled' : '' }}>
                                        @if(!empty($attArr['check_out_timestamp']))
                                            <small class="text-muted d-block">
                                                {{ \Carbon\Carbon::parse($attArr['check_out_timestamp'])->format('d M Y H:i') }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($attArr['check_in_photo']))
                                            @php
                                                $urlIn = str_contains($attArr['check_in_photo'], 'http')
                                                    ? $attArr['check_in_photo']
                                                    : Storage::url($attArr['check_in_photo']);
                                            @endphp
                                            <a href="{{ $urlIn }}" target="_blank">View</a>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($attArr['check_out_photo']))
                                            @php
                                                $urlOut = str_contains($attArr['check_out_photo'], 'http')
                                                    ? $attArr['check_out_photo']
                                                    : Storage::url($attArr['check_out_photo']);
                                            @endphp
                                            <a href="{{ $urlOut }}" target="_blank">View</a>
                                        @endif
                                    </td>
                                    <td>
                                        <input type="text"
                                               name="attendance[{{ $participant->user_id }}][note]"
                                               class="form-control"
                                               value="{{ $attArr['note'] }}"
                                               {{ $locked ? 'disabled' : '' }}>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div><!-- /.table-responsive -->

                <div class="mt-3 d-flex gap-2">
                    @if(!$locked)
                        <button type="submit" name="action" value="save" class="btn btn-success">Simpan</button>
                        <button type="submit" name="action" value="finalize" class="btn btn-outline-secondary">
                            Submit &amp; Selesai
                        </button>
                    @else
                        <button type="button" class="btn btn-secondary" disabled>Sudah Final</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
