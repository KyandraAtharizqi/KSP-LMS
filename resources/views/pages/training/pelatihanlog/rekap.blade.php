@extends('layout.main')

@section('title', 'Rekapitulasi Pelatihan')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Rekapitulasi Pelatihan</h3>
        <div>
            @php
                // keep current query and override viewMode where needed
                $baseQuery = request()->except('viewMode', 'detail');
            @endphp

            <a href="{{ route('training.pelatihanlog.rekap', array_merge($baseQuery, ['viewMode'=>'monthly'])) }}"
               class="btn btn-sm {{ $viewMode==='monthly' ? 'btn-primary' : 'btn-outline-primary' }}">
                Bulanan
            </a>
            <a href="{{ route('training.pelatihanlog.rekap', array_merge($baseQuery, ['viewMode'=>'yearly'])) }}"
               class="btn btn-sm {{ $viewMode==='yearly' ? 'btn-primary' : 'btn-outline-primary' }}">
                Tahunan
            </a>

            @if($viewMode === 'yearly')
                <a href="{{ route('training.pelatihanlog.rekap', array_merge(request()->all(), ['detail'=>0])) }}"
                   class="btn btn-sm {{ !$detail ? 'btn-success' : 'btn-outline-success' }}">
                    Ringkas
                </a>
                <a href="{{ route('training.pelatihanlog.rekap', array_merge(request()->all(), ['detail'=>1])) }}"
                   class="btn btn-sm {{ $detail ? 'btn-success' : 'btn-outline-success' }}">
                    Detail
                </a>
            @endif
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="row mb-3 g-2 align-items-end">
        <input type="hidden" name="viewMode" value="{{ $viewMode }}">
        <input type="hidden" name="detail" value="{{ $detail }}">

        @if($viewMode === 'monthly')
            <div class="col-md-2">
                <select name="month" class="form-select">
                    @for($m=1;$m<=12;$m++)
                        <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m',$m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
        @endif

        <div class="col-md-2">
            <input type="number" name="year" class="form-control" value="{{ $year }}">
        </div>

        <div class="col-md-3">
            <select name="department_id" class="form-select">
                <option value="">Semua Departemen</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ $dept->id == $selectedDept ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>

    @if($positions->isEmpty())
        <div class="alert alert-info">Tidak ada posisi / user untuk periode dan filter yang dipilih.</div>
    @else

    {{-- YEARLY VIEW --}}
    @if($viewMode === 'yearly')
        @if(!$detail)
            {{-- SUMMARY --}}
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Departemen</th>
                        <th>Nama</th>
                        <th>Reg. ID</th>
                        @for($m=1;$m<=12;$m++)
                            <th>{{ substr(DateTime::createFromFormat('!m',$m)->format('M'),0,3) }}</th>
                        @endfor
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $rowNo = 1; @endphp
                    @foreach($positions as $deptId => $deptPositions)
                        @foreach($deptPositions as $pos)
                            @php
                                $user = $pos->user;
                                $userLogs = $logs->get($user->id) ?? collect();
                                $yearlyTotal = 0;
                            @endphp
                            <tr>
                                <td>{{ $rowNo++ }}</td>
                                <td>{{ optional($pos->department)->name ?? '-' }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->registration_id }}</td>

                                @for($m=1;$m<=12;$m++)
                                    @php
                                        $monthlyHours = $userLogs->filter(fn($l) => \Carbon\Carbon::parse($l->tanggal)->month == $m)->sum('jam');
                                        $yearlyTotal += $monthlyHours;
                                    @endphp
                                    <td class="{{ $monthlyHours == 0 ? 'text-muted bg-light' : '' }}">
                                        {{ $monthlyHours ?: '-' }}
                                    </td>
                                @endfor

                                <td><strong>{{ $yearlyTotal }}</strong></td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        @else
            {{-- DETAIL --}}
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Departemen</th>
                        <th>Nama</th>
                        <th>Reg. ID</th>
                        <th>Total Jam</th>
                        <th>Pelatihan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $rowNo = 1; @endphp
                    @foreach($positions as $deptId => $deptPositions)
                        @foreach($deptPositions as $pos)
                            @php
                                $user = $pos->user;
                                $userLogs = $logs->get($user->id) ?? collect();
                                $totalHours = $userLogs->sum('jam');
                            @endphp
                            <tr>
                                <td>{{ $rowNo++ }}</td>
                                <td>{{ optional($pos->department)->name ?? '-' }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->registration_id }}</td>
                                <td>{{ $totalHours }}</td>
                                <td>
                                    @if($userLogs->isNotEmpty())
                                        <ul class="mb-0 ps-3">
                                            @foreach($userLogs as $log)
                                                <li>
                                                    {{ $log->kode_pelatihan }} - {{ $log->pelatihan->judul ?? '-' }}
                                                    ({{ $log->jam }} jam, {{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') }})
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted">Tidak ada pelatihan</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        @endif
    @endif

    {{-- MONTHLY VIEW --}}
    @if($viewMode === 'monthly')
        <table class="table table-bordered table-sm">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Departemen</th>
                    <th>Nama</th>
                    <th>Reg. ID</th>
                    <th>Total Jam ({{ DateTime::createFromFormat('!m',$month)->format('F') }})</th>
                    <th>Pelatihan</th>
                </tr>
            </thead>
            <tbody>
                @php $rowNo = 1; @endphp
                @foreach($positions as $deptId => $deptPositions)
                    @foreach($deptPositions as $pos)
                        @php
                            $user = $pos->user;
                            $allUserLogs = $logs->get($user->id) ?? collect();
                            $userLogs = $allUserLogs->filter(fn($l) => \Carbon\Carbon::parse($l->tanggal)->month == $month);
                            $totalHours = $userLogs->sum('jam');
                        @endphp
                        <tr>
                            <td>{{ $rowNo++ }}</td>
                            <td>{{ optional($pos->department)->name ?? '-' }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->registration_id }}</td>
                            <td>{{ $totalHours }}</td>
                            <td>
                                @if($userLogs->isNotEmpty())
                                    <ul class="mb-0 ps-3">
                                        @foreach($userLogs as $log)
                                            <li>
                                                {{ $log->kode_pelatihan }} - {{ $log->pelatihan->judul ?? '-' }}
                                                ({{ $log->jam }} jam, {{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') }})
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">Tidak ada pelatihan</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @endif

    @endif {{-- end positions empty check --}}
</div>
@endsection
