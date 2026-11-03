@extends('layouts.siswa')

@section('title', $book->judul)
@section('page-title', 'Detail Buku')

@section('content')

    {{-- Alert Sukses/Error --}}
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 mb-4 flex justify-between items-center">
            <span>✅ {{ session('success') }}</span>
            <button onclick="this.parentElement.remove()">✕</button>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 mb-4 flex justify-between items-center">
            <span>⚠️ {{ session('error') }}</span>
            <button onclick="this.parentElement.remove()">✕</button>
        </div>
    @endif

    <div class="mb-5">
        <a href="{{ route('siswa.catalog') }}" class="text-sm text-emerald-600 hover:underline">← Kembali ke Katalog</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">

            {{-- Cover --}}
            <div>
                <div class="aspect-[3/4] bg-slate-100 rounded-lg overflow-hidden">
                    @if ($book->cover_url)
                        <img src="{{ $book->cover_url }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-8xl text-slate-300">📖</div>
                    @endif
                </div>
            </div>

            {{-- Info --}}
            <div class="md:col-span-2">
                <h1 class="text-2xl font-bold text-slate-800 mb-2">{{ $book->judul }}</h1>
                <p class="text-slate-500 mb-4">oleh <strong>{{ $book->penulis }}</strong></p>

                {{-- Badge Status --}}
                <div class="mb-4">
                    @if ($book->stok > 0)
                        <span class="bg-green-100 text-green-700 text-sm font-semibold px-3 py-1 rounded-full">
                            ✅ Tersedia ({{ $book->stok }} buku)
                        </span>
                    @else
                        <span class="bg-red-100 text-red-700 text-sm font-semibold px-3 py-1 rounded-full">
                            ❌ Stok Habis
                        </span>
                    @endif
                </div>

                {{-- Detail Tabel --}}
                <div class="border-t border-slate-200 py-4">
                    <table class="w-full text-sm">
                        <tr class="border-b border-slate-100">
                            <td class="py-2 text-slate-500 w-40">Nomor Induk</td>
                            <td class="py-2 font-mono font-semibold text-emerald-700">{{ $book->nomor_induk }}</td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-2 text-slate-500">Penerbit</td>
                            <td class="py-2">{{ $book->penerbit }}</td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-2 text-slate-500">Tahun Terbit</td>
                            <td class="py-2">{{ $book->tahun_terbit ?? '-' }}</td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-2 text-slate-500">Kategori</td>
                            <td class="py-2">{{ $book->category->nama_kategori ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-slate-500">Rak / Lokasi</td>
                            <td class="py-2">{{ $book->rak ?? '-' }}</td>
                        </tr>
                    </table>
                </div>

                {{-- Info Peminjaman --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-4">
                    <p class="text-sm text-blue-800">
                        ℹ️ <strong>Cara Peminjaman:</strong> Kamu bisa memesan buku ini terlebih dahulu melalui tombol di bawah. Setelah itu, datang ke perpustakaan dan tunjukkan <strong>Kartu Pelajar</strong> ke petugas untuk mengambilnya.
                    </p>
                </div>

                {{-- ========================================== --}}
                {{-- FORM PESAN BUKU (STEP 5) --}}
                {{-- ========================================== --}}
                @if ($book->stok > 0)
                    <form action="{{ route('siswa.booking.store', $book) }}" method="POST" class="mt-6 bg-emerald-50 border border-emerald-200 p-5 rounded-xl">
                        @csrf
                        <h3 class="font-bold text-emerald-800 mb-3">🛒 Pesan Buku Ini</h3>
                        
                        <div class="mb-3">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Catatan (Opsional)</label>
                            <input type="text" name="catatan" placeholder="Contoh: Ambil besok pagi jam 10" 
                                   class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                            @error('catatan')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 rounded-lg transition">
                            Pesan Sekarang
                        </button>
                    </form>
                @else
                    <div class="mt-6 bg-slate-100 border border-slate-200 p-5 rounded-xl text-center">
                        <p class="text-slate-500 font-semibold">Maaf, stok buku ini sedang habis. Kamu belum bisa memesan.</p>
                    </div>
                @endif

            </div>
        </div>
    </div>

@endsection