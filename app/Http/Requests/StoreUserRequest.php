<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Middleware sudah menjaga ini
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email', // Email gak boleh kembar
            'password' => 'required|string|min:6', // Minimal 6 karakter
            'role' => 'required|in:' . User::ROLE_ADMIN . ',' . User::ROLE_USER, // Hanya boleh bikin Admin/User
        ];
    }
}