<?php
namespace App\Http\Controllers;
use App\Models\Review;
use App\Models\Produk;
use App\Models\SellerReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller {
    public function store(Request $request, $id) {
        $user   = Auth::user();
        $produk = Produk::findOrFail($id);
        $request->validate([
            'rating'   => 'required|integer|min:1|max:5',
            'komentar' => 'required|string|max:500',
        ]);

        // 1. Simpan ulasan produk (seperti sebelumnya)
        Review::updateOrCreate(
            ['id_produk' => $id, 'id_user' => $user->id_user],
            [
                'id_penjual' => $produk->id_user,
                'rating'     => $request->rating,
                'komentar'   => $request->komentar,
                'created_at' => now(),
            ]
        );

        // 2. Otomatis update reputasi penjual di seller_reviews
        //    Satu pembeli = satu entri per penjual (updateOrCreate)
        if ($user->id_user !== $produk->id_user) {
            SellerReview::updateOrCreate(
                [
                    'reviewer_id' => $user->id_user,
                    'seller_id'   => $produk->id_user,
                ],
                [
                    'rating'  => $request->rating,
                    'comment' => $request->komentar,
                ]
            );
        }

        return back()->with('success', '✅ Review berhasil dikirim!');
    }

    public function balas(Request $request, $id) {
        $user   = Auth::user();
        $review = Review::where('id_review', $id)->where('id_penjual', $user->id_user)->firstOrFail();
        $request->validate(['balasan' => 'required|string|max:500']);
        $review->update(['balasan_penjual' => $request->balasan]);
        return back()->with('success', '✅ Balasan dikirim!');
    }
}
