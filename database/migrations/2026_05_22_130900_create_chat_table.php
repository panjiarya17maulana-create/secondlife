<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat', function (Blueprint $table) {
            $table->id('id_chat');
            $table->foreignId('id_transaksi')->constrained('transaksi', 'id_transaksi')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_pengirim')->constrained('users', 'id_user')->onDelete('cascade')->onUpdate('cascade');
            $table->text('pesan');
            $table->boolean('dibaca')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat');
    }
};
