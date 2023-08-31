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
        Schema::create('kmeans_data_reals', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pemilik');
            $table->float('jumlah_pekerja');
            $table->string('jenis_produksi');
            $table->float('kapasitas_produksi');
            $table->float('harga_satuan');
            $table->float('nilai_produksi');
            $table->float('nilai_investasi');
            $table->float('umur');
            $table->string('pendidikan');
            $table->string('surat_izin');
            $table->string('motif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kmeans_data_reals');
    }
};
