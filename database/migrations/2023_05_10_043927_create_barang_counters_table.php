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
        Schema::create('barang_counters', function (Blueprint $table) {
            $table->string('barang_counter_id');
            $table->primary('barang_counter_id');
            $table->string('slug');
            $table->string('counter_id');
            $table->string('barang_id');
            $table->integer('stok_awal');
            $table->integer('stok_masuk');
            $table->integer('stok_keluar');

            $table->foreign('barang_id')
                ->references('barang_id')
                ->on('barangs')->onDelete('cascade');

            $table->foreign('counter_id')
                ->references('counter_id')
                ->on('counters')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_counters');
    }
};
