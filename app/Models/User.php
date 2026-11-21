<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Passwords\CanResetPassword;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|User where($column, $operator = null, $value = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User find($id, $columns = array())
 * @method static \Illuminate\Database\Eloquent\Builder|User first($columns = array())
 * @method static \Illuminate\Database\Eloquent\Builder|User with($relations)
 * @method static User create(array $attributes = array())
 * @method bool update(array $attributes = array(), array $options = array())
 * @method bool hasRole(string $role)
 * @method bool isAdmin()
 * @method bool isPenjual()
 * @method bool isPembeli()
 * @method string getPrimaryRole()
 * @method BelongsToMany roles()
 * @method ?string fotoKtmUrl()
 * @method string waLink(string $pesan = '')
 */
class User extends Authenticatable implements CanResetPasswordContract
{
    use HasFactory, Notifiable, CanResetPassword;

    protected $table = 'users';
    protected $primaryKey = 'id_user';
    public $timestamps = false;

    protected $fillable = [
        'nama', 'email', 'password', 'no_hp',
        'lokasi', 'foto_ktm', 'catatan_admin', 'status_akun', 'created_at',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'password'   => 'hashed',
        'created_at' => 'datetime',
    ];

    // ── RELATIONS ─────────────────────────────────────────────
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'id_user', 'id_role');
    }

    public function produk()
    {
        return $this->hasMany(Produk::class, 'id_user', 'id_user');
    }

    public function transaksiSebagaiPembeli()
    {
        return $this->hasMany(Transaksi::class, 'id_pembeli', 'id_user');
    }

    public function transaksiSebagaiPenjual()
    {
        return $this->hasMany(Transaksi::class, 'id_penjual', 'id_user');
    }

    // Review yang DITERIMA user sebagai penjual
    public function sellerReviewsReceived()
    {
        return $this->hasMany(SellerReview::class, 'seller_id', 'id_user');
    }

    // Review yang DIBERIKAN user sebagai pembeli
    public function sellerReviewsGiven()
    {
        return $this->hasMany(SellerReview::class, 'reviewer_id', 'id_user');
    }

    public function requestKategori()
    {
        return $this->hasMany(RequestKategori::class, 'id_user', 'id_user');
    }

    // ── HELPERS ───────────────────────────────────────────────
    public function hasRole(string $role): bool
    {
        return $this->roles->pluck('nama_role')->contains($role);
    }

    public function isAdmin(): bool    { return $this->hasRole('admin'); }
    public function isPenjual(): bool  { return $this->hasRole('penjual'); }
    public function isPembeli(): bool  { return $this->hasRole('pembeli'); }

    public function getPrimaryRole(): string
    {
        $roles = $this->roles->pluck('nama_role');
        if ($roles->contains('admin'))   return 'admin';
        if ($roles->contains('penjual')) return 'penjual';
        return 'pembeli';
    }

    public function fotoKtmUrl(): ?string
    {
        return $this->foto_ktm
            ? asset('storage/ktm/' . $this->foto_ktm)
            : null;
    }

    // Rata-rata rating penjual (dari seller_reviews)
    public function avgSellerRating(): string
    {
        // Jika relasi sudah eager-loaded, pakai collection agar tidak query DB lagi
        if ($this->relationLoaded('sellerReviewsReceived')) {
            $avg = $this->sellerReviewsReceived->avg('rating');
        } else {
            $avg = $this->sellerReviewsReceived()->avg('rating');
        }
        return $avg ? number_format($avg, 1) : '0.0';
    }

    public function waLink(string $pesan = ''): string
    {
        $no = preg_replace('/[^0-9]/', '', $this->no_hp ?? '');
        if (str_starts_with($no, '0')) $no = '62' . substr($no, 1);
        $url = "https://wa.me/{$no}";
        if ($pesan) $url .= '?text=' . urlencode($pesan);
        return $url;
    }
}
