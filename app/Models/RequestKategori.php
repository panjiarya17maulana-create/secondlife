<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RequestKategori extends Model {
    protected $table = 'request_kategori';
    protected $primaryKey = 'id_request';
    public $timestamps = false;
    protected $fillable = ['id_user','nama_usulan','alasan','status','catatan_admin','id_kategori','created_at'];
    protected $casts = ['created_at' => 'datetime'];
    public function user() {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
    public function kategori() {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }
}
