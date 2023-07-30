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
            $table->float('jumlah_pekerja');
            $table->float('jenis_produksi');
            $table->float('kapasitas_produksi');
            $table->float('harga_satuan');
            $table->float('nilai_produksi');
            $table->float('nilai_investasi');
            $table->float('umur');
            $table->float('pendidikan');
            $table->float('surat_izin');
            $table->float('motif');
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
