<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // input field di blade masih name="email", tapi isinya NIP/userId (B316, G999, dll)
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $userId = strtoupper(trim((string) $this->input('email')));
        $password = (string) $this->input('password');

        $baseUrl = rtrim((string) env('UIMNG_BASE_URL', 'http://127.0.0.1:8001'), '/');

        try {
            $resp = Http::timeout(10)->post($baseUrl.'/api/v1/authorization/login-for-application', [
                'userId' => $userId,
                'password' => $password,
                'appId' => (int) env('UIMNG_APP_ID', 230),
                'isEncrypted' => false,
            ]);
        } catch (\Throwable $e) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => 'UIMNG tidak bisa diakses. Pastikan simulator jalan di UIMNG_BASE_URL.',
            ]);
        }

        $json = $resp->json();

        if (! $resp->ok() || ! ($json['succeeded'] ?? false)) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $nama = (string) data_get($json, 'data.nama', $userId);
        $email = (string) data_get($json, 'data.email', strtolower($userId).'@example.com');
        $namaFungsiRaw = (string) data_get($json, 'data.namaFungsi', '');

        // normalisasi role (handle spasi / unicode aneh)
        $nf = strtolower($namaFungsiRaw);
        $nf = preg_replace('/[^\x20-\x7E]/u', ' ', $nf) ?? $nf;
        $nf = trim(preg_replace('/\s+/', ' ', $nf) ?? $nf);
        $nfCompact = preg_replace('/[^a-z0-9]/', '', $nf) ?? $nf;

        // mapping role_id sesuai DB kamu:
        // maker=1, approver=2, administrator=3, approver2=14  (kamu udah ketemu approver2 = 14)
        $roleId = match (true) {
            $nfCompact === 'maker' => 1,
            $nfCompact === 'approver' => 2,
            $nfCompact === 'administrator' => 3,
            $nfCompact === 'approver2' => 14,
            default => null,
        };

        if (! $roleId) {
            abort(403, "Role UIMNG tidak dikenali: {$namaFungsiRaw}");
        }

        // unit kerja: contoh cepat (kalau kamu sudah punya mapping lain, ganti di sini)
        $kodeCabang = (string) data_get($json, 'data.kodeCabang', '');
        $unitKerjaId = str_starts_with($kodeCabang, '002') ? 2 : 1; // audit=2, selain itu=1 (ubah kalau perlu)

        $user = \App\Models\User::updateOrCreate(
            ['user_id' => $userId],
            [
                'name' => $nama,
                'email' => $email,
                // password lokal gak dipakai buat verifikasi (verifikasi terjadi di UIMNG),
                // tapi tetap isi supaya record valid.
                'password' => Str::random(40),
                'role_id' => $roleId,
                'unit_kerja_id' => $unitKerjaId,
            ]
        );

        Auth::login($user, $this->boolean('remember'));
        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
