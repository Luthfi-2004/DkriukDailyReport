<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $service)
    {
        $this->userService = $service;
    }

    public function index()
    {
        // Tampilkan semua user KECUALI Super Admin (Biar gak hapus diri sendiri/sesama dewa)
        $users = User::where('role', '!=', User::ROLE_SUPER_ADMIN)->latest()->get();
        return view('dashboard.super_admin.users.index', compact('users'));
    }

    public function store(StoreUserRequest $request)
    {
        $this->userService->createUser($request->validated());
        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        // Validasi manual dikit buat update
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required',
            'password' => 'nullable|min:6'
        ]);

        $this->userService->updateUser($user, $data);
        return back()->with('success', 'Data user diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'User dihapus dari sistem.');
    }
}