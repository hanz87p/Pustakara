@extends('layouts.admin')

@section('title', 'Peminjaman Baru')
@section('page-title', 'Peminjaman Baru')

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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-bold text-slate-800 mb-5">Form Peminjaman Buku</h2>

            <form action="{{ route('admin.loans.store') }}" method="POST" id="loanForm">
                @csrf

                <div class="mb-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">📷 Scan Nomor Induk Buku</label>
                    <div class="flex gap-2">
                        <input type="text" id="nomorInduk" placeholder="Scan atau ketik nomor induk..."
                               autofocus autocomplete="off"
                               class="flex-1 px-4 py-2.5 border-2 border-dashed border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-mono">
                        <button type="button" id="btnSearchBook"
                                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg">
                            Scan
                        </button>
                    </div>
                    <p id="bookStatus" class="text-xs mt-1"></p>
                </div>

                <input type="hidden" name="book_id" id="bookId">

                <div id="bookInfo" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4 mb-5">
                    <p class="font-bold text-slate-800" id="bookJudul">—</p>
                    <p class="text-sm text-slate-600">Penulis: <span id="bookPenulis">—</span></p>
                    <p class="text-sm font-semibold">Stok: <span id="bookStok">—</span></p>
                </div>

                <div class="mb-5 relative">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">NIS Siswa <span class="text-red-500">*</span></label>
                    <input type="text" id="nisSearch" placeholder="Ketik NIS atau nama..."
                           autocomplete="off"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    <input type="hidden" name="user_id" id="userId">
                    <div id="studentDropdown" class="hidden absolute z-20 w-full bg-white border border-slate-200 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto"></div>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal Pinjam</label>
                    <input type="date" name="tanggal_pinjam" value="{{ date('Y-m-d') }}" readonly
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-lg bg-slate-50 outline-none">
                </div>

                <button type="submit" id="btnSubmit" disabled
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition disabled:opacity-50">
                    📤 Proses Peminjaman
                </button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-bold text-slate-800 mb-5">Riwayat Hari Ini</h2>

            @if ($todayLoans->count() > 0)
                <div class="space-y-3">
                    @foreach ($todayLoans as $loan)
                        <div class="border border-slate-200 rounded-lg p-3">
                            <p class="font-semibold text-slate-800 text-sm">{{ $loan->book->judul }}</p>
                            <p class="text-xs text-slate-500">{{ $loan->user->name }} • {{ $loan->user->nis_nip }}</p>
                            <p class="text-xs text-slate-400 font-mono mt-1">{{ $loan->kode_pinjam }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-slate-400">
                    <p class="text-4xl mb-2">📋</p>
                    <p class="text-sm">Belum ada peminjaman hari ini.</p>
                </div>
            @endif
        </div>

    </div>

@push('scripts')
<script>
document.getElementById('btnSearchBook').addEventListener('click', searchBook);
document.getElementById('nomorInduk').addEventListener('keypress', e => {
    if (e.key === 'Enter') { e.preventDefault(); searchBook(); }
});

async function searchBook() {
    const nomor = document.getElementById('nomorInduk').value.trim();
    const status = document.getElementById('bookStatus');
    const info = document.getElementById('bookInfo');

    if (!nomor) return;

    status.textContent = '⏳ Mencari...';
    status.className = 'text-xs mt-1 text-slate-500';

    const res = await fetch(`{{ route('admin.loans.search-book') }}?nomor_induk=${encodeURIComponent(nomor)}`);
    const data = await res.json();

    if (!data.found) {
        status.textContent = '❌ Buku tidak ditemukan.';
        status.className = 'text-xs mt-1 text-red-600';
        info.classList.add('hidden');
        document.getElementById('bookId').value = '';
        checkSubmit();
        return;
    }

    const b = data.book;
    status.textContent = '✅ Buku ditemukan.';
    status.className = 'text-xs mt-1 text-green-600';
    document.getElementById('bookId').value = b.id;
    document.getElementById('bookJudul').textContent = b.judul;
    document.getElementById('bookPenulis').textContent = b.penulis;
    document.getElementById('bookStok').textContent = b.stok;
    info.classList.remove('hidden');
    checkSubmit();
}

const nisSearch = document.getElementById('nisSearch');
const dropdown = document.getElementById('studentDropdown');
let timeout;

nisSearch.addEventListener('input', function() {
    clearTimeout(timeout);
    const keyword = this.value.trim();
    if (keyword.length < 1) {
        dropdown.classList.add('hidden');
        document.getElementById('userId').value = '';
        checkSubmit();
        return;
    }

    timeout = setTimeout(async () => {
        const res = await fetch(`{{ route('admin.loans.search-student') }}?keyword=${encodeURIComponent(keyword)}`);
        const students = await res.json();

        if (students.length === 0) {
            dropdown.innerHTML = '<div class="p-3 text-sm text-slate-400">Tidak ada siswa.</div>';
        } else {
            dropdown.innerHTML = students.map(s => `
                <div class="p-3 hover:bg-blue-50 cursor-pointer border-b last:border-0"
                     onclick="selectStudent(${s.id}, '${s.name}', '${s.nis_nip}')">
                    <p class="font-semibold text-sm">${s.name}</p>
                    <p class="text-xs text-slate-500">${s.nis_nip}</p>
                </div>
            `).join('');
        }
        dropdown.classList.remove('hidden');
    }, 300);
});

function selectStudent(id, name, nis) {
    document.getElementById('userId').value = id;
    nisSearch.value = `${name} (${nis})`;
    dropdown.classList.add('hidden');
    checkSubmit();
}

function checkSubmit() {
    const bookId = document.getElementById('bookId').value;
    const userId = document.getElementById('userId').value;
    document.getElementById('btnSubmit').disabled = !(bookId && userId);
}
</script>
@endpush

@endsection