<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            // Menyambungkan ke tabel orders
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            // Menyambungkan ke tabel products (Pastikan kamu sudah punya tabel products!)
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            
            $table->integer('qty');
            $table->decimal('price_at_purchase', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};