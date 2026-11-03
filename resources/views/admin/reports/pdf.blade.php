<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi Pustakara</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 20px; }
        .header p { margin: 4px 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #999; padding: 6px 8px; text-align: left; }
        th { background: #eee; font-weight: bold; }
        .summary { margin-bottom: 15px; padding: 10px; background: #f5f5f5; border-radius: 4px; }
        .summary div { display: inline-block; margin-right: 20px; }
        .summary strong { font-size: 14px; }
        .text-right { text-align: right; }
        @media print {
            body { padding: 0; }
            @page { margin: 15mm; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h1>LAPORAN TRANSAKSI PERPUSTAKAAN</h1>
        <p>Sistem Informasi Perpustakaan — Pustakara</p>
        <p>SMK Negeri 5 Surakarta</p>
        <p>Periode: {{ $startDate->format('d M Y') }} — {{ $endDate->format('d M Y') }}</p>
    </div>

    <div class="summary">
        <div>Total Transaksi: <strong>{{ $summary['total_pinjam'] }}</strong></div>
        <div>Dikembalikan: <strong>{{ $summary['total_kembali'] }}</strong></div>
        <div>Masih Dipinjam: <strong>{{ $summary['total_dipinjam'] }}</strong></div>
        <div>Total Denda: <strong>Rp {{ number_format($summary['total_denda'], 0, ',', '.') }}</strong></div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Kode</th>
                <th>Tanggal</th>
                <th>Siswa</th>
                <th>Buku</th>
                <th>Status</th>
                <th class="text-right">Denda</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($loans as $i => $loan)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $loan->kode_pinjam }}</td>
                    <td>{{ $loan->created_at->format('d/m/Y') }}</td>
                    <td>{{ $loan->user->name }} ({{ $loan->user->nis_nip }})</td>
                    <td>{{ $loan->book->judul }}</td>
                    <td>{{ ucfirst($loan->status) }}</td>
                    <td class="text-right">Rp {{ number_format($loan->denda, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align: center;">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p style="margin-top: 20px; text-align: right; font-size: 10px; color: #777;">
        Dicetak: {{ now()->format('d M Y H:i') }}
    </p>

</body>
</html>