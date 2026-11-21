<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class JadwalCod extends Model {
    protected $table = 'jadwal_cod';
    protected $primaryKey = 'id_jadwal';
    public $timestamps = false;
    protected $fillable = [
        'id_transaksi','tanggal','hari','waktu',
        'lokasi_cod','catatan','status_penjual','alasan_tolak','created_at',
    ];
    protected $casts = ['tanggal' => 'date', 'created_at' => 'datetime'];
    public function transaksi() {
        return $this->belongsTo(Transaksi::class, 'id_transaksi', 'id_transaksi');
    }
}
