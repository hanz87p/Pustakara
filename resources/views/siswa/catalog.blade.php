@extends('layouts.siswa')

@section('title', 'Katalog Buku')
@section('page-title', 'Katalog Buku')

@section('content')

    {{-- Hero/Welcome --}}
    <div class="bg-gradient-to-r from-emerald-500 to-emerald-700 rounded-xl p-6 mb-6 text-white">
        <h2 class="text-xl font-bold">Halo, {{ auth()->user()->name }}! 👋</h2>
        <p class="text-emerald-50 text-sm mt-1">Temukan buku favoritmu di sini. Total {{ $books->total() }} buku tersedia.</p>
    </div>

    {{-- Filter & Search --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('siswa.catalog') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">

            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Buku</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Nomor Induk / Judul / Penulis..."
                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Kategori</label>
                <select name="category" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg flex-1">
                    🔍 Cari
                </button>
                <a href="{{ route('siswa.catalog') }}"
                   class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold px-4 py-2 rounded-lg">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Grid Buku --}}
    @if ($books->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @foreach ($books as $book)
                <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition flex flex-col">

                    {{-- Cover --}}
                    <div class="aspect-[3/4] bg-slate-100 relative">
                        @if ($book->cover_url)
                            <img src="{{ $book->cover_url }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-6xl text-slate-300">📖</div>
                        @endif

                        {{-- Badge Stok --}}
                        <span class="absolute top-2 right-2 text-xs font-semibold px-2 py-1 rounded-full
                                     {{ $book->stok > 0 ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                            {{ $book->stok > 0 ? 'Tersedia' : 'Habis' }}
                        </span>
                    </div>

                    {{-- Info --}}
                    <div class="p-3 flex-1 flex flex-col">
                        <h3 class="font-semibold text-slate-800 text-sm line-clamp-2 mb-1" title="{{ $book->judul }}">
                            {{ $book->judul }}
                        </h3>
                        <p class="text-xs text-slate-500 mb-2">{{ $book->penulis }}</p>

                        <div class="mt-auto flex items-center justify-between text-xs text-slate-500">
                            <span class="bg-slate-100 px-2 py-0.5 rounded">{{ $book->category->nama_kategori ?? '-' }}</span>
                            <span class="font-semibold">Stok: {{ $book->stok }}</span>
                        </div>
                    </div>

                    {{-- Tombol --}}
                    <div class="px-3 pb-3">
                        <a href="{{ route('siswa.book.detail', $book) }}"
                           class="block w-full text-center bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold py-2 rounded-lg transition">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $books->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm py-16 text-center">
            <p class="text-6xl mb-4">📚</p>
            <p class="text-slate-500">Tidak ada buku ditemukan.</p>
            <p class="text-sm text-slate-400 mt-1">Coba ubah kata kunci atau filter.</p>
        </div>
    @endif

@endsection