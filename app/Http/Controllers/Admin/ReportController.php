<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    // ============ INDEX — Halaman Laporan ============
    public function index(Request $request)
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)
            : Carbon::now()->startOfMonth();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)
            : Carbon::now();

        $query = Loan::with(['user', 'book'])
            ->whereBetween('created_at', [
                $startDate->copy()->startOfDay(),
                $endDate->copy()->endOfDay(),
            ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $loans = $query->latest()->get();

        $summary = [
            'total_pinjam'   => $loans->count(),
            'total_kembali'  => $loans->where('status', 'dikembalikan')->count(),
            'total_dipinjam' => $loans->where('status', 'dipinjam')->count(),
            'total_denda'    => $loans->sum('denda'),
        ];

        return view('admin.reports.index', compact('loans', 'summary', 'startDate', 'endDate'));
    }

    // ============ EXPORT PDF ============
    public function exportPdf(Request $request)
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)
            : Carbon::now()->startOfMonth();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)
            : Carbon::now();

        $loans = Loan::with(['user', 'book'])
            ->whereBetween('created_at', [
                $startDate->copy()->startOfDay(),
                $endDate->copy()->endOfDay(),
            ])
            ->latest()
            ->get();

        $summary = [
            'total_pinjam'   => $loans->count(),
            'total_kembali'  => $loans->where('status', 'dikembalikan')->count(),
            'total_dipinjam' => $loans->where('status', 'dipinjam')->count(),
            'total_denda'    => $loans->sum('denda'),
        ];

        return view('admin.reports.pdf', compact('loans', 'summary', 'startDate', 'endDate'));
    }

    // ============ EXPORT EXCEL (CSV) ============
    public function exportExcel(Request $request)
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)
            : Carbon::now()->startOfMonth();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)
            : Carbon::now();

        $loans = Loan::with(['user', 'book'])
            ->whereBetween('created_at', [
                $startDate->copy()->startOfDay(),
                $endDate->copy()->endOfDay(),
            ])
            ->latest()
            ->get();

        $filename = 'laporan-transaksi-' . now()->format('Ymd-His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($loans) {
            $file = fopen('php://output', 'w');

            // Tambah BOM supaya Excel bisa baca UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header CSV
            fputcsv($file, [
                'No',
                'Kode Peminjaman',
                'Tanggal',
                'NIS',
                'Nama Siswa',
                'Judul Buku',
                'Nomor Induk',
                'Status',
                'Denda',
            ]);

            // Isi data
            foreach ($loans as $i => $loan) {
                fputcsv($file, [
                    $i + 1,
                    $loan->kode_pinjam,
                    $loan->created_at->format('d/m/Y H:i'),
                    $loan->user->nis_nip,
                    $loan->user->name,
                    $loan->book->judul,
                    $loan->book->nomor_induk,
                    ucfirst($loan->status),
                    $loan->denda,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}