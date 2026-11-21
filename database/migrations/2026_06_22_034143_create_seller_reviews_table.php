<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_reviews', function (Blueprint $table) {
            $table->id();
            // Pembeli yang memberikan review
            $table->unsignedBigInteger('reviewer_id');
            // Penjual yang menerima review
            $table->unsignedBigInteger('seller_id');
            // Data Review
            $table->integer('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->foreign('reviewer_id')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('seller_id')->references('id_user')->on('users')->onDelete('cascade');

            // Satu pembeli hanya bisa review satu kali per penjual
            $table->unique(['reviewer_id', 'seller_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_reviews');
    }
};
