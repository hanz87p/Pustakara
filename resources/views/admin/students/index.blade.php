@extends('layouts.admin')

@section('title', 'Data Siswa')
@section('page-title', 'Data Siswa')

@section('content')

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 mb-4 flex justify-between">
            <span>✅ {{ session('success') }}</span>
            <button onclick="this.parentElement.remove()">✕</button>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 mb-4 flex justify-between">
            <span>⚠️ {{ session('error') }}</span>
            <button onclick="this.parentElement.remove()">✕</button>
        </div>
    @endif

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-5">
        <p class="text-sm text-slate-500">
            Total: <strong>{{ $students->total() }}</strong> siswa
        </p>
        <a href="{{ route('admin.students.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition w-fit">
            + Tambah Siswa
        </a>
    </div>

    {{-- Filter & Search --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-5">
        <form method="GET" action="{{ route('admin.students.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Siswa</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="NIS / Nama / Kelas..."
                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Kelas</label>
                <select name="kelas" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">Semua Kelas</option>
                    @foreach ($kelasList as $kls)
                        <option value="{{ $kls }}" {{ request('kelas') === $kls ? 'selected' : '' }}>{{ $kls }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-3 flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                    🔍 Cari
                </button>
                <a href="{{ route('admin.students.index') }}"
                   class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold px-4 py-2 rounded-lg transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 w-16">No</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">NIS</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Nama</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Kelas</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">No. HP</th>
                        <th class="text-right px-4 py-3 font-semibold text-slate-600 w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $index => $student)
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-500">{{ $students->firstItem() + $index }}</td>
                            <td class="px-4 py-3">
                                <span class="font-mono text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">
                                    {{ $student->nis }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $student->nama }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $student->kelas }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $student->no_hp ?? '-' }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.students.edit', $student) }}"
                                   class="text-blue-600 hover:underline text-xs font-semibold mr-3">Edit</a>
                                <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Yakin hapus siswa {{ $student->nama }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-xs font-semibold">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                <p class="text-4xl mb-2">👥</p>
                                <p>Belum ada siswa.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">
        {{ $students->links() }}
    </div>

@endsection