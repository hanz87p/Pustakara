@extends('layouts.siswa')

@section('title', 'Riwayat Peminjaman')
@section('page-title', 'Riwayat Peminjaman Saya')

@section('content')

    {{-- Info Total --}}
    <div class="bg-white rounded-xl shadow-sm p-5 mb-5">
        <p class="text-sm text-slate-500">
            Total peminjaman: <strong class="text-slate-800">{{ $loans->total() }} buku</strong>
        </p>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 w-12">No</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Kode</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Judul Buku</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Tgl Pinjam</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Jatuh Tempo</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Tgl Kembali</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600">Status</th>
                        <th class="text-right px-4 py-3 font-semibold text-slate-600">Denda</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($loans as $i => $loan)
                        @php
                            $isLate = $loan->status === 'dipinjam' && \Carbon\Carbon::now()->gt($loan->tanggal_jatuh_tempo);
                        @endphp
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-500">{{ $loans->firstItem() + $i }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-emerald-700">{{ $loan->kode_pinjam }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $loan->book->judul }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $loan->tanggal_pinjam->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $loan->tanggal_jatuh_tempo->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ $loan->tanggal_kembali ? $loan->tanggal_kembali->format('d M Y') : '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($loan->status === 'dikembalikan')
                                    <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded-full">Dikembalikan</span>
                                @elseif ($isLate)
                                    <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-1 rounded-full">Terlambat</span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-700 text-xs font-semibold px-2 py-1 rounded-full">Dipinjam</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-slate-800">
                                {{ $loan->denda > 0 ? 'Rp ' . number_format($loan->denda, 0, ',', '.') : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-16 text-center text-slate-400">
                                <p class="text-5xl mb-3">📋</p>
                                <p class="text-sm">Kamu belum pernah meminjam buku.</p>
                                <a href="{{ route('siswa.catalog') }}"
                                   class="inline-block mt-3 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-5 py-2 rounded-lg">
                                    Jelajahi Katalog
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">
        {{ $loans->links() }}
    </div>

@endsection