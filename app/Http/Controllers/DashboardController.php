<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_user'    => User::count(),
            'total_produk'  => Produk::where('status', 'tersedia')->count(),
            'total_trx'     => DB::table('transaksi')->count(),
            'trx_selesai'   => DB::table('transaksi')->where('status', 'selesai')->count(),
            'total_penjual' => DB::table('user_roles')->where('id_role', 2)->count(),
            'total_pembeli' => DB::table('user_roles')->where('id_role', 3)->count(),
        ];

        $trxTerbaru = DB::table('transaksi')
            ->join('users as ub', 'transaksi.id_pembeli', '=', 'ub.id_user')
            ->join('detail_transaksi as dt', 'transaksi.id_transaksi', '=', 'dt.id_transaksi')
            ->join('produk as p', 'dt.id_produk', '=', 'p.id_produk')
            ->join('users as us', 'p.id_user', '=', 'us.id_user')
            ->select('transaksi.*', 'ub.nama as pembeli', 'us.nama as penjual', 'p.nama_produk', 'dt.harga_satuan', 'dt.qty')
            ->orderBy('transaksi.id_transaksi', 'desc')
            ->limit(5)->get();

        $userTerbaru = User::orderBy('id_user', 'desc')->limit(5)->get();

        return view('admin.index', compact('stats', 'trxTerbaru', 'userTerbaru'));
    }

    public function verifikasiPenjual(Request $request)
    {
        $filter = $request->get('status', 'menunggu');
        $users = User::where('status_akun', $filter)
            ->whereHas('roles', function($query) { $query->where('id_role', 2); })->get();

        return view('admin.verifikasi_penjual', compact('users', 'filter'));
    }

    public function prosesVerifikasi(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($request->action === 'approve') {
            $user->update(['status_akun' => 'aktif', 'catatan_admin' => $request->catatan]);
            return redirect()->back()->with('success', '✅ Berkas KTM disetujui!');
        } else {
            $user->update(['status_akun' => 'ditolak', 'catatan_admin' => $request->catatan]);
            DB::table('user_roles')->where('id_user', $user->id_user)->where('id_role', 2)->delete();
            return redirect()->back()->with('success', '❌ Berkas KTM ditolak.');
        }
    }
}