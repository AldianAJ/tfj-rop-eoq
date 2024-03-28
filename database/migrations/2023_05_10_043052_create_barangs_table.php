<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->string('barang_id')->primary();
            $table->string('slug');
            $table->string('nama_barang')->unique();
            $table->integer('harga_barang');
            $table->string('supplier_id');
            $table->string('quantity_satuan');
            $table->integer('konversi_quantity');
            $table->string('konversi_satuan');
            $table->integer('biaya_penyimpanan')->default(0);
            $table->integer('rop')->default(0);
            $table->integer('ss')->default(0);

            $table->timestamps();

            $table->foreign('supplier_id')
                ->references('supplier_id')
                ->on('suppliers')->onDelete('cascade');

            $table->index(['barang_id', 'supplier_id']);
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
