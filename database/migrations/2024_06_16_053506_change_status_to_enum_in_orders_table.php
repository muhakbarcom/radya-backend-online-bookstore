<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Mengubah kolom status menjadi enum
            $table->enum('status', ['pending', 'processing', 'completed', 'shipped', 'cancelled'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Mengembalikan kolom status menjadi string
            $table->string('status')->default('pending')->change();
        });
    }
};
