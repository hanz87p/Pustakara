<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Pustakara</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-600 to-slate-100 flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-10">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-blue-600">Pustakara</h1>
            <p class="text-sm text-slate-500 mt-1">Sistem Informasi Perpustakaan</p>
            <p class="text-xs text-slate-400">SMK Negeri 5 Surakarta</p>
        </div>

        {{-- Error Global --}}
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 mb-4">
                @foreach ($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            {{-- Role Selector --}}
            <div class="grid grid-cols-2 gap-2 mb-6">
                <label class="cursor-pointer">
                    <input type="radio" name="role" value="admin" class="peer sr-only" {{ old('role') === 'admin' ? 'checked' : '' }}>
                    <div class="text-center py-2 rounded-lg bg-slate-100 peer-checked:bg-blue-600 peer-checked:text-white text-slate-600 font-medium text-sm transition">
                        Admin / Petugas
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="role" value="siswa" class="peer sr-only" {{ old('role', 'siswa') === 'siswa' ? 'checked' : '' }}>
                    <div class="text-center py-2 rounded-lg bg-slate-100 peer-checked:bg-blue-600 peer-checked:text-white text-slate-600 font-medium text-sm transition">
                        Siswa
                    </div>
                </label>
            </div>

            {{-- NIS/NIP --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-slate-700 mb-1">NIS / NIP</label>
                <input type="text" name="nis_nip" value="{{ old('nis_nip') }}"
                       placeholder="Masukkan NIS / NIP..."
                       class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
            </div>

            {{-- Password --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                <input type="password" name="password"
                       placeholder="Masukkan password..."
                       class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
            </div>

            {{-- Remember & Forgot --}}
            <div class="flex items-center justify-between mb-6 text-sm">
                <label class="flex items-center gap-2 text-slate-600">
                    <input type="checkbox" name="remember" class="rounded text-blue-600">
                    Ingat Saya
                </label>
                <a href="#" class="text-blue-600 hover:underline">Lupa Password?</a>
            </div>

            {{-- Tombol Login --}}
            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition flex items-center justify-center gap-2">
                Masuk
                <span>→</span>
            </button>
        </form>

        {{-- Footer --}}
        <p class="text-center text-xs text-slate-400 mt-6">
            © 2026 Pustakara — SMK Negeri 5 Surakarta
        </p>
    </div>

</body>
</html>