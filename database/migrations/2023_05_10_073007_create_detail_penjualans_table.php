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
        Schema::create('detail_penjualans', function (Blueprint $table) {
            $table->id();
            $table->string('penjualan_id');
            $table->string('barang_counter_id');
            $table->integer('quantity');
            $table->integer('subtotal');

            $table->foreign('penjualan_id')
                ->references('penjualan_id')
                ->on('penjualans')->onDelete('cascade');

            $table->foreign('barang_counter_id')
                ->references('barang_counter_id')
                ->on('barang_counters')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_penjualans');
    }
};
