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
        Schema::create('detail_permintaan_counters', function (Blueprint $table) {
            $table->id();
            $table->string('permintaan_counter_id');
            $table->string('barang_id');
            $table->integer('jumlah_permintaan');

            $table->foreign('permintaan_counter_id')
                ->references('permintaan_counter_id')
                ->on('permintaan_counters')->onDelete('cascade');

            $table->foreign('barang_id')
                ->references('barang_id')
                ->on('barangs')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_permintaan_counters');
    }
};
