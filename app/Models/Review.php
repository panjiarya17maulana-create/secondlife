<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Review extends Model {
    protected $table = 'review';
    protected $primaryKey = 'id_review';
    public $timestamps = false;
    protected $fillable = ['id_produk','id_user','id_penjual','rating','komentar','balasan_penjual','created_at'];
    protected $casts = ['created_at' => 'datetime'];
    public function user() {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
    public function produk() {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }
}
