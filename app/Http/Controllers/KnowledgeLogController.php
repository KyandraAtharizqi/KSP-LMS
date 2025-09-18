<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KnowledgeLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Untuk sementara, karena belum ada ID knowledge yang bisa ditarik
        // Kita hanya menampilkan template kosong yang menyerupai log pelatihan
        
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));
        
        // Nanti ketika sudah ada data knowledge, implementasi akan seperti ini:
        // $logs = KnowledgeLog::whereMonth('tanggal', $month)
        //                    ->whereYear('tanggal', $year)
        //                    ->with(['user', 'knowledge'])
        //                    ->orderBy('tanggal', 'desc')
        //                    ->get();
        
        $logs = collect(); // Sementara kosong
        
        return view('pages.knowledge.log.index', compact('logs', 'month', 'year'));
    }
}