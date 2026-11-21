<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\RequestKategori;
use Illuminate\Http\Request;

class AdminKategoriController extends Controller
{
    public function index()
    {
        $kategoriList = Kategori::withCount('produk')->orderBy('nama_kategori')->get();
        return view('admin.kategori', compact('kategoriList'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama_kategori' => 'required|string|max:100|unique:kategori,nama_kategori']);
        Kategori::create(['nama_kategori' => $request->nama_kategori]);
        return back()->with('success', '✅ Kategori ditambahkan!');
    }

    public function destroy($id)
    {
        $k = Kategori::findOrFail($id);
        if ($k->produk()->count() > 0) {
            return back()->with('error', 'Kategori masih memiliki produk aktif, tidak bisa dihapus.');
        }
        $k->delete();
        return back()->with('success', 'Kategori dihapus.');
    }

    public function requests(Request $request)
    {
        $query = RequestKategori::with('user','kategori')->orderByDesc('created_at');
        if ($request->status) $query->where('status', $request->status);
        $list = $query->paginate(20);
        return view('admin.request_kategori', compact('list'));
    }

    public function prosesRequest(Request $request, $id)
    {
        $reqModel = RequestKategori::findOrFail($id);
        $request->validate(['status' => 'required|in:disetujui,ditolak']);

        $katId   = null;
        $catatan = $request->catatan_admin ?? '';

        if ($request->status === 'disetujui') {
            $kat   = Kategori::firstOrCreate(['nama_kategori' => $reqModel->nama_usulan]);
            $katId = $kat->id_kategori;
            $catatan = $catatan ?: 'Kategori "'.$reqModel->nama_usulan.'" berhasil ditambahkan!';
        }

        $reqModel->update(['status' => $request->status, 'catatan_admin' => $catatan, 'id_kategori' => $katId]);
        return back()->with('success', '✅ Request diproses!');
    }
}
