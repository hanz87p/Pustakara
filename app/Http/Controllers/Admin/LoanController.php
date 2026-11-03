<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    // ============ INDEX — Daftar peminjaman ============
    public function index(Request $request)
    {
        $query = Loan::with(['user', 'book'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_pinjam', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('nis_nip', 'like', "%{$search}%");
                  })
                  ->orWhereHas('book', function ($b) use ($search) {
                      $b->where('judul', 'like', "%{$search}%")
                        ->orWhere('nomor_induk', 'like', "%{$search}%");
                  });
            });
        }

        $loans = $query->paginate(10)->withQueryString();

        return view('admin.loans.index', compact('loans'));
    }

    // ============ CREATE — Form peminjaman ============
    public function create()
    {
        $todayLoans = Loan::with(['user', 'book'])
            ->whereDate('created_at', today())
            ->latest()
            ->get();

        return view('admin.loans.create', compact('todayLoans'));
    }

    // ============ SEARCH BOOK — AJAX ============
    public function searchBook(Request $request)
    {
        $book = Book::where('nomor_induk', $request->nomor_induk)->first();

        if (!$book) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'book'  => [
                'id'          => $book->id,
                'nomor_induk' => $book->nomor_induk,
                'judul'       => $book->judul,
                'penulis'     => $book->penulis,
                'penerbit'    => $book->penerbit,
                'stok'        => $book->stok,
                'rak'         => $book->rak,
                'cover_url'   => $book->cover_url,
            ],
        ]);
    }

    // ============ SEARCH STUDENT — AJAX ============
    public function searchStudent(Request $request)
    {
        $students = User::where('role', 'siswa')
            ->where(function ($q) use ($request) {
                $q->where('nis_nip', 'like', "%{$request->keyword}%")
                  ->orWhere('name', 'like', "%{$request->keyword}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'nis_nip']);

        return response()->json($students);
    }

    // ============ STORE — Simpan peminjaman ============
    public function store(Request $request)
    {
        $request->validate([
            'user_id'        => 'required|exists:users,id',
            'book_id'        => 'required|exists:books,id',
            'tanggal_pinjam' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
            $book = Book::findOrFail($request->book_id);

            // 1. Cek stok buku
            if ($book->stok <= 0) {
                DB::rollBack();
                return back()->with('error', "Stok buku \"{$book->judul}\" habis.")->withInput();
            }

            // 2. Cek duplikat (unique constraint): siswa sama + buku sama + masih dipinjam
            $existing = Loan::where('user_id', $request->user_id)
                ->where('book_id', $request->book_id)
                ->where('status', 'dipinjam')
                ->first();

            if ($existing) {
                DB::rollBack();
                return back()->with('error', 'Siswa ini masih meminjam buku yang sama. Data duplikat dicegah.')->withInput();
            }

            // 3. Cek batas maksimal 3 buku per siswa
            $activeLoans = Loan::where('user_id', $request->user_id)
                ->where('status', 'dipinjam')
                ->count();

            if ($activeLoans >= 3) {
                DB::rollBack();
                return back()->with('error', 'Siswa ini sudah meminjam 3 buku (batas maksimal). Harap kembalikan salah satu terlebih dahulu.')->withInput();
            }

            // 4. Generate kode pinjam
            $kode = 'PJM-' . date('Ymd') . '-' . str_pad(
                Loan::whereDate('created_at', today())->count() + 1,
                4,
                '0',
                STR_PAD_LEFT
            );

            // 5. Hitung jatuh tempo +7 hari
            $tanggalPinjam = Carbon::parse($request->tanggal_pinjam);
            $tanggalJatuhTempo = $tanggalPinjam->copy()->addDays(7);

            // 6. Simpan loan
            Loan::create([
                'kode_pinjam'         => $kode,
                'user_id'             => $request->user_id,
                'book_id'             => $request->book_id,
                'tanggal_pinjam'      => $tanggalPinjam,
                'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
                'status'              => 'dipinjam',
                'verified_by'         => auth()->id(),
            ]);

            // 7. Kurangi stok
            $book->decrement('stok');

            DB::commit();

            return redirect()->route('admin.loans.create')
                ->with('success', "Peminjaman \"{$book->judul}\" berhasil. Jatuh tempo: {$tanggalJatuhTempo->format('d M Y')}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan peminjaman: ' . $e->getMessage())->withInput();
        }
    }

    // ============ SHOW — Detail ============
    public function show(Loan $loan)
    {
        $loan->load(['user', 'book', 'verifier']);
        return view('admin.loans.show', compact('loan'));
    }

    // ============ DESTROY — Batalkan ============
    public function destroy(Loan $loan)
    {
        if ($loan->status !== 'dipinjam') {
            return back()->with('error', 'Hanya peminjaman aktif yang bisa dibatalkan.');
        }

        DB::beginTransaction();
        try {
            $loan->book->increment('stok');
            $loan->delete();
            DB::commit();

            return redirect()->route('admin.loans.index')
                ->with('success', 'Peminjaman berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan: ' . $e->getMessage());
        }
    }

    // ============ PENGEMBALIAN — Tampilkan form ============
    public function returnForm()
    {
        return view('admin.loans.return');
    }

    // ============ PENGEMBALIAN — Cari data ============
    public function searchLoan(Request $request)
    {
        $book = Book::where('nomor_induk', $request->nomor_induk)->first();

        if (!$book) {
            return response()->json(['found' => false, 'message' => 'Buku tidak ditemukan.']);
        }

        $loan = Loan::with(['user', 'book'])
            ->where('book_id', $book->id)
            ->where('status', 'dipinjam')
            ->latest()
            ->first();

        if (!$loan) {
            return response()->json([
                'found'   => false,
                'message' => "Buku \"{$book->judul}\" sedang tidak dipinjam.",
            ]);
        }

        $jatuhTempo = Carbon::parse($loan->tanggal_jatuh_tempo);
        $hariIni = Carbon::now();
        $hariTerlambat = 0;
        $denda = 0;

        if ($hariIni->greaterThan($jatuhTempo)) {
            $hariTerlambat = $hariIni->diffInDays($jatuhTempo);
            $denda = $hariTerlambat * 1000;
        }

        return response()->json([
            'found' => true,
            'loan' => [
                'id'                  => $loan->id,
                'kode_pinjam'         => $loan->kode_pinjam,
                'tanggal_pinjam'      => $loan->tanggal_pinjam->format('d M Y'),
                'tanggal_jatuh_tempo' => $loan->tanggal_jatuh_tempo->format('d M Y'),
                'hari_terlambat'      => $hariTerlambat,
                'denda'               => $denda,
                'denda_format'        => 'Rp ' . number_format($denda, 0, ',', '.'),
                'user' => [
                    'name'    => $loan->user->name,
                    'nis_nip' => $loan->user->nis_nip,
                ],
                'book' => [
                    'judul'       => $loan->book->judul,
                    'nomor_induk' => $loan->book->nomor_induk,
                ],
            ],
        ]);
    }

    // ============ PENGEMBALIAN — Proses ============
    public function processReturn(Request $request)
    {
        $request->validate([
            'loan_id' => 'required|exists:loans,id',
        ]);

        DB::beginTransaction();

        try {
            $loan = Loan::with('book')->findOrFail($request->loan_id);

            if ($loan->status !== 'dipinjam') {
                DB::rollBack();
                return back()->with('error', 'Peminjaman ini sudah dikembalikan sebelumnya.');
            }

            $jatuhTempo = Carbon::parse($loan->tanggal_jatuh_tempo);
            $hariIni = Carbon::now();
            $denda = 0;

            if ($hariIni->greaterThan($jatuhTempo)) {
                $hariTerlambat = $hariIni->diffInDays($jatuhTempo);
                $denda = $hariTerlambat * 1000;
            }

            $loan->update([
                'tanggal_kembali' => now(),
                'status'          => 'dikembalikan',
                'denda'           => $denda,
                'verified_by'     => auth()->id(),
            ]);

            $loan->book->increment('stok');

            DB::commit();

            $msg = "Buku \"{$loan->book->judul}\" berhasil dikembalikan.";
            if ($denda > 0) {
                $msg .= " Denda: Rp " . number_format($denda, 0, ',', '.');
            }

            return redirect()->route('admin.loans.return')
                ->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pengembalian: ' . $e->getMessage());
        }
    }
}