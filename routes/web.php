<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\RequestKategoriController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SellerReviewController;
use App\Http\Controllers\Penjual\PenjualController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminProdukController;
use App\Http\Controllers\Admin\AdminTransaksiController;
use App\Http\Controllers\Admin\AdminKategoriController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminVerifikasiController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

// ── PUBLIC ──────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/produk/{id}', [ProdukController::class, 'detail'])->name('produk.detail');
Route::get('/penjual/{sellerId}/ulasan', [SellerReviewController::class, 'profile'])->name('seller.profile');

// ── AUTH ────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Forgot Password & Reset Password
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

// Daftar Penjual (bisa diakses user yang sudah login maupun belum login)
Route::get('/daftar-penjual', [AuthController::class, 'showDaftarPenjual'])->name('daftar.penjual');
Route::post('/daftar-penjual', [AuthController::class, 'daftarPenjual']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── USER (login required) ────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Profil
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
    Route::post('/profil', [ProfilController::class, 'update'])->name('profil.update');
    Route::delete('/profil', [ProfilController::class, 'destroy'])->name('profil.destroy');

    // Checkout & Transaksi
    Route::get('/checkout/{id}', [TransaksiController::class, 'checkout'])->name('checkout');
    Route::post('/checkout/{id}', [TransaksiController::class, 'prosesCheckout'])->name('checkout.proses');
    Route::get('/checkout-sukses/{id}', [TransaksiController::class, 'sukses'])->name('checkout.sukses');
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/{id}', [TransaksiController::class, 'detail'])->name('transaksi.detail');
    Route::post('/transaksi/{id}/aksi', [TransaksiController::class, 'aksi'])->name('transaksi.aksi');
    Route::put('/transaksi/{id}/jadwal-cod', [TransaksiController::class, 'editJadwalCod'])->name('transaksi.jadwal.edit');

    // Request Kategori
    Route::get('/request-kategori', [RequestKategoriController::class, 'index'])->name('request.kategori');
    Route::post('/request-kategori', [RequestKategoriController::class, 'store'])->name('request.kategori.store');

    // Dismiss notifikasi penolakan penjual
    Route::post('/notif/dismiss-tolak-penjual', function () {
        auth()->user()->update(['catatan_admin' => null]);
        return back();
    })->name('notif.dismiss.tolak_penjual');

    // Chat
    Route::get('/transaksi/{id}/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/transaksi/{id}/chat', [ChatController::class, 'store'])->name('chat.store');

    // Review produk
    Route::post('/produk/{id}/review', [ReviewController::class, 'store'])->name('review.store');

    // Review penjual (seller review)
    Route::post('/penjual/{sellerId}/review', [SellerReviewController::class, 'store'])->name('seller.review.store');

    // ── PENJUAL ──────────────────────────────────────────────────
    Route::middleware('role:penjual')->prefix('penjual')->name('penjual.')->group(function () {
        Route::get('/dashboard', [PenjualController::class, 'dashboard'])->name('dashboard');

        Route::get('/produk/tambah', [ProdukController::class, 'tambah'])->name('produk.tambah');
        Route::post('/produk/tambah', [ProdukController::class, 'store'])->name('produk.store');
        Route::get('/produk/{id}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
        Route::put('/produk/{id}', [ProdukController::class, 'update'])->name('produk.update');
        Route::patch('/produk/{id}/toggle-publish', [ProdukController::class, 'togglePublish'])->name('produk.togglePublish');
        Route::delete('/produk/{id}', [ProdukController::class, 'destroy'])->name('produk.hapus');

        Route::post('/transaksi/{id}/aksi', [PenjualController::class, 'aksiTransaksi'])->name('transaksi.aksi');
        Route::post('/review/{id}/balas', [ReviewController::class, 'balas'])->name('review.balas');
    });
});

// ── ADMIN ────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/produk', [AdminProdukController::class, 'index'])->name('produk.index');
    Route::patch('/produk/{id}/toggle-publish', [AdminProdukController::class, 'togglePublish'])->name('produk.togglePublish');
    Route::delete('/produk/{id}', [AdminProdukController::class, 'destroy'])->name('produk.hapus');

    Route::get('/transaksi', [AdminTransaksiController::class, 'index'])->name('transaksi.index');

    Route::get('/kategori', [AdminKategoriController::class, 'index'])->name('kategori.index');
    Route::post('/kategori', [AdminKategoriController::class, 'store'])->name('kategori.store');
    Route::delete('/kategori/{id}', [AdminKategoriController::class, 'destroy'])->name('kategori.hapus');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users/{id}/aksi', [AdminUserController::class, 'aksi'])->name('users.aksi');

    Route::get('/verifikasi-penjual', [AdminVerifikasiController::class, 'index'])->name('verifikasi.index');
    Route::post('/verifikasi-penjual/{id}', [AdminVerifikasiController::class, 'proses'])->name('verifikasi.proses');

    Route::get('/request-kategori', [AdminKategoriController::class, 'requests'])->name('kategori.requests');
    Route::post('/request-kategori/{id}', [AdminKategoriController::class, 'prosesRequest'])->name('kategori.proses');

    // Moderasi ulasan penjual
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::delete('/reviews/{id}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
});
