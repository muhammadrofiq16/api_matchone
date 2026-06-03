<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Kalau ada data lama di quantity, pindahkan dulu ke qty
        if (Schema::hasColumn('order_items', 'quantity') && Schema::hasColumn('order_items', 'qty')) {
            DB::statement('UPDATE order_items SET qty = quantity WHERE quantity IS NOT NULL');
        }

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'quantity')) {
                $table->dropColumn('quantity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'quantity')) {
                $table->integer('quantity')->default(1)->after('qty');
            }
        });
    }
};