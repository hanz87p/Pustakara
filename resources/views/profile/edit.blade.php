@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.siswa')

@section('title', 'Profil')
@section('page-title', 'Profil Saya')

@section('content')

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 mb-4">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 mb-4">
            @foreach ($errors->all() as $error)
                <div>⚠️ {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="max-w-3xl space-y-6">

        {{-- ============ FORM EDIT PROFIL ============ --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-bold text-slate-800 mb-5">Informasi Pribadi</h2>

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">NIS / NIP</label>
                        <input type="text" value="{{ auth()->user()->nis_nip }}" readonly
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-lg bg-slate-50 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                        <input type="email" value="{{ auth()->user()->email }}" readonly
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-lg bg-slate-50 outline-none">
                    </div>

                    @if (auth()->user()->role === 'siswa' && auth()->user()->student)
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Kelas</label>
                            <input type="text" value="{{ auth()->user()->student->kelas }}" readonly
                                   class="w-full px-4 py-2.5 border border-slate-200 rounded-lg bg-slate-50 outline-none">
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">No. HP</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp', auth()->user()->student->no_hp ?? '') }}"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>

                <button type="submit"
                        class="mt-5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2.5 rounded-lg">
                    Simpan Perubahan
                </button>
            </form>
        </div>

        {{-- ============ FORM GANTI PASSWORD ============ --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-bold text-slate-800 mb-5">Ganti Password</h2>

            <form action="{{ route('profile.password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Password Lama</label>
                        <input type="password" name="current_password"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Password Baru</label>
                        <input type="password" name="password"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        <p class="text-xs text-slate-500 mt-1">Minimal 6 karakter.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>

                <button type="submit"
                        class="mt-5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-6 py-2.5 rounded-lg">
                    Ganti Password
                </button>
            </form>
        </div>

    </div>

@endsection