<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Katalog') — Pustakara</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 font-sans text-slate-800">

<div class="flex min-h-screen">

    {{-- ============ SIDEBAR ============ --}}
    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-40 w-64 bg-[#059669] text-white transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col">

        {{-- Logo --}}
        <div class="px-6 py-5 border-b border-emerald-700">
            <a href="{{ route('siswa.catalog') }}" class="flex items-center gap-2">
                <span class="text-2xl">📚</span>
                <div>
                    <div class="text-lg font-bold tracking-wide">PUSTAKARA</div>
                    <div class="text-[10px] text-emerald-200 uppercase tracking-wider">Siswa Panel</div>
                </div>
            </a>
        </div>

        {{-- Menu --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

            @php
                $menu = [
                    ['route' => 'siswa.catalog', 'label' => 'Katalog Buku', 'icon' => '📖'],
                    ['route' => 'siswa.history', 'label' => 'Riwayat Saya', 'icon' => '📋'],
                    ['route' => 'siswa.profile', 'label' => 'Profil',       'icon' => '👤'],
                ];
            @endphp

            @foreach ($menu as $item)
                @php
                    $isActive = request()->routeIs($item['route'] . '*') || request()->routeIs($item['route']);
                @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition
                          {{ $isActive
                              ? 'bg-emerald-700 text-white shadow'
                              : 'text-emerald-100 hover:bg-emerald-800 hover:text-white' }}">
                    <span class="text-base">{{ $item['icon'] }}</span>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach

        </nav>

        {{-- Profile & Logout --}}
        <div class="border-t border-emerald-700 p-4">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold truncate">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-emerald-200 truncate">{{ auth()->user()->nis_nip }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full text-left px-4 py-2 rounded-lg text-sm font-medium text-red-200 hover:bg-red-600 hover:text-white transition">
                    🚪 Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- Overlay Mobile --}}
    <div id="overlay" class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden" onclick="toggleSidebar()"></div>

    {{-- ============ MAIN CONTENT ============ --}}
    <div class="flex-1 lg:ml-64 flex flex-col min-h-screen">

        {{-- Header --}}
        <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
            <div class="flex items-center justify-between px-4 lg:px-8 py-4">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="lg:hidden text-slate-600 hover:text-slate-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="text-xl lg:text-2xl font-bold text-slate-800">
                        @yield('page-title', 'Katalog Buku')
                    </h1>
                </div>
                <div class="w-9 h-9 rounded-full bg-emerald-600 flex items-center justify-center text-white font-bold text-sm">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </div>
        </header>

        <main class="flex-1 p-4 lg:p-8">

            {{-- Alert Global --}}
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

            @yield('content')
        </main>

        <footer class="bg-white border-t border-slate-200 px-8 py-4 text-center text-xs text-slate-500">
            © 2026 Pustakara — SMK Negeri 5 Surakarta
        </footer>
    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }
</script>

@stack('scripts')
</body>
</html>