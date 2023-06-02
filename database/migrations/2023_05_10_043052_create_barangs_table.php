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
        Schema::create('barangs', function (Blueprint $table) {
            $table->string('barang_id');
            $table->primary('barang_id');
            $table->string('slug');
            $table->string('nama_barang')->unique('unique_nama_barang');
            $table->integer('harga_barang');
            $table->integer('biaya_penyimpanan')->default(0);
            $table->integer('rop')->default(0);
            $table->integer('ss')->default(0);

            $table->timestamps();

            $table->index(['barang_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
