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
        Schema::create('detail_persediaan_masuks', function (Blueprint $table) {
            $table->id();
            $table->string('persediaan_masuk_id');
            $table->string('barang_id');
            $table->integer('jumlah_persediaan_masuk');

            $table->foreign('persediaan_masuk_id')
                ->references('persediaan_masuk_id')
                ->on('persediaan_masuks')->onDelete('cascade');

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
        Schema::dropIfExists('detail_persediaan_masuks');
    }
};
