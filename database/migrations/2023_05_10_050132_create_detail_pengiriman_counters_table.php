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
        Schema::create('detail_pengiriman_counters', function (Blueprint $table) {
            $table->id();
            $table->string('pengiriman_counter_id');
            $table->string('barang_id');
            $table->string('persetujuan');
            $table->integer('jumlah_pengiriman')->nullable();
            $table->string('gudang_id')->nullable();
            $table->string('counter_id')->nullable();
            $table->string('catatan')->nullable();
            $table->string('status_pengiriman')->nullable();

            $table->foreign('pengiriman_counter_id')
                ->references('pengiriman_counter_id')
                ->on('pengiriman_counters')->onDelete('cascade');

            $table->foreign('barang_id')
                ->references('barang_id')
                ->on('barangs')->onDelete('cascade');

            $table->foreign('gudang_id')
                ->references('gudang_id')
                ->on('gudangs')->onDelete('cascade');

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
        Schema::dropIfExists('detail_pengiriman_counters');
    }
};
