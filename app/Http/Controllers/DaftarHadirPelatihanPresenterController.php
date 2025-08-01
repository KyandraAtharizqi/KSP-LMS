<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Presenter;
use App\Models\SuratPengajuanPelatihan;
use Illuminate\Http\Request;
use App\Models\PelatihanPresenter;
use App\Models\DaftarHadirPelatihanStatus;
use Illuminate\Support\Facades\DB;

class DaftarHadirPelatihanPresenterController extends Controller
{
    public function index(SuratPengajuanPelatihan $pelatihan)
    {
        $dates = DaftarHadirPelatihanStatus::where('pelatihan_id', $pelatihan->id)
                    ->orderBy('date')
                    ->get()
                    ->groupBy('date');

        // Fixed: Load relationships and group by date properly
        $assigned = PelatihanPresenter::where('pelatihan_id', $pelatihan->id)
            ->with(['user', 'presenter']) // Load relationships
            ->get()
            ->groupBy(function($item) {
                return $item->date->format('Y-m-d'); // 👈 This is the correct way
            });
            
        $externalPresenters = Presenter::orderBy('name')->get();
        $internalUsers = User::orderBy('name')->get();

        return view('pages.training.daftarhadirpelatihan.presenter.index', compact(
            'pelatihan', 'dates', 'assigned', 'externalPresenters', 'internalUsers'
        ));
    }

    public function update(Request $request, $pelatihanId, $date)
    {
        $request->validate([
            'date' => 'required|date',
            'user_ids' => 'nullable|array',
            'presenter_ids' => 'nullable|array',
        ]);

        // Delete existing assignments for this date first to avoid duplicates
        PelatihanPresenter::where('pelatihan_id', $pelatihanId)
            ->where('date', $request->date)
            ->delete();

        // Save internal presenters
        if ($request->has('user_ids')) {
            foreach ($request->user_ids as $userId) {
                PelatihanPresenter::create([
                    'pelatihan_id' => $pelatihanId,
                    'date' => $request->date,
                    'type' => 'internal',
                    'user_id' => $userId,
                    'presenter_id' => null,
                ]);
            }
        }

        // Save external presenters
        if ($request->has('presenter_ids')) {
            foreach ($request->presenter_ids as $presenterId) {
                PelatihanPresenter::create([
                    'pelatihan_id' => $pelatihanId,
                    'date' => $request->date,
                    'type' => 'external',
                    'user_id' => null,
                    'presenter_id' => $presenterId,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Presenter berhasil ditambahkan.');
    }

    public function storeInlinePresenter(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'institution' => 'nullable|string|max:255',
        ]);

        $presenter = Presenter::create([
            'name' => $request->name,
            'institution' => $request->institution,
        ]);

        return response()->json([
            'id' => $presenter->id,
            'name' => $presenter->name,
            'institution' => $presenter->institution,
        ]);
    }

    public function destroy(Request $request, SuratPengajuanPelatihan $pelatihan)
    {
        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:internal,external',
            'id' => 'required|integer',
        ]);

        $query = PelatihanPresenter::where('pelatihan_id', $pelatihan->id)
                    ->where('date', $request->date);

        if ($request->type === 'internal') {
            $query->where('user_id', $request->id);
        } else {
            $query->where('presenter_id', $request->id);
        }

        $query->delete();

        return response()->json(['success' => true]);
    }

    public function storeExternalPresenter(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'institution' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        Presenter::create($validated);

        return redirect()->back()->with('success', 'Presenter eksternal berhasil ditambahkan.');
    }


public function submitFinal(Request $request, $pelatihanId, $date)
{
    $request->validate([
        'check_in_time' => 'array',
        'check_out_time' => 'array',
    ]);

    $userId = auth()->id();

    // Update check-in/out times and submitted_by for presenters
    foreach ($request->check_in_time ?? [] as $presenterId => $checkIn) {
        PelatihanPresenter::where('id', $presenterId)
            ->update([
                'check_in_time' => $checkIn,
                'check_out_time' => $request->check_out_time[$presenterId] ?? null,
                'submitted_by' => $userId,
                'is_submitted' => true,
                'submitted_at' => now(),
                
            ]);
    }
    return redirect()->back()->with('success', 'Data presenter telah dikunci.');
}

}