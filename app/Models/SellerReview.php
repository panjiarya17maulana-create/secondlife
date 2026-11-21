<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerReview extends Model
{
    protected $table = 'seller_reviews';

    protected $fillable = ['reviewer_id', 'seller_id', 'rating', 'comment'];

    // Pembeli yang memberi review
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id', 'id_user');
    }

    // Penjual yang menerima review
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id', 'id_user');
    }
}
