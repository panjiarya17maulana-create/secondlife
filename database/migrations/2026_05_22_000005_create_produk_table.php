<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id('id_produk');
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
            $table->foreignId('id_kategori')->constrained('kategori', 'id_kategori')->onUpdate('cascade');
            $table->string('nama_produk', 200);
            $table->text('deskripsi')->nullable();
            $table->bigInteger('harga')->default(0);
            $table->enum('kondisi', ['Baru', 'Bekas'])->default('Bekas');
            $table->enum('status', ['tersedia', 'habis', 'dihapus'])->default('tersedia');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
