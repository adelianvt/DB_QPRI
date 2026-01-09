<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required','string'],
            'password' => ['required','string'],
        ]);

        $baseUrl = rtrim(env('UIMNG_BASE_URL', 'http://localhost:8000'), '/');

        $resp = Http::timeout(10)->post($baseUrl . '/api/v1/authorization/login-for-application', [
            'userId' => $data['email'], // mapping email -> userId (sementara)
            'password' => $data['password'],
            'appId' => (int) env('UIMNG_APP_ID', 230),
            'isEncrypted' => filter_var(env('UIMNG_IS_ENCRYPTED', 'false'), FILTER_VALIDATE_BOOLEAN),
        ]);

        $json = $resp->json();

        if (!$resp->ok() || !($json['succeeded'] ?? false)) {
            throw ValidationException::withMessages([
                'email' => ['Login gagal (UIMNG). Email/password salah atau service belum jalan.'],
            ]);
        }

        // sync user lokal (buat token Sanctum)
        $user = User::firstOrCreate(
            ['email' => $data['email']],
            [
                'name' => $json['data']['userName'] ?? $data['email'],
                // password lokal tidak dipakai (karena otentikasi lewat UIMNG)
                'password' => bcrypt(str()->random(32)),
            ]
        );

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
