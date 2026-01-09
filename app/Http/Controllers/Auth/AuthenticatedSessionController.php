<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nip' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $login = trim($request->nip);
        $password = $request->password;

        // ===============================
        // LOGIN VIA EMAIL
        // ===============================
        if (str_contains($login, '@')) {
            if (! Auth::attempt([
                'email' => $login,
                'password' => $password
            ])) {
                throw ValidationException::withMessages([
                    'nip' => 'NIP / Email atau Password salah.',
                ]);
            }

            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        // ===============================
        // LOGIN VIA NIP (user_id)
        // ===============================
        if (! Auth::attempt([
            'user_id' => $login, // ⬅️ INI KUNCI UTAMANYA
            'password' => $password
        ])) {
            throw ValidationException::withMessages([
                'nip' => 'NIP / Email atau Password salah.',
            ]);
        }

        $request->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}