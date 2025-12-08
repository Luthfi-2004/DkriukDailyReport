<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    // Tampilkan Form Profile
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user()
        ]);
    }

    // Update Profile
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'avatar_cropped' => 'nullable|string', // Ini string base64 dari Cropper.js
        ]);

        // 1. Update Nama
        $user->name = $request->name;

        // 2. Handle Upload Gambar (Jika ada perubahan)
        if ($request->filled('avatar_cropped')) {
            // Hapus avatar lama jika bukan default
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Proses Base64 Image
            $image_64 = $request->avatar_cropped; // data:image/png;base64,.....
            
            // Pisahkan header data dan isi file
            $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .png / .jpg
            $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
            $image = str_replace($replace, '', $image_64); 
            $image = str_replace(' ', '+', $image); 
            
            // Simpan File
            $imageName = 'avatars/' . Str::random(10) . '.' . $extension;
            Storage::disk('public')->put($imageName, base64_decode($image));

            $user->avatar = $imageName;
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}