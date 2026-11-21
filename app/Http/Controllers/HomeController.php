<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $q        = $request->get('q', '');
        $katId    = (int) $request->get('kategori', 0);
        $kondisi  = in_array($request->get('kondisi'), ['Baru', 'Bekas']) ? $request->get('kondisi') : '';
        $minHarga = (int) $request->get('min_harga', 0);
        $maxHarga = (int) $request->get('max_harga', 0);
        $sort     = $request->get('sort', '');

        $kategoriList = Kategori::withCount(['produk' => function ($q) {
            $q->where('status', 'tersedia');
        }])->orderBy('nama_kategori')->get();

        $query = Produk::with(['kategori', 'penjual.sellerReviewsReceived', 'foto'])
            ->where('status', 'tersedia');

        if ($q) {
            $query->where(function ($qb) use ($q) {
                $qb->where('nama_produk', 'like', "%$q%")
                   ->orWhereHas('penjual', fn($u) => $u->where('nama', 'like', "%$q%"));
            });
        }

        if ($katId)    $query->where('id_kategori', $katId);
        if ($kondisi)  $query->where('kondisi', $kondisi);
        if ($minHarga) $query->where('harga', '>=', $minHarga);
        if ($maxHarga) $query->where('harga', '<=', $maxHarga);

        $query = match ($sort) {
            'termurah' => $query->orderBy('harga'),
            'termahal' => $query->orderByDesc('harga'),
            default    => $query->orderByDesc('id_produk'),
        };

        $produkList  = $query->limit(60)->get();
        $totalProduk = $kategoriList->sum('produk_count');

        return view('home', compact('produkList', 'kategoriList', 'totalProduk', 'q', 'katId', 'kondisi', 'minHarga', 'maxHarga', 'sort'));
    }
}
