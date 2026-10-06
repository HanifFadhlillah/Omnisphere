<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

// Membuat 3 akun demo, satu untuk tiap role di SRS (Tabel 2.2 Karakteristik Pengguna)
// Password default: password123 — WAJIB diganti sebelum dipakai sungguhan.
class RoleUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@pmo.pjt2.local'],
            [
                'name' => 'Administrator PMO',
                'password' => Hash::make('password123'),
                'role' => 'admin_pmo',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@pmo.pjt2.local'],
            [
                'name' => 'Staff PMO',
                'password' => Hash::make('password123'),
                'role' => 'staff_pmo',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'viewer@pmo.pjt2.local'],
            [
                'name' => 'Manajemen / Viewer',
                'password' => Hash::make('password123'),
                'role' => 'viewer',
                'is_active' => true,
            ]
        );
    }
}
