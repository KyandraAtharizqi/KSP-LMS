<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NotaDinas;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;



class NotaDinasController extends Controller
{
    protected $fillable = ['judul', 'isi'];

    public function index()
    {
        $notaDinas = NotaDinas::latest()->get(); // ambil semua nota dinas
        return view('pages.knowledge.notadinas.index', compact('notaDinas'));
    }


    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('pages.knowledge.notadinas.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|unique:nota_dinas,kode',
            'judul' => 'required|string',
            'pemateri' => 'required|string',
            'perihal' => 'required|string',
            'tanggal' => 'required|date',
            'dari' => 'required|string',
            'kepada' => 'required|string',
            'lampiran' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        // Ambil semua data yang diizinkan
        $data = $request->only([
            'kode', 'judul', 'pemateri', 'perihal',
            'tanggal', 'dari', 'kepada'
        ]);

        // Tambahkan user_id ke array
        $data['user_id'] = auth()->id();

        // Cek apakah ada file lampiran
        if ($request->hasFile('lampiran')) {
            $data['lampiran'] = $request->file('lampiran')->store('lampiran_notadinas', 'public');
        }

        // Simpan ke database
        $nota = NotaDinas::create($data);

        if (!$nota) {
            dd('Gagal menyimpan data');
        }

        return redirect()->route('knowledge.notadinas.index')->with('success', 'Nota Dinas berhasil disimpan.');
    }

    public function show($id)
    {
        $notaDinas = NotaDinas::with(['user.signatureParaf'])->findOrFail($id);
        return view('pages.knowledge.notadinas.show', compact('notaDinas'));
    }

    public function destroy($id)
    {
        $nota = NotaDinas::findOrFail($id);

        // Hapus file lampiran kalau ada
        if ($nota->lampiran && \Storage::disk('public')->exists($nota->lampiran)) {
            \Storage::disk('public')->delete($nota->lampiran);
        }

        $nota->delete();

        return redirect()->route('knowledge.notadinas.index')->with('success', 'Nota Dinas berhasil dihapus.');
    }

    public function download($id)
    {
        $notaDinas = NotaDinas::with(['user.signatureParaf'])->findOrFail($id);

        $pdf = \PDF::loadView('pages.knowledge.notadinas.pdf', compact('notaDinas'))
                ->setPaper('A4', 'portrait');

        $filename = 'NotaDinas-' . Str::slug($notaDinas->judul) . '.pdf';

        return $pdf->download($filename);
    }

}
