<?php

namespace App\Http\Controllers;

use App\Models\PelatihanLog;
use App\Models\UserPositionHistory;
use App\Models\Department;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PelatihanLogController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        $logs = PelatihanLog::with(['user', 'pelatihan', 'pengajuanDepartment', 'currentDepartment'])
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->orderBy('tanggal', 'asc')
            ->orderBy('user_id', 'asc')
            ->get();

        return view('pages.training.pelatihanlog.index', compact('logs', 'month', 'year'));
    }

    public function rekap(Request $request)
    {
        // Input / defaults
        $viewMode    = $request->get('viewMode', 'yearly'); // yearly | monthly
        $year        = (int) $request->get('year', now()->year);
        $month       = (int) $request->get('month', now()->month);
        $detail      = (int) $request->get('detail', 0); // 0 = summary, 1 = detail
        $selectedDept = $request->get('department_id', null);

        // Determine date window
        if ($viewMode === 'monthly') {
            $start = Carbon::create($year, $month, 1)->startOfMonth();
            $end   = Carbon::create($year, $month, 1)->endOfMonth();
        } else {
            $start = Carbon::create($year, 1, 1)->startOfYear();
            $end   = Carbon::create($year, 12, 31)->endOfYear();
            // set $month to null when yearly
            $month = null;
        }

        // Departments for the dropdown
        $departments = Department::orderBy('name')->get();

        // 1) Snapshot of active positions up to the end of the period
        //    - We fetch UserPositionHistory rows which indicate the user was (or still is) assigned
        //    - We filter by effective_date <= $end and is_active = true
        $positionsQuery = UserPositionHistory::with(['user', 'department'])
            ->where('effective_date', '<=', $end)
            ->where('is_active', true);

        if ($selectedDept) {
            $positionsQuery->where('department_id', $selectedDept);
        }

        // collect and group by department_id
        $positions = $positionsQuery->get()->groupBy('department_id');

        // 2) Logs in the selected period (grouped by user_id)
        $logs = PelatihanLog::with(['user', 'pelatihan'])
            ->whereBetween('tanggal', [$start, $end])
            ->get()
            ->groupBy('user_id');

        // Pass to view
        return view('pages.training.pelatihanlog.rekap', compact(
            'positions',
            'logs',
            'departments',
            'selectedDept',
            'viewMode',
            'year',
            'month',
            'detail'
        ));
    }
}
