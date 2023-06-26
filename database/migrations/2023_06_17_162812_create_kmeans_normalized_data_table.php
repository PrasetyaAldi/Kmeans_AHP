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
        Schema::create('kmeans_normalized_data', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('data_id')->unsigned();
            $table->foreign('data_id')->references('id')->on('kmeans_data')
                ->onDelete('restrict')->onUpdate('cascade');
            $table->decimal('jumlah_pekerja', 10, 8);
            $table->decimal('jenis_produksi', 10, 8);
            $table->decimal('kapasitas_produksi', 10, 8);
            $table->decimal('harga_satuan', 10, 8);
            $table->decimal('nilai_produksi', 10, 8);
            $table->decimal('nilai_investasi', 10, 8);
            $table->decimal('umur', 10, 8);
            $table->decimal('pendidikan', 10, 8);
            $table->decimal('surat_izin', 10, 8);
            $table->decimal('motif', 10, 8);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kmeans_normalized_data');
    }
};
