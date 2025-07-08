<?php

namespace App\Http\Controllers;

use App\Models\SuratPengajuanTrainingExample;
use Illuminate\Http\Request;

class SuratPengajuanTrainingExampleController extends Controller
{
    public function index()
    {
        $examples = SuratPengajuanTrainingExample::latest()->get();
        return view('pages.training.suratpengajuan.index', compact('examples'));
    }

    public function create()
    {
        return view('pages.training.suratpengajuan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'training_date' => 'required|date',
        ]);

        SuratPengajuanTrainingExample::create([
            'title' => $request->title,
            'description' => $request->description,
            'training_date' => $request->training_date,
            'submitted_by' => auth()->id(),
            'status' => 'pending',
        ]);

        return redirect()->route('training.suratpengajuan.index')->with('success', 'Surat berhasil diajukan!');
    }

    public function preview(SuratPengajuanTrainingExample $example)
    {
        return view('pages.training.suratpengajuan.preview', ['letter' => $example]);
    }
}
