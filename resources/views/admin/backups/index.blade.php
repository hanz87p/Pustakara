@extends('layouts.admin')

@section('title', 'Backup Database')
@section('page-title', 'Backup Database')

@section('content')

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 mb-4">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 mb-4">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    {{-- Card Backup --}}
    <div class="bg-white rounded-xl shadow-sm p-6 mb-5">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-800">💾 Backup Database</h2>
                <p class="text-sm text-slate-500 mt-1">
                    Backup seluruh data aplikasi untuk mengantisipasi kehilangan data.
                </p>
                @if ($backups->count() > 0)
                    <p class="text-xs text-slate-400 mt-2">
                        Backup terakhir: {{ $backups->first()->created_at->format('d M Y H:i') }}
                    </p>
                @endif
            </div>
            <form action="{{ route('admin.backups.store') }}" method="POST"
                  onsubmit="return confirm('Mulai backup database sekarang?')">
                @csrf
                <button type="submit"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-5 py-2.5 rounded-lg transition">
                    💾 Backup Sekarang
                </button>
            </form>
        </div>
    </div>

    {{-- Riwayat Backup --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h3 class="font-semibold text-slate-800">Riwayat Backup</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 w-16">No</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Nama File</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Ukuran</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Tanggal</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600">Status</th>
                        <th class="text-right px-4 py-3 font-semibold text-slate-600 w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($backups as $i => $backup)
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-500">{{ $backups->firstItem() + $i }}</td>
                            <td class="px-4 py-3 font-mono text-xs">{{ $backup->nama_file }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $backup->ukuran }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $backup->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 text-center">
                                @if ($backup->status === 'sukses')
                                    <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">Sukses</span>
                                @else
                                    <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full">Gagal</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if ($backup->status === 'sukses')
                                    <a href="{{ route('admin.backups.download', $backup) }}"
                                       class="text-blue-600 hover:underline text-xs font-semibold mr-3">Download</a>
                                @endif
                                <form action="{{ route('admin.backups.destroy', $backup) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Hapus file backup ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-xs font-semibold">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                <p class="text-4xl mb-2">💾</p>
                                <p>Belum ada backup. Klik <strong>Backup Sekarang</strong> untuk membuat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">{{ $backups->links() }}</div>

@endsection