<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, callable $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                // Penjual yang masih menunggu verifikasi admin tidak boleh akses
                if ($role === 'penjual' && $user->status_akun === 'menunggu') {
                    return redirect()->route('home')
                        ->with('error', '⏳ Akun penjual kamu masih menunggu verifikasi admin. Silakan tunggu konfirmasi.');
                }

                // Penjual yang ditolak juga tidak boleh akses
                if ($role === 'penjual' && $user->status_akun === 'ditolak') {
                    return redirect()->route('home')
                        ->with('error', '❌ Akun penjual kamu telah ditolak oleh admin.');
                }

                return $next($request);
            }
        }

        abort(403, 'Akses ditolak.');
    }
}
