<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminProdukController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::with(['penjual','kategori','foto'])
            ->whereIn('status', ['tersedia','habis','nonaktif'])
            ->orderByDesc('id_produk');

        if ($request->cari) {
            $q = $request->cari;
            $query->where(function($qb) use ($q) {
                $qb->where('nama_produk','like',"%$q%")
                   ->orWhereHas('penjual', fn($u) => $u->where('nama','like',"%$q%"));
            });
        }
        if ($request->status) $query->where('status', $request->status);

        $produkList = $query->paginate(20);
        return view('admin.produk', compact('produkList'));
    }

    /**
     * Toggle publish/unpublish produk (sembunyikan/tampilkan di beranda)
     */
    public function togglePublish($id)
    {
        $produk = Produk::whereIn('status', ['tersedia', 'habis', 'nonaktif'])->findOrFail($id);

        if ($produk->status === 'nonaktif') {
            $produk->update(['status' => 'tersedia']);
            $msg = '✅ Produk dipublikasikan kembali.';
        } else {
            $produk->update(['status' => 'nonaktif']);
            $msg = '📦 Produk disembunyikan dari beranda.';
        }

        return back()->with('success', $msg);
    }

    /**
     * Hapus permanen produk dari database
     */
    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);

        // Hapus file foto dari storage
        foreach ($produk->foto as $f) {
            Storage::disk('public')->delete('produk/'.$f->foto);
        }

        // Hapus semua relasi terkait (foreign key constraint)
        \App\Models\DetailTransaksi::where('id_produk', $id)->delete();
        \App\Models\Review::where('id_produk', $id)->delete();
        \App\Models\ProdukFoto::where('id_produk', $id)->delete();

        // Hapus produk dari database
        $produk->delete();

        return back()->with('success', '🗑️ Produk dihapus permanen dari database.');
    }
}
