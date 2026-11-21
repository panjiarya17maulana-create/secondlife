<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the email input form for forgot password request.
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle forgot password request. Validate email, generate token,
     * and bypass email sending by redirecting directly to reset password page.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Email tidak terdaftar dalam sistem.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak terdaftar dalam sistem.']);
        }

        // Generate token using password broker
        $token = Password::getRepository()->create($user);

        // Bypass email sending: redirect directly to reset-password form with the token and email
        return redirect()->route('password.reset', [
            'token' => $token,
            'email' => $request->email
        ])->with('success', 'Email ditemukan. Token reset telah berhasil dibuat (Email bypass). Silakan masukkan password baru.');
    }
}
