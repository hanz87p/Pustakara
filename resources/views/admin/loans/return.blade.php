@extends('layouts.admin')

@section('title', 'Pengembalian Buku')
@section('page-title', 'Pengembalian Buku')

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

    <div class="max-w-3xl mx-auto">

        {{-- FORM PENGEMBALIAN --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-slate-800 mb-5">Form Pengembalian Buku</h2>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-slate-700 mb-1">
                    📷 Scan Barcode / Input Nomor Induk Buku
                </label>
                <div class="flex gap-2">
                    <input type="text" id="nomorInduk"
                           placeholder="Arahkan scanner ke barcode buku..."
                           autofocus autocomplete="off"
                           class="flex-1 px-4 py-2.5 border-2 border-dashed border-green-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-solid outline-none font-mono">
                    <button type="button" id="btnSearch"
                            class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-5 py-2 rounded-lg">
                        🔍 Cari
                    </button>
                </div>
                <p id="statusMsg" class="text-xs mt-2"></p>
            </div>

            {{-- HASIL PENCARIAN --}}
            <div id="resultBox" class="hidden">

                <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 mb-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-slate-500 text-xs">Kode Peminjaman</p>
                            <p class="font-mono font-semibold text-blue-700" id="rKode">—</p>
                        </div>
                        <div>
                            <p class="text-slate-500 text-xs">Nama Siswa</p>
                            <p class="font-semibold" id="rNama">—</p>
                        </div>
                        <div>
                            <p class="text-slate-500 text-xs">NIS</p>
                            <p class="font-mono" id="rNis">—</p>
                        </div>
                        <div>
                            <p class="text-slate-500 text-xs">Judul Buku</p>
                            <p class="font-semibold" id="rJudul">—</p>
                        </div>
                        <div>
                            <p class="text-slate-500 text-xs">Tanggal Pinjam</p>
                            <p id="rPinjam">—</p>
                        </div>
                        <div>
                            <p class="text-slate-500 text-xs">Jatuh Tempo</p>
                            <p id="rJatuhTempo">—</p>
                        </div>
                    </div>
                </div>

                {{-- INFO DENDA --}}
                <div id="dendaBox" class="hidden bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-red-700">
                                ⚠️ Terlambat <span id="rHari">0</span> hari
                            </p>
                            <p class="text-xs text-red-600">Denda: Rp 1.000 / hari</p>
                        </div>
                        <p class="text-2xl font-bold text-red-700" id="rDenda">Rp 0</p>
                    </div>
                </div>

                <div id="noDendaBox" class="hidden bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <p class="text-sm font-semibold text-green-700">✅ Tidak ada denda — buku dikembalikan tepat waktu.</p>
                </div>

                {{-- TOMBOL PROSES --}}
                <form action="{{ route('admin.loans.process-return') }}" method="POST"
                      onsubmit="return confirm('Proses pengembalian buku ini?')">
                    @csrf
                    <input type="hidden" name="loan_id" id="loanId">
                    <button type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg transition">
                        ✅ Proses Pengembalian
                    </button>
                </form>
            </div>

        </div>

    </div>

@push('scripts')
<script>
const btn = document.getElementById('btnSearch');
const input = document.getElementById('nomorInduk');

btn.addEventListener('click', searchLoan);
input.addEventListener('keypress', e => {
    if (e.key === 'Enter') { e.preventDefault(); searchLoan(); }
});

async function searchLoan() {
    const nomorInduk = input.value.trim();
    const status = document.getElementById('statusMsg');
    const resultBox = document.getElementById('resultBox');
    const dendaBox = document.getElementById('dendaBox');
    const noDendaBox = document.getElementById('noDendaBox');

    if (!nomorInduk) {
        status.textContent = '⚠️ Masukkan nomor induk buku.';
        status.className = 'text-xs mt-2 text-yellow-600';
        return;
    }

    status.textContent = '⏳ Mencari data peminjaman...';
    status.className = 'text-xs mt-2 text-slate-500';
    resultBox.classList.add('hidden');

    try {
        const res = await fetch(`{{ route('admin.loans.search-loan') }}?nomor_induk=${encodeURIComponent(nomorInduk)}`);
        const data = await res.json();

        if (!data.found) {
            status.textContent = '❌ ' + data.message;
            status.className = 'text-xs mt-2 text-red-600';
            return;
        }

        const l = data.loan;
        status.textContent = '✅ Data ditemukan.';
        status.className = 'text-xs mt-2 text-green-600';

        document.getElementById('rKode').textContent       = l.kode_pinjam;
        document.getElementById('rNama').textContent       = l.user.name;
        document.getElementById('rNis').textContent        = l.user.nis_nip;
        document.getElementById('rJudul').textContent      = l.book.judul;
        document.getElementById('rPinjam').textContent     = l.tanggal_pinjam;
        document.getElementById('rJatuhTempo').textContent = l.tanggal_jatuh_tempo;
        document.getElementById('loanId').value            = l.id;

        if (l.denda > 0) {
            document.getElementById('rHari').textContent  = l.hari_terlambat;
            document.getElementById('rDenda').textContent = l.denda_format;
            dendaBox.classList.remove('hidden');
            noDendaBox.classList.add('hidden');
        } else {
            dendaBox.classList.add('hidden');
            noDendaBox.classList.remove('hidden');
        }

        resultBox.classList.remove('hidden');

    } catch (err) {
        status.textContent = '❌ Error: ' + err.message;
        status.className = 'text-xs mt-2 text-red-600';
    }
}
</script>
@endpush

@endsection