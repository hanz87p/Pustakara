<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ============ STATISTIK UTAMA ============
        $stats = [
            'total_buku'      => Book::count(),
            'total_stok'      => Book::sum('stok'),
            'total_dipinjam'  => Loan::where('status', 'dipinjam')->count(),
            'total_siswa'     => Student::count(),
            'total_terlambat' => Loan::where('status', 'dipinjam')
                                    ->where('tanggal_jatuh_tempo', '<', now())
                                    ->count(),
        ];

        // ============ BUKU TERBARU DIPINJAM ============
        $recentLoans = Loan::with(['user', 'book'])
            ->latest()
            ->limit(5)
            ->get();

        // ============ GRAFIK: PEMINJAMAN 6 BULAN TERAKHIR ============
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = Loan::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $chartData[] = [
                'label' => $month->format('M Y'),
                'value' => $count,
            ];
        }

        // Max value untuk scaling chart
        $maxChart = collect($chartData)->max('value') ?: 1;

        // ============ TOP 5 BUKU POPULER ============
        $popularBooks = Book::withCount('loans')
            ->orderBy('loans_count', 'desc')
            ->limit(5)
            ->get();

        // ============ PEMINJAMAN TERLAMBAT ============
        $lateLoans = Loan::with(['user', 'book'])
            ->where('status', 'dipinjam')
            ->where('tanggal_jatuh_tempo', '<', now())
            ->latest('tanggal_jatuh_tempo')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentLoans',
            'chartData',
            'maxChart',
            'popularBooks',
            'lateLoans'
        ));
    }
}