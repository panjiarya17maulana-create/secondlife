<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProdukFoto extends Model {
    protected $table = 'produk_foto';
    protected $primaryKey = 'id_foto';
    public $timestamps = false;
    protected $fillable = ['id_produk','foto'];
    public function produk() {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }
    public function url(): string {
        return asset('storage/produk/' . $this->foto);
    }
}
