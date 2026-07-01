<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail; // 1. Import ini
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Translatable\HasTranslations;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, HasTranslations, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = ['name', 'email', 'password', 'role', 'profile_photo', 'timezone', 'locale', 'preferences'];

    protected $hidden = ['password', 'remember_token'];

    // Tambahkan casting
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'preferences' => 'array', // PENTING: Cast ke array
        'timezone' => 'string',
        'locale' => 'string',
    ];

    // Helper: apakah user punya akses panel admin (super_admin atau admin)
    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin'], true);
    }

    /**
     * Helper untuk mengambil inisial nama (Untuk profile_photo).
     * Contoh: "Budi Santoso" -> "BS", "Admin" -> "AD"
     */
    public function initials()
    {
        $words = explode(' ', $this->name);

        // Jika nama terdiri dari 2 kata atau lebih (Contoh: Budi Santoso)
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1).substr(end($words), 0, 1));
        }

        // Jika hanya 1 kata (Contoh: Admin), ambil 2 huruf pertama
        return strtoupper(substr($this->name, 0, 2));
    }
}
