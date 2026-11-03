@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">NIS <span class="text-red-500">*</span></label>
        <input type="text" name="nis" value="{{ old('nis', $student->nis ?? '') }}"
               placeholder="Contoh: 2024006"
               class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('nis') border-red-400 @enderror">
        @error('nis')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
        <input type="text" name="nama" value="{{ old('nama', $student->nama ?? '') }}"
               class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('nama') border-red-400 @enderror">
        @error('nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Kelas <span class="text-red-500">*</span></label>
        <input type="text" name="kelas" value="{{ old('kelas', $student->kelas ?? '') }}"
               placeholder="Contoh: XII RPL C"
               class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('kelas') border-red-400 @enderror">
        @error('kelas')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">No. HP</label>
        <input type="text" name="no_hp" value="{{ old('no_hp', $student->no_hp ?? '') }}"
               placeholder="081234567xxx"
               class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
    </div>

    {{-- Password hanya untuk CREATE --}}
    @if (!isset($student))
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-slate-700 mb-1">Password <span class="text-red-500">*</span></label>
            <input type="password" name="password" placeholder="Minimal 6 karakter"
                   class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('password') border-red-400 @enderror">
            @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            <p class="text-xs text-slate-500 mt-1">Password ini digunakan siswa untuk login.</p>
        </div>
    @endif

</div>

<div class="flex gap-3 mt-6">
    <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition">
        {{ $submitLabel ?? 'Simpan' }}
    </button>
    <a href="{{ route('admin.students.index') }}"
       class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold px-6 py-2.5 rounded-lg transition">
        Batal
    </a>
</div>