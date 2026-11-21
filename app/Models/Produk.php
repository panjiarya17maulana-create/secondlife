<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produk extends Model {
    use HasFactory;
    protected $table = 'produk';
    protected $primaryKey = 'id_produk';
    public $timestamps = false;
    protected $fillable = [
        'id_user','id_kategori','nama_produk','deskripsi',
        'harga','kondisi','status','created_at',
    ];

    /**
     * Cek apakah produk aktif (tampil di beranda)
     */
    public function isPublished(): bool {
        return in_array($this->status, ['tersedia', 'habis']);
    }
    protected $casts = ['created_at' => 'datetime'];

    public function kategori() {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }
    public function penjual() {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
    public function foto() {
        return $this->hasMany(ProdukFoto::class, 'id_produk', 'id_produk');
    }
    public function fotoUtama(): ?ProdukFoto {
        return $this->foto()->first();
    }
    public function review() {
        return $this->hasMany(Review::class, 'id_produk', 'id_produk');
    }
    public function avgRating(): float {
        return round($this->review()->avg('rating') ?? 0, 1);
    }
    public function formatHarga(): string {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }
}
