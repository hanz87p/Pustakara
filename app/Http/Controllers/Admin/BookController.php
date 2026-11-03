<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    // READ — Daftar buku + pencarian + filter
    public function index(Request $request)
    {
        $query = Book::with('category');

        // Pencarian by Nomor Induk / Judul / Penulis
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_induk', 'like', "%{$search}%")
                  ->orWhere('judul', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%");
            });
        }

        // Filter kategori
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter status stok
        if ($request->filled('status')) {
            if ($request->status === 'tersedia') {
                $query->where('stok', '>', 0);
            } elseif ($request->status === 'habis') {
                $query->where('stok', '=', 0);
            }
        }

        $books = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::orderBy('nama_kategori')->get();

        return view('admin.books.index', compact('books', 'categories'));
    }

    // CREATE — Form tambah buku
    public function create()
    {
        $categories = Category::orderBy('nama_kategori')->get();
        return view('admin.books.create', compact('categories'));
    }

    // STORE — Simpan ke DB
    public function store(Request $request)
    {
        $request->validate([
            'nomor_induk'  => 'required|string|max:30|unique:books,nomor_induk',
            'judul'        => 'required|string|max:200',
            'penulis'      => 'required|string|max:100',
            'penerbit'     => 'required|string|max:100',
            'tahun_terbit' => 'nullable|integer|min:1900|max:' . date('Y'),
            'stok'         => 'required|integer|min:0',
            'category_id'  => 'required|exists:categories,id',
            'rak'          => 'nullable|string|max:20',
            'cover'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'nomor_induk.required' => 'Nomor Induk Buku wajib diisi.',
            'nomor_induk.unique'   => 'Nomor Induk Buku sudah terdaftar.',
            'judul.required'       => 'Judul buku wajib diisi.',
            'stok.required'        => 'Stok wajib diisi.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'cover.image'          => 'Cover harus berupa gambar.',
            'cover.max'            => 'Ukuran cover maksimal 2MB.',
        ]);

        $data = $request->except('cover');
        $data['stok_total'] = $request->stok;

        // Upload cover
        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

        Book::create($data);

        return redirect()->route('admin.books.index')
            ->with('success', 'Buku berhasil ditambahkan.');
    }

    // SHOW — Detail buku (opsional)
    public function show(Book $book)
    {
        return view('admin.books.show', compact('book'));
    }

    // EDIT — Form edit buku
    public function edit(Book $book)
    {
        $categories = Category::orderBy('nama_kategori')->get();
        return view('admin.books.edit', compact('book', 'categories'));
    }

    // UPDATE — Simpan perubahan
    public function update(Request $request, Book $book)
    {
        $request->validate([
            'nomor_induk'  => 'required|string|max:30|unique:books,nomor_induk,' . $book->id,
            'judul'        => 'required|string|max:200',
            'penulis'      => 'required|string|max:100',
            'penerbit'     => 'required|string|max:100',
            'tahun_terbit' => 'nullable|integer|min:1900|max:' . date('Y'),
            'stok'         => 'required|integer|min:0',
            'category_id'  => 'required|exists:categories,id',
            'rak'          => 'nullable|string|max:20',
            'cover'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->except('cover');

        // Upload cover baru
        if ($request->hasFile('cover')) {
            // Hapus cover lama
            if ($book->cover && Storage::disk('public')->exists($book->cover)) {
                Storage::disk('public')->delete($book->cover);
            }
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

        $book->update($data);

        return redirect()->route('admin.books.index')
            ->with('success', 'Buku berhasil diperbarui.');
    }

    // DELETE — Hapus buku
    public function destroy(Book $book)
    {
        // Cek apakah buku sedang dipinjam
        if ($book->loans()->where('status', 'dipinjam')->count() > 0) {
            return redirect()->route('admin.books.index')
                ->with('error', 'Buku tidak bisa dihapus karena sedang dipinjam.');
        }

        // Hapus cover
        if ($book->cover && Storage::disk('public')->exists($book->cover)) {
            Storage::disk('public')->delete($book->cover);
        }

        $book->delete();

        return redirect()->route('admin.books.index')
            ->with('success', 'Buku berhasil dihapus.');
    }
}