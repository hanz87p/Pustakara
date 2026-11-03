<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nis_nip',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ============ RELASI ============

    // 1 user punya 1 data siswa (jika role = siswa)
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    // 1 user bisa punya banyak peminjaman
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    // 1 admin bisa verifikasi banyak loan
    public function verifiedLoans()
    {
        return $this->hasMany(Loan::class, 'verified_by');
    }

    // ============ HELPER ============

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }
}