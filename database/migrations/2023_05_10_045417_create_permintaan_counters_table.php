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
        Schema::create('permintaan_counters', function (Blueprint $table) {
            $table->string('permintaan_counter_id')->primary();
            $table->string('slug');
            $table->string('counter_id');
            $table->string('status_permintaan');
            $table->dateTime('tanggal_permintaan');

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
        Schema::dropIfExists('permintaan_counters');
    }
};
