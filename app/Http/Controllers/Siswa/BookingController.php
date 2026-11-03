<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Book;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    // Proses simpan pesanan
    public function store(Request $request, Book $book)
    {
        // Cek apakah stok ada
        if ($book->stok <= 0) {
            return back()->with('error', 'Maaf, stok buku ini sedang habis.');
        }

        // Cek apakah siswa sudah pernah pesan buku ini dan masih menunggu
        $existing = Booking::where('user_id', auth()->id())
            ->where('book_id', $book->id)
            ->where('status', 'menunggu')
            ->first();

        if ($existing) {
            return back()->with('error', 'Kamu sudah memesan buku ini. Harap tunggu konfirmasi admin.');
        }

        Booking::create([
            'user_id' => auth()->id(),
            'book_id' => $book->id,
            'tanggal_pesan' => Carbon::now(),
            'status' => 'menunggu',
            'catatan' => $request->catatan,
        ]);

        return back()->with('success', 'Buku berhasil dipesan! Silakan datang ke perpustakaan untuk mengambilnya.');
    }
}