<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratPengajuanKnowledge;
use App\Models\KnowledgeParticipant;
use App\Models\KnowledgeSignatureAndParaf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SuratPengajuanKnowledgeController extends Controller
{
    // List all knowledge sharings
    public function index()
    {
        $knowledges = SuratPengajuanKnowledge::with(['participants.user', 'parafs', 'signatures'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('knowledge.index', compact('knowledges'));
    }

    // Show create form
    public function create()
    {
        $knowledge = new SuratPengajuanKnowledge();
        return view('knowledge.form', compact('knowledge'));
    }

    // Store new knowledge sharing
    public function store(Request $request)
    {
        $request->validate([
            'tipe' => 'required|in:lanjutan,non_lanjutan',
            'kode_knowledge' => 'required|unique:surat_pengajuan_knowledges,kode_knowledge',
            'judul' => 'required|string|max:255',
            'pemateri' => 'nullable|array',
            'tanggal_pelaksanaan' => 'nullable|array',
        ]);

        $knowledge = SuratPengajuanKnowledge::create([
            'tipe' => $request->tipe,
            'kode_pelatihan' => $request->kode_pelatihan,
            'kode_knowledge' => $request->kode_knowledge,
            'judul' => $request->judul,
            'materi' => $request->materi,
            'pemateri' => $request->pemateri,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'durasi' => $request->durasi,
            'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan,
            'tempat' => $request->tempat,
            'penyelenggara' => $request->penyelenggara,
            'created_by' => Auth::id(),
        ]);

        // Optionally: create empty participants / parafs/signature later
        return redirect()->route('knowledge.index')->with('success', 'Knowledge Sharing created successfully.');
    }

    // Show edit form
    public function edit(SuratPengajuanKnowledge $knowledge)
    {
        $knowledge->load(['participants.user', 'parafs', 'signatures']);
        return view('knowledge.form', compact('knowledge'));
    }

    // Update knowledge sharing
    public function update(Request $request, SuratPengajuanKnowledge $knowledge)
    {
        $request->validate([
            'tipe' => 'required|in:lanjutan,non_lanjutan',
            'judul' => 'required|string|max:255',
            'pemateri' => 'nullable|array',
            'tanggal_pelaksanaan' => 'nullable|array',
        ]);

        $knowledge->update([
            'tipe' => $request->tipe,
            'kode_pelatihan' => $request->kode_pelatihan,
            'judul' => $request->judul,
            'materi' => $request->materi,
            'pemateri' => $request->pemateri,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'durasi' => $request->durasi,
            'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan,
            'tempat' => $request->tempat,
            'penyelenggara' => $request->penyelenggara,
        ]);

        return redirect()->route('knowledge.index')->with('success', 'Knowledge Sharing updated successfully.');
    }

    // Show knowledge sharing (preview)
    public function show(SuratPengajuanKnowledge $knowledge)
    {
        $knowledge->load(['participants.user', 'parafs', 'signatures']);
        return view('knowledge.show', compact('knowledge'));
    }

    // Delete knowledge sharing
    public function destroy(SuratPengajuanKnowledge $knowledge)
    {
        $knowledge->delete();
        return redirect()->route('knowledge.index')->with('success', 'Knowledge Sharing deleted successfully.');
    }
}
