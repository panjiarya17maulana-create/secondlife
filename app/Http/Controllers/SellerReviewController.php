<?php

namespace App\Http\Controllers;

use App\Models\SellerReview;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerReviewController extends Controller
{
    /**
     * Halaman publik reputasi penjual — bisa dilihat siapa saja.
     */
    public function profile($sellerId)
    {
        $seller = User::whereHas('roles', function ($q) {
            $q->where('nama_role', 'penjual');
        })->where('id_user', $sellerId)->firstOrFail();

        $reviews = SellerReview::with('reviewer')
            ->where('seller_id', $sellerId)
            ->latest()
            ->get();

        // Distribusi rating (1–5)
        $distribusi = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = $reviews->where('rating', $i)->count();
            $distribusi[$i] = [
                'count'   => $count,
                'persen'  => $reviews->count() > 0 ? round($count / $reviews->count() * 100) : 0,
            ];
        }

        $avgRating = $reviews->avg('rating') ? number_format($reviews->avg('rating'), 1) : '0.0';

        return view('seller.profil', compact('seller', 'reviews', 'distribusi', 'avgRating'));
    }

    /**
     * Simpan review pembeli untuk penjual.
     */
    public function store(Request $request, $sellerId)
    {
        $reviewer = Auth::user();
        $seller   = User::where('id_user', $sellerId)->firstOrFail();

        // Pastikan pembeli tidak mereview dirinya sendiri
        if ($reviewer->id_user === $seller->id_user) {
            return back()->with('error', '❌ Kamu tidak bisa mereview dirimu sendiri.');
        }

        // Pastikan ada transaksi selesai antara pembeli dan penjual ini
        $transaksiSelesai = Transaksi::where('id_pembeli', $reviewer->id_user)
            ->where('id_penjual', $sellerId)
            ->where('status', 'selesai')
            ->exists();

        if (!$transaksiSelesai) {
            return back()->with('error', '❌ Kamu hanya bisa mereview penjual setelah transaksi selesai.');
        }

        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        SellerReview::updateOrCreate(
            [
                'reviewer_id' => $reviewer->id_user,
                'seller_id'   => $seller->id_user,
            ],
            [
                'rating'  => $request->rating,
                'comment' => $request->comment,
            ]
        );

        return back()->with('success', '⭐ Review penjual berhasil dikirim!');
    }
}
