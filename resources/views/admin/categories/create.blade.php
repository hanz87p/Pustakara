@extends('layouts.admin')

@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori')

@section('content')

    <div class="max-w-xl">
        <div class="bg-white rounded-xl shadow-sm p-6">

            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf

                <div class="mb-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">
                        Nama Kategori <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_kategori" value="{{ old('nama_kategori') }}"
                           placeholder="Contoh: Fiksi, Pelajaran, Komik..."
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none @error('nama_kategori') border-red-400 @enderror">
                    @error('nama_kategori')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition">
                        Simpan
                    </button>
                    <a href="{{ route('admin.categories.index') }}"
                       class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold px-6 py-2.5 rounded-lg transition">
                        Batal
                    </a>
                </div>
            </form>

        </div>
    </div>

@endsection