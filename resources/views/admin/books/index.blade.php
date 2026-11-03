@extends('layouts.admin')

@section('title', 'Data Buku')
@section('page-title', 'Data Buku')

@section('content')

    {{-- Alert --}}
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

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-5">
        <p class="text-sm text-slate-500">
            Total: <strong>{{ $books->total() }}</strong> buku
        </p>
        <a href="{{ route('admin.books.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition inline-flex items-center gap-2 w-fit">
            + Tambah Buku
        </a>
    </div>

    {{-- Filter & Search --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-5">
        <form method="GET" action="{{ route('admin.books.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">

            {{-- Search --}}
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Buku</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Nomor Induk / Judul / Penulis..."
                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            {{-- Filter Kategori --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Kategori</label>
                <select name="category" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Status --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">Semua Status</option>
                    <option value="tersedia" {{ request('status') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="habis"    {{ request('status') === 'habis'    ? 'selected' : '' }}>Stok Habis</option>
                </select>
            </div>

            {{-- Tombol --}}
            <div class="md:col-span-4 flex gap-2">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                    🔍 Cari
                </button>
                <a href="{{ route('admin.books.index') }}"
                   class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold px-4 py-2 rounded-lg transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Tabel Buku --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 w-16">No</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Cover</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Nomor Induk</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Judul</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Penulis</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Kategori</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600">Stok</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600">Status</th>
                        <th class="text-right px-4 py-3 font-semibold text-slate-600 w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($books as $index => $book)
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-500">{{ $books->firstItem() + $index }}</td>
                            <td class="px-4 py-3">
                                @if ($book->cover_url)
                                    <img src="{{ $book->cover_url }}" class="w-10 h-14 object-cover rounded shadow-sm">
                                @else
                                    <div class="w-10 h-14 bg-slate-100 rounded flex items-center justify-center text-slate-400 text-xs">📖</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-mono text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">
                                    {{ $book->nomor_induk }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $book->judul }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $book->penulis }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $book->category->nama_kategori ?? '-' }}</td>
                            <td class="px-4 py-3 text-center font-semibold">{{ $book->stok }}</td>
                            <td class="px-4 py-3 text-center">
                                @if ($book->stok > 0)
                                    <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded-full">Tersedia</span>
                                @else
                                    <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-1 rounded-full">Habis</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.books.edit', $book) }}"
                                   class="text-blue-600 hover:underline text-xs font-semibold mr-3">Edit</a>
                                <form action="{{ route('admin.books.destroy', $book) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Yakin hapus buku {{ $book->judul }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-xs font-semibold">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-slate-400">
                                <p class="text-4xl mb-2">📚</p>
                                <p>Tidak ada buku ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-5">
        {{ $books->links() }}
    </div>

@endsection