<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_cod', function (Blueprint $table) {
            $table->id('id_jadwal');
            $table->foreignId('id_transaksi')->unique()->constrained('transaksi', 'id_transaksi')->onDelete('cascade')->onUpdate('cascade');
            $table->date('tanggal');
            $table->string('hari', 20);
            $table->time('waktu');
            $table->string('lokasi_cod', 255);
            $table->text('catatan')->nullable();
            $table->enum('status_penjual', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('alasan_tolak')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_cod');
    }
};
