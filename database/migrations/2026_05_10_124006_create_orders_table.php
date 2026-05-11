<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel users
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->string('invoice_number')->unique();
            $table->decimal('total_price', 12, 2);
            
            // Status pesanan dengan default 'pending'
            $table->enum('status', ['pending', 'paid', 'processing', 'completed', 'cancelled'])->default('pending');
            
            $table->string('payment_method');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
