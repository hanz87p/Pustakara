@extends('layouts.admin')
@section('title', 'Pesanan Buku')
@section('page-title', 'Daftar Pesanan Siswa')

@section('content')
    @if (session('success')) <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div> @endif
    @if (session('error')) <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div> @endif

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 border-b">
                <tr>
                    <th class="p-4">Siswa</th>
                    <th class="p-4">Buku</th>
                    <th class="p-4">Tgl Pesan</th>
                    <th class="p-4">Status</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bookings as $b)
                    <tr class="border-b">
                        <td class="p-4"><div class="font-bold">{{ $b->user->name }}</div><div class="text-xs text-slate-500">{{ $b->user->nis_nip }}</div></td>
                        <td class="p-4"><div class="font-bold">{{ $b->book->judul }}</div><div class="text-xs font-mono">{{ $b->book->nomor_induk }}</div></td>
                        <td class="p-4">{{ $b->tanggal_pesan }}</td>
                        <td class="p-4">
                            @if($b->status == 'menunggu') <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs">Menunggu</span>
                            @elseif($b->status == 'disetujui') <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">Disetujui</span>
                            @else <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs">Ditolak</span>
                            @endif
                        </td>
                        <td class="p-4 text-right">
                            @if($b->status == 'menunggu')
                                <form action="{{ route('admin.bookings.convert', $b) }}" method="POST" class="inline" onsubmit="return confirm('Proses jadi peminjaman? Stok akan berkurang.')">
                                    @csrf
                                    <button class="bg-blue-600 text-white px-3 py-1 rounded text-xs">Proses Pinjam</button>
                                </form>
                                <form action="{{ route('admin.bookings.update', $b) }}" method="POST" class="inline">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="ditolak">
                                    <button class="bg-red-100 text-red-600 px-3 py-1 rounded text-xs">Tolak</button>
                                </form>
                            @else
                                <span class="text-slate-400 text-xs">Selesai</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-8 text-center text-slate-500">Belum ada pesanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $bookings->links() }}</div>
@endsection