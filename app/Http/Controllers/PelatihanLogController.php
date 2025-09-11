<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PelatihanLog;
use App\Models\UserPositionHistory;
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
        $viewMode = $request->get('viewMode', 'yearly'); // yearly | monthly
        $mode     = $request->get('mode', 'pengajuan'); // pengajuan | current
        $year     = (int) $request->get('year', now()->year);

        if ($viewMode === 'monthly') {
            $month = (int) $request->get('month', now()->month);
            $start = Carbon::create($year, $month, 1)->startOfMonth();
            $end   = Carbon::create($year, $month, 1)->endOfMonth();
        } else {
            $month = null;
            $start = Carbon::create($year, 1, 1)->startOfYear();
            $end   = Carbon::create($year, 12, 31)->endOfYear();
        }

        // ✅ 1. Active positions until the end of the selected period
        $positions = UserPositionHistory::with(['user', 'department'])
            ->where('effective_date', '<=', $end)
            ->where('is_active', true)
            ->get()
            ->groupBy('department_id');

        // ✅ 2. Logs in selected period
        $logs = PelatihanLog::with(['user', 'pelatihan'])
            ->whereBetween('tanggal', [$start, $end])
            ->get()
            ->groupBy('user_id');

        // ✅ 3. Group logs per user & month
        $userLogsByMonth = [];
        foreach ($logs as $userId => $userLogs) {
            foreach ($userLogs as $log) {
                $logMonth = Carbon::parse($log->tanggal)->month;
                $userLogsByMonth[$userId][$logMonth][] = $log;
            }
        }

        return view('pages.training.pelatihanlog.rekap', compact(
            'positions', 'userLogsByMonth', 'year', 'month', 'mode', 'viewMode', 'logs'
        ));
    }
}
