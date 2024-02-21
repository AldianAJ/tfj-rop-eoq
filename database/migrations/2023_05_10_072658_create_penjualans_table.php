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
        Schema::create('penjualans', function (Blueprint $table) {
            $table->string('penjualan_id')->primary();
            $table->string('slug');
            $table->string('counter_id');
            $table->integer('grand_total');
            $table->dateTime('tanggal_penjualan')->nullable();

            $table->foreign('counter_id')
                ->references('counter_id')
                ->on('counters')->onDelete('cascade');

            $table->timestamps();
            $table->index(['penjualan_id']);
            $table->index(['counter_id']);
            $table->index(['tanggal_penjualan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};
