<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KnowledgeEvaluasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Untuk sementara, karena belum ada ID knowledge yang bisa ditarik
        // Kita hanya menampilkan template kosong yang menyerupai evaluasi level 1 pelatihan
        
        $q = $request->get('q');
        
        // Nanti ketika sudah ada data knowledge, implementasi akan seperti ini:
        // $knowledgeList = Knowledge::with(['evaluasi.user', 'evaluasi.superior', 'participants'])
        //                           ->when($q, function($query, $q) {
        //                               return $query->where('judul', 'like', "%{$q}%")
        //                                           ->orWhere('kode_knowledge', 'like', "%{$q}%");
        //                           })
        //                           ->get();
        
        $knowledgeList = collect(); // Sementara kosong
        
        return view('pages.knowledge.evaluasi.index', compact('knowledgeList'));
    }
}