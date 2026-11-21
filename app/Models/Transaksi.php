<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model {
    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';
    public $timestamps = false;
    protected $fillable = ['id_pembeli','id_penjual','total_harga','status','foto_bukti','created_at'];
    protected $casts = ['created_at' => 'datetime'];

    public function pembeli() {
        return $this->belongsTo(User::class, 'id_pembeli', 'id_user');
    }
    public function penjual() {
        return $this->belongsTo(User::class, 'id_penjual', 'id_user');
    }
    public function detail() {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi', 'id_transaksi');
    }
    public function jadwalCod() {
        return $this->hasOne(JadwalCod::class, 'id_transaksi', 'id_transaksi');
    }
    public function chat() {
        return $this->hasMany(Chat::class, 'id_transaksi', 'id_transaksi');
    }
    public function labelStatus(): string {
        return match($this->status) {
            'menunggu_konfirmasi' => '⏳ Menunggu Konfirmasi',
            'dikonfirmasi'        => '✅ Dikonfirmasi — Siap COD',
            'selesai'             => '🎉 Selesai',
            'dibatalkan'          => '❌ Dibatalkan',
            default               => ucfirst($this->status),
        };
    }
    public function formatTotal(): string {
        return 'Rp ' . number_format($this->total_harga, 0, ',', '.');
    }
}
