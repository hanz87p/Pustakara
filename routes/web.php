<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\LoanController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Siswa\CatalogController;
use App\Http\Controllers\Siswa\HistoryController;
use App\Http\Controllers\ProfileController;

// ============ GUEST ============
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

// ============ LOGOUT ============
Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ============ PROFIL (Bisa diakses admin & siswa) ============
Route::middleware('auth')->group(function () {
    Route::get('/profile',          [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile',          [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// ============ ADMIN ============
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // KATEGORI
    Route::resource('categories', CategoryController::class);

    // BUKU
    Route::resource('books', BookController::class);

    // SISWA
    Route::resource('students', StudentController::class);

    // PEMINJAMAN
    Route::get('/loans/search-book',    [LoanController::class, 'searchBook'])->name('loans.search-book');
    Route::get('/loans/search-student', [LoanController::class, 'searchStudent'])->name('loans.search-student');
    Route::resource('loans', LoanController::class);

    // PENGEMBALIAN
    Route::get('/returns',          [LoanController::class, 'returnForm'])->name('loans.return');
    Route::get('/returns/search',   [LoanController::class, 'searchLoan'])->name('loans.search-loan');
    Route::post('/returns/process', [LoanController::class, 'processReturn'])->name('loans.process-return');

    // LAPORAN
    Route::get('/reports',       [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf',   [ReportController::class, 'exportPdf'])->name('reports.pdf');
    Route::get('/reports/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');

    // BACKUP
    Route::get('/backups',                   [BackupController::class, 'index'])->name('backups.index');
    Route::post('/backups',                  [BackupController::class, 'store'])->name('backups.store');
    Route::get('/backups/{backup}/download', [BackupController::class, 'download'])->name('backups.download');
    Route::delete('/backups/{backup}',       [BackupController::class, 'destroy'])->name('backups.destroy');

    Route::get('/bookings', [\App\Http\Controllers\Admin\BookingController::class, 'index'])->name('bookings.index');
Route::put('/bookings/{booking}', [\App\Http\Controllers\Admin\BookingController::class, 'update'])->name('bookings.update');
Route::post('/bookings/{booking}/convert', [\App\Http\Controllers\Admin\BookingController::class, 'convertToLoan'])->name('bookings.convert');
});

// ============ SISWA ============
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/catalog',        [CatalogController::class, 'index'])->name('catalog');
    Route::get('/catalog/{book}', [CatalogController::class, 'show'])->name('book.detail');
    Route::get('/history',        [HistoryController::class, 'index'])->name('history');
    Route::get('/profile',        [ProfileController::class, 'edit'])->name('profile');
    // Di dalam grup siswa
Route::post('/catalog/{book}/booking', [\App\Http\Controllers\Siswa\BookingController::class, 'store'])->name('booking.store');
});

// ============ ROOT ============
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('siswa.catalog');
    }
    return redirect()->route('login');
});
