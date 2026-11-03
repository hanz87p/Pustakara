@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Nomor Induk Buku <span class="text-red-500">*</span></label>
        <input type="text" name="nomor_induk" value="{{ old('nomor_induk', $book->nomor_induk ?? '') }}"
               placeholder="Contoh: BKU-011"
               class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('nomor_induk') border-red-400 @enderror">
        @error('nomor_induk')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Kategori <span class="text-red-500">*</span></label>
        <select name="category_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('category_id') border-red-400 @enderror">
            <option value="">-- Pilih Kategori --</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id', $book->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->nama_kategori }}
                </option>
            @endforeach
        </select>
        @error('category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-slate-700 mb-1">Judul Buku <span class="text-red-500">*</span></label>
        <input type="text" name="judul" value="{{ old('judul', $book->judul ?? '') }}"
               class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('judul') border-red-400 @enderror">
        @error('judul')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Penulis <span class="text-red-500">*</span></label>
        <input type="text" name="penulis" value="{{ old('penulis', $book->penulis ?? '') }}"
               class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('penulis') border-red-400 @enderror">
        @error('penulis')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Penerbit <span class="text-red-500">*</span></label>
        <input type="text" name="penerbit" value="{{ old('penerbit', $book->penerbit ?? '') }}"
               class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('penerbit') border-red-400 @enderror">
        @error('penerbit')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Tahun Terbit</label>
        <input type="number" name="tahun_terbit" value="{{ old('tahun_terbit', $book->tahun_terbit ?? date('Y')) }}"
               min="1900" max="{{ date('Y') }}"
               class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Stok <span class="text-red-500">*</span></label>
        <input type="number" name="stok" value="{{ old('stok', $book->stok ?? 1) }}" min="0"
               class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('stok') border-red-400 @enderror">
        @error('stok')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Rak / Lokasi</label>
        <input type="text" name="rak" value="{{ old('rak', $book->rak ?? '') }}"
               placeholder="Contoh: A-01"
               class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-slate-700 mb-1">Cover Buku</label>
        @if (isset($book) && $book->cover_url)
            <div class="mb-2">
                <img src="{{ $book->cover_url }}" class="w-24 h-32 object-cover rounded shadow">
                <p class="text-xs text-slate-500 mt-1">Cover saat ini</p>
            </div>
        @endif
        <input type="file" name="cover" accept="image/*"
               class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        <p class="text-xs text-slate-500 mt-1">Format: JPG, PNG, WEBP. Maks 2MB.</p>
        @error('cover')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

</div>

<div class="flex gap-3 mt-6">
    <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition">
        {{ $submitLabel ?? 'Simpan' }}
    </button>
    <a href="{{ route('admin.books.index') }}"
       class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold px-6 py-2.5 rounded-lg transition">
        Batal
    </a>
</div>