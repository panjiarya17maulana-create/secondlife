<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, callable $next): Response
{
    {
        if (Auth::check()) {
            // Cek apakah user yang login punya id_role = 1 (Admin) di tabel pivot
            $isAdmin = DB::table('user_roles')
                ->where('id_user', Auth::id())
                ->where('id_role', 1)
                ->exists();

            if ($isAdmin) {
                return $next($request);
            }
        }

        // Jika bukan admin, tendang ke halaman utama dengan pesan error
        return redirect('/')->with('error', 'Akses ditolak! Halaman tersebut khusus Admin.');
    }
}
}