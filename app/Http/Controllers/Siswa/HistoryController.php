<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Loan;

class HistoryController extends Controller
{
    public function index()
    {
        $loans = Loan::with('book')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('siswa.history', compact('loans'));
    }
}