<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    // Tampilkan halaman login
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }

        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $request->validate([
            'nis_nip' => 'required|string',
            'password' => 'required|string',
        ], [
            'nis_nip.required' => 'NIS/NIP wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $credentials = [
            'nis_nip' => $request->nis_nip,
            'password' => $request->password,
        ];

        // Cek role yang dipilih user di form (admin / siswa)
        $role = $request->input('role', 'siswa');

        // Cek user ada atau tidak
        $user = \App\Models\User::where('nis_nip', $request->nis_nip)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'nis_nip' => 'NIS/NIP tidak terdaftar.',
            ]);
        }

        // Cek role cocok
        if ($user->role !== $role) {
            throw ValidationException::withMessages([
                'role' => 'Role yang dipilih tidak sesuai dengan akun Anda.',
            ]);
        }

        // Coba login
        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();
            return $this->redirectByRole();
        }

        throw ValidationException::withMessages([
            'password' => 'Password salah.',
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // Redirect berdasarkan role
    private function redirectByRole()
    {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('siswa.catalog');
    }
}