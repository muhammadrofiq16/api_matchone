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
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom role untuk membedakan admin dan user biasa
            // Diletakkan setelah kolom 'password' agar urutan di database lebih rapi
            $table->string('role')->default('user')->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menghapus kolom role jika perintah 'php artisan migrate:rollback' dijalankan
            $table->dropColumn('role');
        });
    }
};