<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            // Foto bukti COD wajib diunggah sebelum transaksi bisa diselesaikan
            $table->string('foto_bukti', 255)->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn('foto_bukti');
        });
    }
};
