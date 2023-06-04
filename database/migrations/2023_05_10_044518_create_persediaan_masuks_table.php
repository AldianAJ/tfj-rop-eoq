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
        Schema::create('persediaan_masuks', function (Blueprint $table) {
            $table->string('persediaan_masuk_id');
            $table->primary('persediaan_masuk_id');
            $table->string('slug');
            $table->string('pemesanan_id');
            $table->dateTime('tanggal_persediaan_masuk');

            $table->foreign('pemesanan_id')
                ->references('pemesanan_id')
                ->on('pemesanans')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persediaan_masuks');
    }
};
