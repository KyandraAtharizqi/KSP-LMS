<?php

namespace App\Http\Controllers;

use App\Models\SignatureAndParaf;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil pengguna.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $user = Auth::user();
        return view('pages.profile', ['data' => $user]);
    }

    /**
     * Memperbarui data profil pengguna.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        try {
            \Log::info('ProfileController@update called', ['request' => $request->all()]);
            
            $user = User::findOrFail($request->id);
            \Log::info('User found', ['user_id' => $user->id, 'name' => $user->name]);
            
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:800',
            ];
            
            if ($request->filled('current_password') || $request->filled('new_password') || $request->filled('new_password_confirmation')) {
                $rules['current_password'] = 'required';
                $rules['new_password'] = ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()];
            }
            
            $validator = Validator::make($request->all(), $rules, [
                'current_password.required' => 'Password saat ini wajib diisi',
                'new_password.required' => 'Password baru wajib diisi',
                'new_password.confirmed' => 'Konfirmasi password baru tidak cocok',
            ]);
            
            if ($validator->fails()) {
                \Log::warning('Validation failed', ['errors' => $validator->errors()]);
                return redirect()->back()->withErrors($validator)->withInput();
            }
            
            $user->name = $request->name;
            $user->email = $request->email;
            
            // Debug info tentang file yang di-upload
            \Log::info('Profile picture update request', [
                'has_file' => $request->hasFile('profile_picture'),
                'has_reset' => $request->has('reset_avatar'),
                'all_files' => $request->allFiles(),
                'current_profile_picture' => $user->profile_picture
            ]);
            
            if ($request->has('reset_avatar')) {
                \Log::info('Resetting avatar');
                if ($user->profile_picture && !str_contains($user->profile_picture, 'ui-avatars.com') && !str_contains($user->profile_picture, 'http')) {
                    $oldPath = str_replace('storage/', '', $user->profile_picture);
                    Storage::disk('public')->delete($oldPath);
                }
                
                $user->profile_picture = null;
            }
            
            if ($request->hasFile('profile_picture')) {
                \Log::info('Processing profile picture upload');
                $file = $request->file('profile_picture');
                \Log::info('File details', [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                    'is_valid' => $file->isValid()
                ]);
                
                if (!$file->isValid()) {
                    throw new \Exception('File upload failed: ' . $file->getError());
                }
                
                // Hapus foto lama jika ada dan bukan URL avatar
                if ($user->profile_picture && !str_contains($user->profile_picture, 'ui-avatars.com') && !str_contains($user->profile_picture, 'http')) {
                    $oldPath = $user->profile_picture;
                    \Log::info('Deleting old profile picture', ['path' => $oldPath]);
                    Storage::disk('public')->delete($oldPath);
                }
                
                // Pastikan direktori profile-pictures ada
                Storage::disk('public')->makeDirectory('profile-pictures');
                
                // Buat nama file unik dengan ekstensi aslinya
                $fileName = uniqid('profile_') . '.' . $file->getClientOriginalExtension();
                
                // Simpan file ke storage dengan nama yang ditentukan
                $path = $request->file('profile_picture')->storeAs('profile-pictures', $fileName, 'public');
                \Log::info('New profile picture stored', ['path' => $path, 'url' => asset('storage/'.$path)]);
                
                // Verifikasi file tersimpan
                if (!Storage::disk('public')->exists($path)) {
                    throw new \Exception('File failed to be saved to storage');
                }
                
                $user->profile_picture = $path; // Simpan path relatif
            }
            
            if ($request->filled('current_password')) {
                \Log::info('Processing password change');
                if (!Hash::check($request->current_password, $user->password)) {
                    \Log::warning('Current password mismatch');
                    return redirect()->back()->withErrors(['current_password' => 'Password saat ini tidak sesuai'])->withInput();
                }
                
                $user->password = Hash::make($request->new_password);
            }
            
            $user->save();
            \Log::info('User profile updated successfully', ['user_id' => $user->id]);
            
            return redirect()->route('profile.show')->with('success', 'Profil berhasil diperbarui.');
        } catch (\Throwable $e) {
            \Log::error('Error updating profile', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menangani upload Tanda Tangan dari modal.
     */
    public function uploadSignature(Request $request)
    {
        $request->validate(['signature_data' => 'required']);

        $user = Auth::user();
        
        // Data gambar dalam format base64
        $imageData = $request->input('signature_data'); 
        
        // Pisahkan header dari data base64
        list($type, $imageData) = explode(';', $imageData);
        list(, $imageData)      = explode(',', $imageData);
        $imageData = base64_decode($imageData);

        // Buat nama file yang unik
        $filename = 'signatures/' . $user->registration_id . '.png';

        // Simpan file ke storage/app/public/signatures
        Storage::disk('public')->put($filename, $imageData);

        // Simpan atau update path di tabel signature_and_parafs
        SignatureAndParaf::updateOrCreate(
            ['registration_id' => $user->registration_id],
            [
                'user_id' => $user->id,
                'signature_path' => $filename
            ]
        );

        return back()->with('success', 'Tanda tangan berhasil disimpan.');
    }

    /**
     * Menangani upload Paraf dari modal.
     */
    public function uploadParaf(Request $request)
    {
        $request->validate(['paraf_data' => 'required']);

        $user = Auth::user();
        
        // Data gambar dalam format base64
        $imageData = $request->input('paraf_data');

        // Proses data base64 menjadi file gambar
        list($type, $imageData) = explode(';', $imageData);
        list(, $imageData)      = explode(',', $imageData);
        $imageData = base64_decode($imageData);

        // Buat nama file yang unik
        $filename = 'parafs/' . $user->registration_id . '.png';

        // Simpan file ke storage/app/public/parafs
        Storage::disk('public')->put($filename, $imageData);
        
        // Simpan atau update path di tabel signature_and_parafs
        SignatureAndParaf::updateOrCreate(
            ['registration_id' => $user->registration_id],
            [
                'user_id' => $user->id,
                'paraf_path' => $filename
            ]
        );

        return back()->with('success', 'Paraf berhasil disimpan.');
    }
}