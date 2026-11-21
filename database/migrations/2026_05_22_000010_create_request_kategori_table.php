<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_kategori', function (Blueprint $table) {
            $table->id('id_request');
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade')->onUpdate('cascade');
            $table->string('nama_usulan', 100);
            $table->text('alasan')->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('catatan_admin')->nullable();
            $table->integer('id_kategori')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_kategori');
    }
};
