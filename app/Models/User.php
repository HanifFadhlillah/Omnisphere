<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Role: 'admin_pmo' | 'staff_pmo' | 'viewer' — lihat Tabel 3.9 SRS untuk matriks hak akses
    public function isAdmin(): bool
    {
        return $this->role === 'admin_pmo';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff_pmo';
    }

    public function isViewer(): bool
    {
        return $this->role === 'viewer';
    }

    // Admin PMO dan Staff PMO sama-sama boleh kelola data (lihat Tabel 3.9)
    public function canManage(): bool
    {
        return in_array($this->role, ['admin_pmo', 'staff_pmo']);
    }
}
