<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['user', 'book'])->latest()->paginate(10);
        return view('admin.bookings.index', compact('bookings'));
    }

    // Proses ubah status
    public function update(Request $request, Booking $booking)
    {
        $request->validate(['status' => 'required|in:disetujui,ditolak']);

        $booking->update(['status' => $request->status]);

        $msg = $request->status === 'disetujui' ? 'Pesanan disetujui.' : 'Pesanan ditolak.';
        return back()->with('success', $msg);
    }

    // Konversi pesanan jadi peminjaman (opsional, biar cepet)
    public function convertToLoan(Booking $booking)
    {
        if ($booking->status !== 'menunggu') {
            return back()->with('error', 'Hanya pesanan berstatus menunggu yang bisa diproses.');
        }

        DB::beginTransaction();
        try {
            // 1. Buat transaksi pinjam
            $loan = Loan::create([
                'kode_pinjam' => 'PJM-' . date('Ymd') . '-' . str_pad(Loan::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT),
                'user_id' => $booking->user_id,
                'book_id' => $booking->book_id,
                'tanggal_pinjam' => Carbon::now(),
                'tanggal_jatuh_tempo' => Carbon::now()->addDays(7),
                'status' => 'dipinjam',
                'verified_by' => auth()->id(),
            ]);

            // 2. Kurangi stok
            $booking->book->decrement('stok');

            // 3. Update status pesanan
            $booking->update(['status' => 'disetujui']);

            DB::commit();
            return redirect()->route('admin.loans.index')->with('success', 'Pesanan diproses menjadi peminjaman.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }
}