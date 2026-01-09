<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (! $user) {
            abort(401);
        }

        $userRole = $user->role ?? null;
        if (! $userRole) {
            abort(403, 'Forbidden: role missing');
        }

        // roles datang dari route middleware: role:Approver,Administrator
        // Pastikan perbandingan string persis sama dengan yang disimpan
        if (! in_array($userRole, $roles, true)) {
            abort(403, 'Forbidden: role not allowed');
        }

        return $next($request);
    }
}
