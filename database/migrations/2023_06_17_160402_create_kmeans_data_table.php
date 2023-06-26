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
        Schema::create('kmeans_data', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pemilik');
            $table->decimal('jumlah_pekerja');
            $table->decimal('jenis_produksi');
            $table->decimal('kapasitas_produksi');
            $table->decimal('harga_satuan', 10, 2);
            $table->decimal('nilai_produksi', 10, 2);
            $table->decimal('nilai_investasi', 10, 2);
            $table->decimal('umur');
            $table->decimal('pendidikan');
            $table->decimal('surat_izin');
            $table->decimal('motif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kmeans_data');
    }
};
