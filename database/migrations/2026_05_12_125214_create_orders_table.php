<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        
        // PASTIKAN BARIS INI ADA
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
        
        $table->string('invoice_number')->unique();
        $table->decimal('total_price', 15, 2);
        $table->enum('status', ['pending', 'paid', 'processing', 'completed', 'cancelled'])->default('pending');
        $table->string('payment_method');
        $table->timestamps();
    });
}
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};