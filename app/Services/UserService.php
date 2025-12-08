<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Membuat user baru dengan password yang sudah di-hash.
     */
    public function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']), // Enkripsi di sini
            'role' => $data['role'],
        ]);
    }

    /**
     * Update user (Skip password jika kosong).
     */
    public function updateUser(User $user, array $data): bool
    {
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ];

        // Hanya update password jika diisi admin (fitur reset password)
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        return $user->update($updateData);
    }
}