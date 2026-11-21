<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;

class AdminTransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with(['pembeli','detail.produk.foto','jadwalCod'])
            ->orderByDesc('created_at');

        if ($request->cari) {
            $q = $request->cari;
            $query->where(function($qb) use ($q) {
                $qb->whereHas('pembeli', fn($u) => $u->where('nama','like',"%$q%"))
                   ->orWhereHas('detail.produk', fn($p) => $p->where('nama_produk','like',"%$q%"));
            });
        }
        if ($request->status) $query->where('status', $request->status);

        // Inject penjual nama
        $transaksiList = $query->paginate(20);
        $transaksiList->each(function($t) {
            $t->setRelation('penjual', User::where('id_user',$t->id_penjual)->first());
        });

        return view('admin.transaksi', compact('transaksiList'));
    }
}
