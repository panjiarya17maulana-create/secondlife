<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk_foto', function (Blueprint $table) {
            $table->id('id_foto');
            $table->foreignId('id_produk')->constrained('produk', 'id_produk')->onDelete('cascade')->onUpdate('cascade');
            $table->string('foto', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk_foto');
    }
};
