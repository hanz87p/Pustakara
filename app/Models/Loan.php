<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_pinjam',
        'user_id',
        'book_id',
        'tanggal_pinjam',
        'tanggal_jatuh_tempo',
        'tanggal_kembali',
        'status',
        'denda',
        'verified_by',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_kembali' => 'date',
    ];

    // ============ RELASI ============

    // 1 loan milik 1 user (siswa)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 1 loan milik 1 buku
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    // 1 loan diverifikasi oleh 1 admin
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // ============ HELPER ============

    // Hitung denda keterlambatan
    public function hitungDenda(): int
    {
        if ($this->status === 'dikembalikan') {
            return $this->denda;
        }

        $jatuhTempo = Carbon::parse($this->tanggal_jatuh_tempo);
        $hariIni = Carbon::now();

        if ($hariIni->greaterThan($jatuhTempo)) {
            $hariTerlambat = $hariIni->diffInDays($jatuhTempo);
            return $hariTerlambat * 1000; // Rp 1.000 / hari
        }

        return 0;
    }

    // Cek apakah terlambat
    public function isTerlambat(): bool
    {
        return Carbon::now()->greaterThan($this->tanggal_jatuh_tempo)
            && $this->status === 'dipinjam';
    }
}