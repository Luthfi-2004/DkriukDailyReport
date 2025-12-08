<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // --- 1. DEFINISI KONSTANTA (Agar tidak ada Typo di masa depan) ---
    // Ini adalah "Kamus Bahasa" untuk role.
    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',// Hati-hati dengan ini di Controller register!
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- 2. HELPER METHODS (Encapsulation Logic) ---
    // Ini seperti method 'cetakBarang()' atau 'nyalakanPemanas()'.
    // Kita bungkus logika pengecekan di dalam Model, bukan di Controller.

    /**
     * Cek apakah user memiliki role tertentu.
     * Penggunaan: $user->hasRole('admin')
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Cek apakah user adalah Super Admin.
     * Penggunaan: $user->isSuperAdmin()
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    /**
     * Cek apakah user adalah Admin.
     * Penggunaan: $user->isAdmin()
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }
    
     /**
     * Cek apakah user adalah User Biasa.
     * Penggunaan: $user->isUser()
     */
    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }
    public function isManagerial(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin();
    }
}