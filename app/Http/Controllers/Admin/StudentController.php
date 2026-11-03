<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    // READ — Daftar siswa + pencarian
    public function index(Request $request)
    {
        $query = Student::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('kelas', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        $students = $query->latest()->paginate(10)->withQueryString();

        // Ambil daftar kelas unik untuk filter
        $kelasList = Student::select('kelas')->distinct()->orderBy('kelas')->pluck('kelas');

        return view('admin.students.index', compact('students', 'kelasList'));
    }

    // CREATE — Form tambah siswa
    public function create()
    {
        return view('admin.students.create');
    }

    // STORE — Simpan ke DB (bikin User + Student sekaligus)
    public function store(Request $request)
    {
        $request->validate([
            'nis'    => 'required|string|max:20|unique:students,nis|unique:users,nis_nip',
            'nama'   => 'required|string|max:100',
            'kelas'  => 'required|string|max:20',
            'no_hp'  => 'nullable|string|max:15',
            'password' => 'required|string|min:6',
        ], [
            'nis.required'      => 'NIS wajib diisi.',
            'nis.unique'        => 'NIS sudah terdaftar.',
            'nama.required'     => 'Nama wajib diisi.',
            'kelas.required'    => 'Kelas wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
        ]);

        // Bikin User dulu
        $user = User::create([
            'name'     => $request->nama,
            'email'    => $request->nis . '@siswa.pustakara.sch.id',
            'password' => Hash::make($request->password),
            'role'     => 'siswa',
            'nis_nip'  => $request->nis,
        ]);

        // Bikin Student yang terhubung ke User
        Student::create([
            'user_id' => $user->id,
            'nis'     => $request->nis,
            'nama'    => $request->nama,
            'kelas'   => $request->kelas,
            'no_hp'   => $request->no_hp,
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Siswa berhasil ditambahkan.');
    }

    // EDIT — Form edit siswa
    public function edit(Student $student)
    {
        return view('admin.students.edit', compact('student'));
    }

    // UPDATE — Simpan perubahan
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'nis'   => 'required|string|max:20|unique:students,nis,' . $student->id,
            'nama'  => 'required|string|max:100',
            'kelas' => 'required|string|max:20',
            'no_hp' => 'nullable|string|max:15',
        ]);

        $student->update($request->only('nis', 'nama', 'kelas', 'no_hp'));

        // Update user juga
        if ($student->user) {
            $student->user->update([
                'name'    => $request->nama,
                'nis_nip' => $request->nis,
            ]);
        }

        return redirect()->route('admin.students.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    // DELETE — Hapus siswa
    public function destroy(Student $student)
    {
        // Cek apakah siswa masih punya peminjaman aktif
        if ($student->user && $student->user->loans()->where('status', 'dipinjam')->count() > 0) {
            return redirect()->route('admin.students.index')
                ->with('error', 'Siswa tidak bisa dihapus karena masih memiliki peminjaman aktif.');
        }

        // Hapus user (otomatis hapus student karena cascade)
        if ($student->user) {
            $student->user->delete();
        } else {
            $student->delete();
        }

        return redirect()->route('admin.students.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }
}