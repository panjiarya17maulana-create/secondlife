<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->foreignId('id_pembeli')->constrained('users', 'id_user')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_penjual')->constrained('users', 'id_user')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('total_harga')->default(0);
            $table->enum('status', ['menunggu_konfirmasi', 'dikonfirmasi', 'selesai', 'dibatalkan', 'diproses'])->default('menunggu_konfirmasi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
