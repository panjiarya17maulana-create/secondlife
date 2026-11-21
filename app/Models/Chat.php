<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model {
    protected $table = 'chat';
    protected $primaryKey = 'id_chat';
    public $timestamps = false;
    protected $fillable = ['id_transaksi','id_pengirim','pesan','dibaca','created_at'];
    protected $casts = ['created_at' => 'datetime', 'dibaca' => 'boolean'];
    public function pengirim() {
        return $this->belongsTo(User::class, 'id_pengirim', 'id_user');
    }
    public function transaksi() {
        return $this->belongsTo(Transaksi::class, 'id_transaksi', 'id_transaksi');
    }
}
