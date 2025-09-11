@extends('layout.main')

@section('title', 'Rekapitulasi Pelatihan')

@section('content')
<div class="container">
    <h3 class="mb-4">
        Rekapitulasi Pelatihan 
        @if($viewMode === 'monthly')
            - {{ DateTime::createFromFormat('!m', $month)->format('F') }}/{{ $year }}
        @else
            - Tahun {{ $year }}
        @endif
    </h3>

    <form method="GET" class="row mb-3 g-2 align-items-end">
        <div class="col-md-2">
            <select name="viewMode" class="form-select" onchange="this.form.submit()">
                <option value="monthly" {{ $viewMode=='monthly'?'selected':'' }}>Bulanan</option>
                <option value="yearly" {{ $viewMode=='yearly'?'selected':'' }}>Tahunan</option>
            </select>
        </div>

        @if($viewMode === 'monthly')
            <div class="col-md-2">
                <select name="month" class="form-select">
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ $m }}" {{ $m==$month?'selected':'' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
        @endif

        <div class="col-md-2">
            <input type="number" name="year" class="form-control" value="{{ $year }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>

    @foreach($positions as $departmentId => $deptPositions)
        @php
            $departmentName = optional($deptPositions->first()->department)->name ?? 'Tidak ada departemen';
        @endphp

        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Departemen: {{ $departmentName }}</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:50px;">No</th>
                            <th>Nama</th>
                            <th>Reg. ID</th>
                            @if($viewMode === 'monthly')
                                <th>Jam</th>
                                <th>Detail Pelatihan</th>
                            @else
                                @for($m=1; $m<=12; $m++)
                                    <th>{{ substr(DateTime::createFromFormat('!m', $m)->format('M'),0,3) }}</th>
                                @endfor
                                <th>Total Jam</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deptPositions as $i => $pos)
                            @php
                                $user = $pos->user;
                                $userLogs = $logs->get($user->id) ?? collect();
                            @endphp
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->registration_id }}</td>

                                @if($viewMode === 'monthly')
                                    @php $totalHours = $userLogs->sum('jam'); @endphp
                                    <td>{{ $totalHours }}</td>
                                    <td>
                                        @if($userLogs->isNotEmpty())
                                            <ul class="mb-0 ps-3">
                                                @foreach($userLogs as $log)
                                                    <li>
                                                        {{ $log->kode_pelatihan }} - 
                                                        {{ $log->pelatihan->judul ?? '-' }} 
                                                        ({{ $log->jam }} jam, {{ $log->tanggal->format('d/m') }})
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-muted">Tidak ada pelatihan</span>
                                        @endif
                                    </td>
                                @else
                                    @php
                                        $yearlyTotal = 0;
                                    @endphp
                                    @for($m=1; $m<=12; $m++)
                                        @php
                                            $monthlyLogs = $userLogs->filter(fn($log) => $log->tanggal->month == $m);
                                            $monthlyHours = $monthlyLogs->sum('jam');
                                            $yearlyTotal += $monthlyHours;
                                        @endphp
                                        <td class="{{ $monthlyHours==0 ? 'bg-light text-muted' : '' }}">
                                            {{ $monthlyHours ?: '-' }}
                                        </td>
                                    @endfor
                                    <td><strong>{{ $yearlyTotal }}</strong></td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
@endsection
