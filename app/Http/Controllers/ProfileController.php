<?php

namespace App\Http\Controllers;

use App\Models\SignatureAndParaf;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Menangani update profil utama (termasuk foto profil).
     */
    public function update(Request $request)
    {
        // Ambil user yang sedang login
        $user = Auth::user();

        // Validasi input dari form
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'profile_picture' => 'nullable|image|mimes:jpg,gif,png|max:800', // Validasi untuk file foto
        ]);

        // Siapkan data untuk diupdate
        $updateData = $request->only('name', 'email', 'phone');

        // Cek jika ada file foto profil baru yang diunggah
        if ($request->hasFile('profile_picture')) {
            // Hapus foto lama jika sudah ada untuk menghemat storage
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // Simpan foto baru ke storage/app/public/profile-pictures
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $updateData['profile_picture'] = $path;
        }

        // Lakukan update pada data user
        $user->update($updateData);

        return back()->with('success', 'Profil berhasil diperbarui.');
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
                'user_id' => $user->id, // <--- ini yang ditambahkan
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
                'user_id' => $user->id, // <--- ini juga ditambahkan
                'paraf_path' => $filename
            ]
        );

        return back()->with('success', 'Paraf berhasil disimpan.');
    }
}