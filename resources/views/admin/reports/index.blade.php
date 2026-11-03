@extends('layouts.admin')

@section('title', 'Laporan Transaksi')
@section('page-title', 'Laporan Transaksi')

@section('content')

    {{-- Filter --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-5">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">Semua</option>
                    <option value="dipinjam"     {{ request('status') === 'dipinjam'     ? 'selected' : '' }}>Dipinjam</option>
                    <option value="dikembalikan" {{ request('status') === 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg flex-1">
                    🔍 Filter
                </button>
            </div>
        </form>

        {{-- Tombol Cetak --}}
        <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-slate-200">
            <a href="{{ route('admin.reports.pdf', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}"
               target="_blank"
               class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-lg">
                🖨️ Cetak PDF
            </a>
            <a href="{{ route('admin.reports.excel', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}"
               class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-lg">
                📊 Export Excel (CSV)
            </a>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
            <p class="text-xs text-slate-500 uppercase font-semibold">Total Transaksi</p>
            <p class="text-2xl font-bold text-slate-800">{{ $summary['total_pinjam'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-yellow-500">
            <p class="text-xs text-slate-500 uppercase font-semibold">Masih Dipinjam</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $summary['total_dipinjam'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
            <p class="text-xs text-slate-500 uppercase font-semibold">Dikembalikan</p>
            <p class="text-2xl font-bold text-green-600">{{ $summary['total_kembali'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-red-500">
            <p class="text-xs text-slate-500 uppercase font-semibold">Total Denda</p>
            <p class="text-2xl font-bold text-red-600">Rp {{ number_format($summary['total_denda'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Tabel Laporan --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">No</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Kode</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Tanggal</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Siswa</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Buku</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600">Status</th>
                        <th class="text-right px-4 py-3 font-semibold text-slate-600">Denda</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($loans as $i => $loan)
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-500">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-blue-700">{{ $loan->kode_pinjam }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $loan->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <div class="font-semibold">{{ $loan->user->name }}</div>
                                <div class="text-xs text-slate-500">{{ $loan->user->nis_nip }}</div>
                            </td>
                            <td class="px-4 py-3">{{ $loan->book->judul }}</td>
                            <td class="px-4 py-3 text-center">
                                @if ($loan->status === 'dikembalikan')
                                    <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">Dikembalikan</span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-1 rounded-full">Dipinjam</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-semibold">
                                Rp {{ number_format($loan->denda, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-12 text-slate-400">Tidak ada data pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection