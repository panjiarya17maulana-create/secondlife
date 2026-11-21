<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Produk;
use App\Models\DetailTransaksi;
use App\Models\Review;
use App\Models\ProdukFoto;
use App\Models\User;

class ProfilController extends Controller {
    public function index() {
        return view('profil', ['user'=>Auth::user()]);
    }
    public function update(Request $request)
{
    $user = User::findOrFail(Auth::id());

    // Form Ganti Password
    if ($request->tab === 'password') {

        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password berhasil diperbarui!');
    }

    // Form Edit Profil
    $request->validate([
        'nama'     => 'required|string|max:150',
        'no_hp'    => 'required|string|max:20',
        'lokasi'   => 'required|string|max:200',
        'foto_ktm' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $user->nama = $request->nama;
    $user->no_hp = $request->no_hp;
    $user->lokasi = $request->lokasi;

    if ($request->hasFile('foto_ktm')) {

        if (!empty($user->foto_ktm)) {
            Storage::disk('public')->delete('ktm/' . $user->foto_ktm);
        }

        $file = $request->file('foto_ktm');
        $namaFile = 'ktm_' . uniqid() . '.' . $file->extension();

        $file->storeAs('ktm', $namaFile, 'public');

        $user->foto_ktm = $namaFile;
    }

    $user->save();

    return back()->with('success', 'Profil berhasil diperbarui!');
}
    /**
     * Hapus akun 
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id_user;

        // validasi password untuk konfirmasi
        if (!Hash::check($request->password_konfirmasi, $user->password)) {
            return back()->with('error', 'Password salah');
        }

        // hapus foto produk dari storage
        $produkIds = Produk::where('id_user', $userId)->pluck('id_produk');
        foreach (ProdukFoto::whereIn('id_produk', $produkIds)->get() as $foto) {
            Storage::disk('public')->delete('produk/' . $foto->foto);
        }

        // hapus foto KTM dari storage
        if ($user->foto_ktm) {
            Storage::disk('public')->delete('ktm/' . $user->foto_ktm);
        }

        // hapus relasi yang tidak punya cascade ke produk
        DetailTransaksi::whereIn('id_produk', $produkIds)->delete();
        Review::whereIn('id_produk', $produkIds)->delete();
        ProdukFoto::whereIn('id_produk', $produkIds)->delete();

        // hapus semua produk user
        Produk::where('id_user', $userId)->delete();

        // logout dulu sebelum hapus user
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // hapus user (cascade akan hapus: user_roles, transaksi, chat, request_kategori, review, sessions)
        DB::table('users')->where('id_user', $userId)->delete();

        return redirect()->route('home')->with('success', 'Akun Anda telah dihapus. Terima kasih telah menggunakan SecondLife.');
    }
}

