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
use App\Models\PengajuanKnowledge;
use App\Models\SignatureAndParaf;
use App\Models\SuratPengajuanPelatihan;
use App\Models\SuratTugasPelatihan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Requests\UpdateProfileRequest;


class PageController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        
        // Filter data berdasarkan role user - menampilkan semua data
        $baseSuratPengajuanQuery = SuratPengajuanPelatihan::query();
        $baseSuratTugasQuery = SuratTugasPelatihan::query();
        $baseKnowledgeQuery = PengajuanKnowledge::query();
        
        // Query untuk data yang diterima/disetujui
        $baseSuratPengajuanAcceptedQuery = SuratPengajuanPelatihan::where('is_accepted', true);
        $baseSuratTugasAcceptedQuery = SuratTugasPelatihan::where('is_accepted', true);
        $baseKnowledgeAcceptedQuery = PengajuanKnowledge::where('status', 'approved');
        
        // Filter berdasarkan role
        switch ($user->role) {
            case 'admin':
                // Admin bisa melihat semua data
                break;
            case 'department_admin':
                // Department admin hanya melihat data department mereka
                $baseSuratPengajuanQuery->whereIn('created_by', function($query) use ($user) {
                    return $query->select('id')->from('users')->where('department_id', $user->department_id);
                });
                $baseSuratTugasQuery->whereIn('created_by', function($query) use ($user) {
                    return $query->select('id')->from('users')->where('department_id', $user->department_id);
                });
                $baseKnowledgeQuery->whereIn('created_by', function($query) use ($user) {
                    return $query->select('id')->from('users')->where('department_id', $user->department_id);
                });
                // Filter untuk data yang diterima
                $baseSuratPengajuanAcceptedQuery->whereIn('created_by', function($query) use ($user) {
                    return $query->select('id')->from('users')->where('department_id', $user->department_id);
                });
                $baseSuratTugasAcceptedQuery->whereIn('created_by', function($query) use ($user) {
                    return $query->select('id')->from('users')->where('department_id', $user->department_id);
                });
                $baseKnowledgeAcceptedQuery->whereIn('created_by', function($query) use ($user) {
                    return $query->select('id')->from('users')->where('department_id', $user->department_id);
                });
                break;
            case 'division_admin':
                // Division admin hanya melihat data division mereka
                $baseSuratPengajuanQuery->whereIn('created_by', function($query) use ($user) {
                    return $query->select('id')->from('users')->where('division_id', $user->division_id);
                });
                $baseSuratTugasQuery->whereIn('created_by', function($query) use ($user) {
                    return $query->select('id')->from('users')->where('division_id', $user->division_id);
                });
                $baseKnowledgeQuery->whereIn('created_by', function($query) use ($user) {
                    return $query->select('id')->from('users')->where('division_id', $user->division_id);
                });
                // Filter untuk data yang diterima
                $baseSuratPengajuanAcceptedQuery->whereIn('created_by', function($query) use ($user) {
                    return $query->select('id')->from('users')->where('division_id', $user->division_id);
                });
                $baseSuratTugasAcceptedQuery->whereIn('created_by', function($query) use ($user) {
                    return $query->select('id')->from('users')->where('division_id', $user->division_id);
                });
                $baseKnowledgeAcceptedQuery->whereIn('created_by', function($query) use ($user) {
                    return $query->select('id')->from('users')->where('division_id', $user->division_id);
                });
                break;
            case 'staff':
            case 'upper_staff':
            default:
                // Staff hanya melihat data mereka sendiri
                $baseSuratPengajuanQuery->where('created_by', $user->id);
                $baseSuratTugasQuery->where('created_by', $user->id);
                $baseKnowledgeQuery->where('created_by', $user->id);
                // Filter untuk data yang diterima
                $baseSuratPengajuanAcceptedQuery->where('created_by', $user->id);
                $baseSuratTugasAcceptedQuery->where('created_by', $user->id);
                $baseKnowledgeAcceptedQuery->where('created_by', $user->id);
                break;
        }
        
        // Hitung total data
        $todaySuratPengajuan = $baseSuratPengajuanQuery->count();
        $todaySuratTugas = $baseSuratTugasQuery->count();
        $todayKnowledgeLetter = $baseKnowledgeQuery->count();
        
        // Hitung data yang diterima/disetujui
        $acceptedSuratPengajuan = $baseSuratPengajuanAcceptedQuery->count();
        $acceptedSuratTugas = $baseSuratTugasAcceptedQuery->count();
        $acceptedKnowledgeLetter = $baseKnowledgeAcceptedQuery->count();
        
        $todayLetterTransaction = $todaySuratPengajuan + $todaySuratTugas + $todayKnowledgeLetter;

        return view('pages.dashboard', [
            'greeting' => GeneralHelper::greeting(),
            'currentDate' => Carbon::now()->isoFormat('dddd, D MMMM YYYY'),
            'todayIncomingLetter' => $todaySuratPengajuan,
            'todayOutgoingLetter' => $todaySuratTugas,
            'todayKnowledgeLetter' => $todayKnowledgeLetter,
            'todayLetterTransaction' => $todayLetterTransaction,
            'acceptedSuratPengajuan' => $acceptedSuratPengajuan,
            'acceptedSuratTugas' => $acceptedSuratTugas,
            'acceptedKnowledgeLetter' => $acceptedKnowledgeLetter,
            'activeUser' => User::active()->count(),
            'percentageIncomingLetter' => 0, // Set 0 karena kita tidak hitung persentase lagi
            'percentageOutgoingLetter' => 0,
            'percentageKnowledgeLetter' => 0,
            'percentageLetterTransaction' => 0,
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
                // Hapus foto lama jika bukan URL dari ui-avatars
                if ($user->profile_picture && !Str::startsWith($user->profile_picture, 'http')) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
    
                // Pastikan direktori avatars ada
                Storage::disk('public')->makeDirectory('avatars');
    
                // Ambil file dan periksa validitas
                $file = $request->file('profile_picture');
                if (!$file->isValid()) {
                    throw new \Exception('File upload gagal: ' . $file->getError());
                }
                
                // Buat nama file yang unik dengan extension asli
                $fileName = uniqid('avatar_') . '.' . $file->getClientOriginalExtension();
                
                // Simpan foto baru dengan nama yang ditentukan
                $path = $file->storeAs('avatars', $fileName, 'public');
                
                // Pastikan file tersimpan dengan benar
                if (!Storage::disk('public')->exists($path)) {
                    throw new \Exception('File gagal disimpan ke storage');
                }
                
                // Debug info
                Log::info('Profile picture uploaded', [
                    'path' => $path,
                    'file_name' => $fileName,
                    'original_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'exists' => Storage::disk('public')->exists($path)
                ]);
    
                $updateData['profile_picture'] = $path;
            }
    
            $user->update($updateData);
            
            return back()->with('success', 'Profil berhasil diperbarui.');
    
        } catch (\Throwable $exception) {
            Log::error('Profile update error: ' . $exception->getMessage(), [
                'exception' => $exception,
                'trace' => $exception->getTraceAsString()
            ]);
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
