<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DaftarHadirKnowledgeController extends Controller
{
    public function index()
    {
        $daftarHadir = DB::table('daftar_hadir_knowledge')->latest()->get();
        return view('pages.knowledge.daftarhadir.index', compact('daftarHadir'));
    }

    public function create()
    {
        return view('pages.knowledge.daftarhadir.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'pemateri' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'peserta' => 'required|string',
        ]);

        DB::table('daftar_hadir_knowledge')->insert([
            'judul' => $request->judul,
            'pemateri' => $request->pemateri,
            'tanggal' => $request->tanggal,
            'peserta' => $request->peserta,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('knowledge.daftarhadir.index')->with('success', 'Data berhasil disimpan.');
    }

    public function show($id)
    {
        $data = DB::table('daftar_hadir_knowledge')->find($id);
        return view('pages.knowledge.daftarhadir.show', compact('data'));
    }

    public function edit($id)
    {
        $data = DB::table('daftar_hadir_knowledge')->find($id);
        return view('pages.knowledge.daftarhadir.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'pemateri' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'peserta' => 'required|string',
        ]);

        DB::table('daftar_hadir_knowledge')->where('id', $id)->update([
            'judul' => $request->judul,
            'pemateri' => $request->pemateri,
            'tanggal' => $request->tanggal,
            'peserta' => $request->peserta,
            'updated_at' => now(),
        ]);

        return redirect()->route('knowledge.daftarhadir.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DB::table('daftar_hadir_knowledge')->where('id', $id)->delete();
        return redirect()->route('knowledge.daftarhadir.index')->with('success', 'Data berhasil dihapus.');
    }
}

