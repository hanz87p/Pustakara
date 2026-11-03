<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ============ ADMIN ============
        User::create([
            'name' => 'Admin Perpustakaan',
            'email' => 'admin@pustakara.sch.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'nis_nip' => '198501012010011001',
        ]);

        // ============ SISWA ============
        $siswa = [
            ['nis' => '2024001', 'nama' => 'Ghozi Mumtaza Hanif', 'kelas' => 'XII RPL C', 'hp' => '081234567001'],
            ['nis' => '2024002', 'nama' => 'Budi Santoso',        'kelas' => 'XII RPL C', 'hp' => '081234567002'],
            ['nis' => '2024003', 'nama' => 'Siti Aminah',         'kelas' => 'XI RPL A',  'hp' => '081234567003'],
            ['nis' => '2024004', 'nama' => 'Andi Wijaya',         'kelas' => 'XI RPL B',  'hp' => '081234567004'],
            ['nis' => '2024005', 'nama' => 'Dewi Lestari',        'kelas' => 'X RPL A',   'hp' => '081234567005'],
        ];

        foreach ($siswa as $s) {
            $user = User::create([
                'name' => $s['nama'],
                'email' => $s['nis'] . '@siswa.pustakara.sch.id',
                'password' => Hash::make('siswa123'),
                'role' => 'siswa',
                'nis_nip' => $s['nis'],
            ]);

            Student::create([
                'user_id' => $user->id,
                'nis' => $s['nis'],
                'nama' => $s['nama'],
                'kelas' => $s['kelas'],
                'no_hp' => $s['hp'],
            ]);
        }
    }
}