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
        Schema::create('pengiriman_counters', function (Blueprint $table) {
            $table->string('pengiriman_counter_id');
            $table->primary('pengiriman_counter_id');
            $table->string('slug');
            $table->string('permintaan_counter_id');
            $table->date('tanggal_pengiriman');
            $table->date('tanggal_penerimaan')->nullable();

            $table->foreign('permintaan_counter_id')
                ->references('permintaan_counter_id')
                ->on('permintaan_counters')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengiriman_counters');
    }
};
