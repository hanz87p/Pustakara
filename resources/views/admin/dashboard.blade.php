@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    {{-- Welcome --}}
    <div class="mb-6">
        <h2 class="text-xl font-bold text-slate-800">Selamat datang, {{ auth()->user()->name }}! 👋</h2>
        <p class="text-sm text-slate-500">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>

    {{-- ============ STATISTIK CARDS ============ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

        {{-- Total Buku --}}
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">Total Buku</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1">{{ $stats['total_buku'] }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $stats['total_stok'] }} stok tersedia</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xl">
                    📚
                </div>
            </div>
        </div>

        {{-- Dipinjam --}}
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">Sedang Dipinjam</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $stats['total_dipinjam'] }}</p>
                    <p class="text-xs text-slate-400 mt-1">Transaksi aktif</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center text-xl">
                    ⏳
                </div>
            </div>
        </div>

        {{-- Terlambat --}}
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">Terlambat</p>
                    <p class="text-3xl font-bold text-red-600 mt-1">{{ $stats['total_terlambat'] }}</p>
                    <p class="text-xs text-slate-400 mt-1">Perlu ditindaklanjuti</p>
                </div>
                <div class="w-12 h-12 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-xl">
                    ⚠️
                </div>
            </div>
        </div>

        {{-- Total Siswa --}}
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">Total Siswa</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1">{{ $stats['total_siswa'] }}</p>
                    <p class="text-xs text-slate-400 mt-1">Anggota aktif</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-xl">
                    👥
                </div>
            </div>
        </div>

    </div>

    {{-- ============ GRAFIK + TOP BUKU ============ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-8">

        {{-- Grafik Peminjaman --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-bold text-slate-800 mb-5">📊 Grafik Peminjaman (6 Bulan Terakhir)</h3>

            <div class="flex items-end justify-between h-48 gap-3">
                @foreach ($chartData as $data)
                    @php
                        $height = $maxChart > 0 ? ($data['value'] / $maxChart) * 100 : 0;
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-2">
                        <div class="text-xs font-semibold text-slate-600">{{ $data['value'] }}</div>
                        <div class="w-full bg-gradient-to-t from-blue-500 to-blue-400 rounded-t-lg transition-all hover:from-blue-600"
                             style="height: {{ max($height, 3) }}%"></div>
                        <div class="text-xs text-slate-500 whitespace-nowrap">{{ $data['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Top 5 Buku Populer --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-bold text-slate-800 mb-5">🔥 Buku Terpopuler</h3>

            @if ($popularBooks->count() > 0)
                <div class="space-y-3">
                    @foreach ($popularBooks as $i => $book)
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold shrink-0">
                                {{ $i + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-800 truncate">{{ $book->judul }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ $book->penulis }}</p>
                            </div>
                            <div class="text-xs font-semibold text-blue-600 shrink-0">
                                {{ $book->loans_count }}x
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-slate-400 text-center py-4">Belum ada data.</p>
            @endif
        </div>

    </div>

    {{-- ============ TABEL ============ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Buku Terbaru Dipinjam --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center">
                <h3 class="font-bold text-slate-800">📖 Buku Terbaru Dipinjam</h3>
                <a href="{{ route('admin.loans.index') }}" class="text-xs text-blue-600 hover:underline">Lihat Semua</a>
            </div>

            @if ($recentLoans->count() > 0)
                <table class="w-full text-sm">
                    <tbody>
                        @foreach ($recentLoans as $loan)
                            <tr class="border-b border-slate-100 hover:bg-slate-50">
                                <td class="px-6 py-3">
                                    <p class="font-semibold text-slate-800 text-sm truncate max-w-[200px]">{{ $loan->book->judul }}</p>
                                    <p class="text-xs text-slate-500">{{ $loan->user->name }}</p>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    @if ($loan->status === 'dikembalikan')
                                        <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">Dikembalikan</span>
                                    @else
                                        <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-1 rounded-full">Dipinjam</span>
                                    @endif
                                    <p class="text-xs text-slate-400 mt-1">{{ $loan->created_at->diffForHumans() }}</p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center py-8 text-slate-400 text-sm">Belum ada peminjaman.</div>
            @endif
        </div>

        {{-- Peminjaman Terlambat --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center">
                <h3 class="font-bold text-slate-800">⚠️ Peminjaman Terlambat</h3>
                <a href="{{ route('admin.loans.index', ['status' => 'dipinjam']) }}" class="text-xs text-blue-600 hover:underline">Lihat Semua</a>
            </div>

            @if ($lateLoans->count() > 0)
                <table class="w-full text-sm">
                    <tbody>
                        @foreach ($lateLoans as $loan)
                            @php
                                $hariTelat = \Carbon\Carbon::parse($loan->tanggal_jatuh_tempo)->diffInDays(now());
                            @endphp
                            <tr class="border-b border-slate-100 hover:bg-red-50">
                                <td class="px-6 py-3">
                                    <p class="font-semibold text-slate-800 text-sm truncate max-w-[200px]">{{ $loan->book->judul }}</p>
                                    <p class="text-xs text-slate-500">{{ $loan->user->name }} • {{ $loan->user->nis_nip }}</p>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-1 rounded-full">
                                        +{{ $hariTelat }} hari
                                    </span>
                                    <p class="text-xs text-slate-400 mt-1">Jatuh tempo: {{ $loan->tanggal_jatuh_tempo->format('d M') }}</p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center py-8 text-slate-400 text-sm">
                    ✅ Tidak ada keterlambatan. Mantap!
                </div>
            @endif
        </div>

    </div>

@endsection