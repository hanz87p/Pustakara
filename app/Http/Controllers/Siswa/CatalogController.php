<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with('category');

        // Search by nomor induk / judul / penulis
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

        // Filter hanya yang tersedia
        if ($request->filled('available') && $request->available == '1') {
            $query->where('stok', '>', 0);
        }

        $books = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::orderBy('nama_kategori')->get();

        return view('siswa.catalog', compact('books', 'categories'));
    }

    public function show(Book $book)
    {
        $book->load('category');
        return view('siswa.book-detail', compact('book'));
    }
}