<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah enum status untuk menambahkan 'nonaktif'
        DB::statement("ALTER TABLE produk MODIFY COLUMN status ENUM('tersedia', 'habis', 'nonaktif', 'dihapus') DEFAULT 'tersedia'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE produk MODIFY COLUMN status ENUM('tersedia', 'habis', 'dihapus') DEFAULT 'tersedia'");
    }
};
