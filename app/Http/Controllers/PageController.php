<?php

namespace App\Http\Controllers;

use App\Enums\LetterType;
use App\Helpers\GeneralHelper;
use App\Http\Requests\UpdateConfigRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Attachment;
use App\Models\Config;
use App\Models\Disposition;
use App\Models\Letter;
use App\Models\SignatureAndParaf;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateProfileRequest;


class PageController extends Controller
{
    public function index(Request $request): View
    {
        $todayIncomingLetter = Letter::incoming()->today()->count();
        $todayOutgoingLetter = Letter::outgoing()->today()->count();
        $todayDispositionLetter = Disposition::today()->count();
        $todayLetterTransaction = $todayIncomingLetter + $todayOutgoingLetter + $todayDispositionLetter;

        $yesterdayIncomingLetter = Letter::incoming()->yesterday()->count();
        $yesterdayOutgoingLetter = Letter::outgoing()->yesterday()->count();
        $yesterdayDispositionLetter = Disposition::yesterday()->count();
        $yesterdayLetterTransaction = $yesterdayIncomingLetter + $yesterdayOutgoingLetter + $yesterdayDispositionLetter;

        return view('pages.dashboard', [
            'greeting' => GeneralHelper::greeting(),
            'currentDate' => Carbon::now()->isoFormat('dddd, D MMMM YYYY'),
            'todayIncomingLetter' => $todayIncomingLetter,
            'todayOutgoingLetter' => $todayOutgoingLetter,
            'todayDispositionLetter' => $todayDispositionLetter,
            'todayLetterTransaction' => $todayLetterTransaction,
            'activeUser' => User::active()->count(),
            'percentageIncomingLetter' => GeneralHelper::calculateChangePercentage($yesterdayIncomingLetter, $todayIncomingLetter),
            'percentageOutgoingLetter' => GeneralHelper::calculateChangePercentage($yesterdayOutgoingLetter, $todayOutgoingLetter),
            'percentageDispositionLetter' => GeneralHelper::calculateChangePercentage($yesterdayDispositionLetter, $todayDispositionLetter),
            'percentageLetterTransaction' => GeneralHelper::calculateChangePercentage($yesterdayLetterTransaction, $todayLetterTransaction),
        ]);
    }

    public function profile(Request $request): View
    {
        $user = auth()->user()->load('signatureParaf');

        return view('pages.profile', [
            'data' => $user,
        ]);
    }

    public function profileUpdate(UpdateProfileRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $user = auth()->user();
            
            // Ambil semua data yang sudah divalidasi dari UpdateProfileRequest
            $updateData = $request->validated();
    
            // Handle profile picture HANYA JIKA ADA FILE BARU
            if ($request->hasFile('profile_picture')) {
                // Hapus foto lama
                if ($user->profile_picture) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_picture);
                }
    
                // Simpan foto baru
                $path = $request->file('profile_picture')->store('avatars', 'public');
    
                $updateData['profile_picture'] = $path;
            }
    
            $user->update($updateData);
            
            return back()->with('success', 'Profil berhasil diperbarui.');
    
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }


    public function deactivate(): RedirectResponse
    {
        try {
            auth()->user()->update(['is_active' => false]);
            Auth::logout();
            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function settings(Request $request): View
    {
        return view('pages.setting', [
            'configs' => Config::all(),
        ]);
    }

    public function settingsUpdate(UpdateConfigRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            foreach ($request->validated() as $code => $value) {
                Config::where('code', $code)->update(['value' => $value]);
            }
            DB::commit();
            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            DB::rollBack();
            return back()->with('error', $exception->getMessage());
        }
    }

    public function removeAttachment(Request $request): RedirectResponse
    {
        try {
            $attachment = Attachment::find($request->id);
            $oldPicture = $attachment->path_url;
            if (str_contains($oldPicture, '/storage/attachments/')) {
                $url = parse_url($oldPicture, PHP_URL_PATH);
                Storage::delete(str_replace('/storage', 'public', $url));
            }
            $attachment->delete();
            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function uploadSignature(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'signature_data' => 'required|string',
            ]);

            $user = auth()->user();
            $imageData = $request->input('signature_data');
            $filename = $user->registration_id . '.png';
            $path = 'public/signatures/' . $filename;

            Storage::put($path, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData)));

            SignatureAndParaf::updateOrCreate(
                ['registration_id' => $user->registration_id],
                ['signature_path' => 'signatures/' . $filename]
            );

            return back()->with('success', 'Signature uploaded successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to upload signature: ' . $e->getMessage());
        }
    }

    public function uploadParaf(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'paraf_data' => 'required|string',
            ]);

            $user = auth()->user();
            $imageData = $request->input('paraf_data');
            $filename = $user->registration_id . '.png';
            $path = 'public/parafs/' . $filename;

            Storage::put($path, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData)));

            SignatureAndParaf::updateOrCreate(
                ['registration_id' => $user->registration_id],
                ['paraf_path' => 'parafs/' . $filename]
            );

            return back()->with('success', 'Paraf uploaded successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to upload paraf: ' . $e->getMessage());
        }
    }
    public function testProfile()
    {
        dd('KONEKSI BERHASIL! Controller terhubung.');
    }
}
