@extends('layouts.admin')

@section('title', 'Kategori Buku')
@section('page-title', 'Kategori Buku')

@section('content')

    {{-- Alert --}}
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 mb-4 flex justify-between items-center">
            <span>✅ {{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-700">✕</button>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 mb-4 flex justify-between items-center">
            <span>⚠️ {{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-red-700">✕</button>
        </div>
    @endif

    {{-- Header Aksi --}}
    <div class="flex justify-between items-center mb-5">
        <p class="text-sm text-slate-500">Total: {{ $categories->total() }} kategori</p>
        <a href="{{ route('admin.categories.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
            + Tambah Kategori
        </a>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600 w-16">No</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600">Nama Kategori</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600">Slug</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600">Jumlah Buku</th>
                    <th class="text-right px-4 py-3 font-semibold text-slate-600 w-40">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $index => $category)
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="px-4 py-3 text-slate-500">
                            {{ $categories->firstItem() + $index }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-slate-800">
                            {{ $category->nama_kategori }}
                        </td>
                        <td class="px-4 py-3 text-slate-500 font-mono text-xs">
                            {{ $category->slug }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-1 rounded-full">
                                {{ $category->books_count }} buku
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.categories.edit', $category) }}"
                               class="text-blue-600 hover:underline text-xs font-semibold mr-3">Edit</a>

                            <form action="{{ route('admin.categories.destroy', $category) }}"
                                  method="POST" class="inline"
                                  onsubmit="return confirm('Yakin hapus kategori {{ $category->nama_kategori }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-600 hover:underline text-xs font-semibold">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-slate-400">
                            <p class="text-4xl mb-2">📂</p>
                            <p>Belum ada kategori. Klik <strong>+ Tambah Kategori</strong> untuk membuat.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-5">
        {{ $categories->links() }}
    </div>

@endsection