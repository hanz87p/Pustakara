<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nis',
        'nama',
        'kelas',
        'no_hp',
    ];

    // 1 siswa milik 1 user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}