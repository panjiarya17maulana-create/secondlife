<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\ProdukFoto;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    public function detail($id)
    {
        $produk = Produk::with(['kategori', 'penjual.sellerReviewsReceived', 'foto', 'review.user'])
            ->findOrFail($id);

        $produkLain = Produk::with(['foto'])
            ->where('id_kategori', $produk->id_kategori)
            ->where('status', 'tersedia')
            ->where('id_produk', '!=', $id)
            ->limit(6)->get();

        return view('produk.detail', compact('produk', 'produkLain'));
    }

    public function tambah()
    {
        $kategori = Kategori::orderBy('nama_kategori')->get();
        return view('produk.tambah', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:200',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'deskripsi'   => 'nullable|string',
            'harga'       => 'required|integer|min:0',
            'kondisi'     => 'required|in:Baru,Bekas',
            'foto'        => 'required|array|min:1',
            'foto.*'      => 'image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        $user = Auth::user();

        $produk = Produk::create([
            'id_user'     => $user->id_user,
            'id_kategori' => $request->id_kategori,
            'nama_produk' => $request->nama_produk,
            'deskripsi'   => $request->deskripsi,
            'harga'       => $request->harga,
            'kondisi'     => $request->kondisi,
            'status'      => 'tersedia',
            'created_at'  => now(),
        ]);

        foreach ($request->file('foto') as $file) {
            $nama = 'produk_' . $produk->id_produk . '_' . uniqid() . '.' . $file->extension();
            $file->storeAs('produk', $nama, 'public');
            ProdukFoto::create(['id_produk' => $produk->id_produk, 'foto' => $nama]);
        }

        return redirect()->route('penjual.dashboard')->with('success', '✅ Produk berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $user   = Auth::user();
        $produk = Produk::with('foto')
            ->where('id_produk', $id)
            ->where('id_user', $user->id_user)
            ->firstOrFail();

        $kategori = Kategori::orderBy('nama_kategori')->get();
        return view('produk.edit', compact('produk', 'kategori'));
    }

    public function update(Request $request, $id)
    {
        $user   = Auth::user();
        $produk = Produk::where('id_produk', $id)
            ->where('id_user', $user->id_user)
            ->firstOrFail();

        $request->validate([
            'nama_produk' => 'required|string|max:200',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'deskripsi'   => 'nullable|string',
            'harga'       => 'required|integer|min:0',
            'kondisi'     => 'required|in:Baru,Bekas',
            'status'      => 'required|in:tersedia,habis',
            'foto_baru.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        $produk->update($request->only('nama_produk', 'id_kategori', 'deskripsi', 'harga', 'kondisi', 'status'));

        // Hapus foto yang dipilih
        if ($request->has('hapus_foto')) {
            foreach ($request->hapus_foto as $fotoId) {
                $foto = ProdukFoto::find($fotoId);
                if ($foto && $foto->id_produk == $id) {
                    Storage::disk('public')->delete('produk/' . $foto->foto);
                    $foto->delete();
                }
            }
        }

        // Tambah foto baru
        if ($request->hasFile('foto_baru')) {
            foreach ($request->file('foto_baru') as $file) {
                $nama = 'produk_' . $id . '_' . uniqid() . '.' . $file->extension();
                $file->storeAs('produk', $nama, 'public');
                ProdukFoto::create(['id_produk' => $id, 'foto' => $nama]);
            }
        }

        return redirect()->route('penjual.dashboard')->with('success', '✅ Produk berhasil diperbarui!');
    }

    /**
     * Toggle publish/unpublish produk (sembunyikan/tampilkan di beranda)
     */
    public function togglePublish($id)
    {
        $user   = Auth::user();
        $produk = Produk::where('id_produk', $id)
            ->where('id_user', $user->id_user)
            ->whereIn('status', ['tersedia', 'habis', 'nonaktif'])
            ->firstOrFail();

        if ($produk->status === 'nonaktif') {
            $produk->update(['status' => 'tersedia']);
            $msg = '✅ Produk berhasil dipublikasikan kembali!';
        } else {
            $produk->update(['status' => 'nonaktif']);
            $msg = '📦 Produk disembunyikan dari beranda.';
        }

        return redirect()->route('penjual.dashboard')->with('success', $msg);
    }

    /**
     * Hapus permanen produk dari database
     */
    public function destroy($id)
    {
        $user   = Auth::user();
        $produk = Produk::where('id_produk', $id)
            ->where('id_user', $user->id_user)
            ->firstOrFail();

        // Hapus file foto dari storage
        foreach ($produk->foto as $foto) {
            Storage::disk('public')->delete('produk/' . $foto->foto);
        }

        // Hapus semua relasi terkait (foreign key constraint)
        \App\Models\DetailTransaksi::where('id_produk', $id)->delete();
        \App\Models\Review::where('id_produk', $id)->delete();
        \App\Models\ProdukFoto::where('id_produk', $id)->delete();

        // Hapus produk dari database secara permanen
        $produk->delete();

        return redirect()->route('penjual.dashboard')->with('success', '🗑️ Produk dihapus permanen dari database.');
    }
}

