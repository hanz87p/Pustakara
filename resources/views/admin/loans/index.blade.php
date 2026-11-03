@extends('layouts.admin')

@section('title', 'Data Peminjaman')
@section('page-title', 'Data Peminjaman')

@section('content')

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 mb-4">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 mb-4">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-5">
        <p class="text-sm text-slate-500">
            Total: <strong>{{ $loans->total() }}</strong> transaksi
        </p>
        <a href="{{ route('admin.loans.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition w-fit">
            + Peminjaman Baru
        </a>
    </div>

    {{-- Filter & Search --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-5">
        <form method="GET" action="{{ route('admin.loans.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Kode / Nama / NIS / Judul / Nomor Induk..."
                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">Semua Status</option>
                    <option value="dipinjam"     {{ request('status') === 'dipinjam'     ? 'selected' : '' }}>Dipinjam</option>
                    <option value="dikembalikan" {{ request('status') === 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                </select>
            </div>
            <div class="md:col-span-3 flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg">🔍 Cari</button>
                <a href="{{ route('admin.loans.index') }}"
                   class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold px-4 py-2 rounded-lg">Reset</a>
            </div>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Kode</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Siswa</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Buku</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Tgl Pinjam</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Jatuh Tempo</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600">Status</th>
                        <th class="text-right px-4 py-3 font-semibold text-slate-600 w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($loans as $loan)
                        @php
                            $isLate = $loan->status === 'dipinjam' && \Carbon\Carbon::now()->gt($loan->tanggal_jatuh_tempo);
                        @endphp
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="px-4 py-3 font-mono text-xs text-blue-700">{{ $loan->kode_pinjam }}</td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-800">{{ $loan->user->name }}</div>
                                <div class="text-xs text-slate-500">{{ $loan->user->nis_nip }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-800">{{ $loan->book->judul }}</div>
                                <div class="text-xs text-slate-500 font-mono">{{ $loan->book->nomor_induk }}</div>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $loan->tanggal_pinjam->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $loan->tanggal_jatuh_tempo->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-center">
                                @if ($loan->status === 'dikembalikan')
                                    <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded-full">Dikembalikan</span>
                                @elseif ($isLate)
                                    <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-1 rounded-full">Terlambat</span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-700 text-xs font-semibold px-2 py-1 rounded-full">Dipinjam</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if ($loan->status === 'dipinjam')
                                    <form action="{{ route('admin.loans.destroy', $loan) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Batalkan peminjaman ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline text-xs font-semibold">Batalkan</button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                                <p class="text-4xl mb-2">📤</p>
                                <p>Belum ada transaksi peminjaman.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">{{ $loans->links() }}</div>

@endsection