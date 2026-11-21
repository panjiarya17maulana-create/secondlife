<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review', function (Blueprint $table) {
            $table->id('id_review');
            $table->foreignId('id_produk')->constrained('produk', 'id_produk')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_penjual')->constrained('users', 'id_user')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('rating');
            $table->text('komentar');
            $table->text('balasan_penjual')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review');
    }
};
