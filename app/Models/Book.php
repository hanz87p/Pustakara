<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;      // ← TAMBAH INI

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_induk',
        'judul',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'stok',
        'stok_total',
        'category_id',
        'rak',
        'cover',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function isAvailable(): bool
    {
        return $this->stok > 0;
    }

    // ============ TAMBAHAN BARU (7.3) ============
    // Accessor: ambil URL cover buku
    public function getCoverUrlAttribute()
    {
        if ($this->cover && Storage::disk('public')->exists($this->cover)) {
            return asset('storage/' . $this->cover);
        }
        return null;
    }
}