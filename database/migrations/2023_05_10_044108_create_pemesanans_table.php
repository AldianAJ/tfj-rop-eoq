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
        Schema::create('pemesanans', function (Blueprint $table) {
            $table->string('pemesanan_id')->primary();
            $table->string('slug');
            $table->string('supplier_id');
            $table->string('status_pemesanan');
            $table->dateTime('tanggal_pemesanan');


            $table->foreign('supplier_id')
                ->references('supplier_id')
                ->on('suppliers')->onDelete('cascade');

            $table->timestamps();

            $table->index(['supplier_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemesanans');
    }
};
