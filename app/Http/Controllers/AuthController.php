<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $identifier = trim((string) $request->input('login', ''));
        $password   = (string) $request->input('password', '');

        if ($identifier === '') {
            throw ValidationException::withMessages([
                'login' => 'Field NIP / Email wajib diisi.',
            ]);
        }

        if ($password === '') {
            throw ValidationException::withMessages([
                'password' => 'Password wajib diisi.',
            ]);
        }

    
        $candidates = [
            'user_id',    
            'nip',
            'username',
            'nik',
            'no_pegawai',
            'employee_id',
            'email',
        ];

        foreach ($candidates as $col) {
            if (!Schema::hasColumn('users', $col)) continue;

            if (Auth::attempt([$col => $identifier, 'password' => $password])) {
                $request->session()->regenerate();
                return redirect()->intended(route('pengajuans.index'));
            }
        }

        throw ValidationException::withMessages([
            'login' => 'Akun / password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}