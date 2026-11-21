<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\Kategori;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $trxTerbaru = Transaksi::with(['pembeli','detail.produk'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(fn($t) => [
                'nama_produk'  => $t->detail->first()?->produk->nama_produk ?? '-',
                'pembeli'      => $t->pembeli->nama ?? '-',
                'penjual'      => User::where('id_user',$t->id_penjual)->value('nama') ?? '-',
                'total_harga'  => $t->total_harga,
                'status'       => $t->status,
            ]);

        $userTerbaru = User::with('roles')
            ->orderByDesc('id_user')
            ->limit(5)
            ->get()
            ->map(fn($u) => [
                'nama'      => $u->nama,
                'email'     => $u->email,
                'nama_role' => $u->roles->pluck('nama_role')->first() ?? '-',
            ]);

        $penjualRole = Role::where('nama_role','penjual')->value('id_role');
        $pembeliRole = Role::where('nama_role','pembeli')->value('id_role');

        // ── Data grafik 6 bulan terakhir (Fitur #8) ──
        $bulan6 = collect(range(5, 0))->map(fn($i) => Carbon::now()->subMonths($i));

        $labelBulan = $bulan6->map(fn($b) => $b->translatedFormat('M Y'))->values();

        $dataTransaksi = $bulan6->map(fn($b) =>
            Transaksi::whereYear('created_at', $b->year)
                     ->whereMonth('created_at', $b->month)
                     ->count()
        )->values();

        $dataUserBaru = $bulan6->map(fn($b) =>
            User::whereYear('created_at', $b->year)
                ->whereMonth('created_at', $b->month)
                ->count()
        )->values();

        $dataProdukBaru = $bulan6->map(fn($b) =>
            Produk::whereYear('created_at', $b->year)
                  ->whereMonth('created_at', $b->month)
                  ->count()
        )->values();

        return view('admin.dashboard', [
            'totalUsers'     => User::count(),
            'totalProduk'    => Produk::where('status','tersedia')->count(),
            'totalTransaksi' => Transaksi::count(),
            'totalKategori'  => Kategori::count(),
            'trxSelesai'     => Transaksi::where('status','selesai')->count(),
            'totalPenjual'   => DB::table('user_roles')->where('id_role',$penjualRole)->count(),
            'totalPembeli'   => DB::table('user_roles')->where('id_role',$pembeliRole)->count(),
            'userMenunggu'   => User::where('status_akun','menunggu')->count(),
            'trxMenunggu'    => Transaksi::where('status','menunggu_konfirmasi')->count(),
            'trxTerbaru'     => $trxTerbaru,
            'userTerbaru'    => $userTerbaru,
            // Grafik
            'labelBulan'     => $labelBulan,
            'dataTransaksi'  => $dataTransaksi,
            'dataUserBaru'   => $dataUserBaru,
            'dataProdukBaru' => $dataProdukBaru,
        ]);
    }
}