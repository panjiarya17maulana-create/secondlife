<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Produk;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenjualController extends Controller
{
    public function dashboard(Request $request)
    {
        $user       = Auth::user();
        $idPenjual  = $user->id_user;
        $tab        = $request->get('tab', 'produk');

        // Statistik
        $totalPendapatan = Transaksi::where('id_penjual', $idPenjual)
            ->where('status', 'selesai')->sum('total_harga');

        $statTransaksi = Transaksi::where('id_penjual', $idPenjual)
            ->selectRaw('status, COUNT(*) as n')
            ->groupBy('status')->pluck('n', 'status');

        $produkList = Produk::with('foto', 'kategori')
            ->where('id_user', $idPenjual)
            ->whereIn('status', ['tersedia', 'habis', 'nonaktif'])
            ->orderByDesc('id_produk')->get();

        $transaksiList = Transaksi::with(['detail.produk.foto', 'pembeli', 'jadwalCod'])
            ->where('id_penjual', $idPenjual)
            ->orderByDesc('created_at')->get();

        $reviewList = Review::with(['user', 'produk'])
            ->where('id_penjual', $idPenjual)
            ->orderByDesc('created_at')->get();

        return view('penjual.dashboard', compact(
            'user', 'tab', 'totalPendapatan', 'statTransaksi',
            'produkList', 'transaksiList', 'reviewList'
        ));
    }

    public function aksiTransaksi(Request $request, $id)
    {
        $user      = Auth::user();
        $transaksi = Transaksi::where('id_transaksi', $id)
            ->where('id_penjual', $user->id_user)
            ->firstOrFail();

        $aksi = $request->status_baru;

        if (!in_array($aksi, ['dikonfirmasi', 'selesai', 'dibatalkan'])) {
            return back()->with('error', 'Aksi tidak valid.');
        }

        $transaksi->update(['status' => $aksi]);

        if ($aksi === 'dibatalkan') {
            foreach ($transaksi->detail as $dt) {
                Produk::where('id_produk', $dt->id_produk)
                    ->where('status', 'habis')
                    ->update(['status' => 'tersedia']);
            }
        }

        return redirect()->route('penjual.dashboard', ['tab' => 'transaksi'])
            ->with('success', '✅ Status transaksi diperbarui!');
    }
}
