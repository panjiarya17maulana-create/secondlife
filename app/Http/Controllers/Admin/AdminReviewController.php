<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerReview;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = SellerReview::with(['reviewer', 'seller'])->latest();

        // Filter pencarian
        if ($request->cari) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->whereHas('reviewer', fn($u) => $u->where('nama', 'like', "%$cari%"))
                  ->orWhereHas('seller',   fn($u) => $u->where('nama', 'like', "%$cari%"))
                  ->orWhere('comment', 'like', "%$cari%");
            });
        }

        // Filter rating
        if ($request->rating) {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->paginate(10)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function destroy($id)
    {
        $review = SellerReview::findOrFail($id);
        $review->delete();

        return back()->with('success', '🗑️ Ulasan berhasil dihapus.');
    }
}
