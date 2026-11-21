<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = User::with('roles')->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
        }

        if ($user->status_akun === 'ditolak') {
            return back()->withErrors(['email' => 'Akun Anda telah ditolak oleh admin.'])->withInput();
        }

        if ($user->status_akun === 'menunggu') {
            return back()->withErrors(['email' => '⏳ Akun penjual Anda sedang menunggu verifikasi admin. Silakan tunggu konfirmasi melalui email.'])->withInput();
        }

        Auth::login($user, $request->boolean('remember'));

        $role = $user->getPrimaryRole();
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard')->with('success', '👑 Selamat datang, Admin!');
        }

        return redirect()->route('home')->with('success', 'Selamat datang, ' . $user->nama . '!');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:150',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', 'min:6', 'confirmed'],
            'no_hp'    => 'required|string|max:20',
            'lokasi'   => 'nullable|string|max:200',
        ], [
            'email.unique' => 'Email sudah terdaftar.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $user = User::create([
            'nama'       => $request->nama,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'no_hp'      => $request->no_hp,
            'lokasi'     => $request->lokasi,
            'status_akun'=> 'aktif',
            'created_at' => now(),
        ]);

        $roleId = Role::where('nama_role', 'pembeli')->value('id_role');
        $user->roles()->attach($roleId);

        return redirect()->route('login')->with('success', '✅ Registrasi berhasil! Silakan login.');
    }

    public function showDaftarPenjual()
    {
        // Jika user sudah login dan sudah menjadi penjual, redirect ke dashboard
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            if ($user->isPenjual()) {
                return redirect()->route('penjual.dashboard');
            }
        }
        return view('auth.daftar_penjual');
    }

    public function daftarPenjual(Request $request)
    {
        // Jika user sudah login, upgrade mereka menjadi penjual
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            
            // Validasi hanya untuk file KTM dan data tambahan
            $request->validate([
                'no_hp'    => 'nullable|string|max:20',
                'lokasi'   => 'nullable|string|max:200',
                'foto_ktm' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            $fotoKtm = null;
            if ($request->hasFile('foto_ktm')) {
                $file    = $request->file('foto_ktm');
                $nama    = 'ktm_' . uniqid() . '.' . $file->extension();
                $file->storeAs('ktm', $nama, 'public');
                $fotoKtm = $nama;
            }

            // Update user data
            $user->update([
                'no_hp'       => $request->no_hp ?? $user->no_hp,
                'lokasi'      => $request->lokasi ?? $user->lokasi,
                'foto_ktm'    => $fotoKtm,
                'status_akun' => 'menunggu',
            ]);

            // Tambahkan role penjual jika belum ada
            if (!$user->isPenjual()) {
                $roleId = Role::where('nama_role', 'penjual')->value('id_role');
                $user->roles()->attach($roleId);
            }

            return redirect()->route('home')
                ->with('success', '✅ Pendaftaran penjual berhasil! KTM kamu sudah diterima. Tunggu verifikasi admin sebelum bisa berjualan.');
        }

        // Validasi untuk registrasi penjual baru
        $request->validate([
            'nama'     => 'required|string|max:150',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', 'min:6', 'confirmed'],
            'no_hp'    => 'required|string|max:20',
            'lokasi'   => 'required|string|max:200',
            'foto_ktm' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $fotoKtm = null;
        if ($request->hasFile('foto_ktm')) {
            $file    = $request->file('foto_ktm');
            $nama    = 'ktm_' . uniqid() . '.' . $file->extension();
            $file->storeAs('ktm', $nama, 'public');
            $fotoKtm = $nama;
        }

        $user = User::create([
            'nama'        => $request->nama,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'no_hp'       => $request->no_hp,
            'lokasi'      => $request->lokasi,
            'foto_ktm'    => $fotoKtm,
            'status_akun' => 'menunggu',
            'created_at'  => now(),
        ]);

        $roleId = Role::where('nama_role', 'penjual')->value('id_role');
        $user->roles()->attach($roleId);

        // Jangan auto-login, arahkan ke login dengan pesan menunggu verifikasi
        return redirect()->route('login')
            ->with('success', '✅ Pendaftaran penjual berhasil! KTM kamu sudah diterima. Silakan login setelah akun diverifikasi oleh admin.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
