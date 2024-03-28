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
        Schema::create('barang_gudangs', function (Blueprint $table) {
            $table->string('barang_gudang_id')->primary();
            $table->string('slug');
            $table->string('gudang_id');
            $table->string('barang_id');
            $table->integer('stok_awal')->default(0);
            $table->integer('stok_masuk')->default(0);
            $table->integer('stok_keluar')->default(0);

            $table->foreign('barang_id')
                ->references('barang_id')
                ->on('barangs')->onDelete('cascade');

            $table->foreign('gudang_id')
                ->references('gudang_id')
                ->on('gudangs')->onDelete('cascade');

            $table->timestamps();

            $table->index(['barang_gudang_id', 'barang_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_gudangs');
    }
};
