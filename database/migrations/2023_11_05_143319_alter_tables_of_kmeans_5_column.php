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
        // kmeans_data
        Schema::table('kmeans_data', function (Blueprint $table) {
            $table->dropColumn('jenis_produksi');
            $table->dropColumn('harga_satuan');
            $table->dropColumn('umur');
            $table->dropColumn('pendidikan');
            $table->dropColumn('motif');
        });

        // kmeans_data
        Schema::table('kmeans_data_reals', function (Blueprint $table) {
            $table->dropColumn('jenis_produksi');
            $table->dropColumn('harga_satuan');
            $table->dropColumn('umur');
            $table->dropColumn('pendidikan');
            $table->dropColumn('motif');
        });

        // kmeans_data
        Schema::table('kmeans_normalized_data', function (Blueprint $table) {
            $table->dropColumn('jenis_produksi');
            $table->dropColumn('harga_satuan');
            $table->dropColumn('umur');
            $table->dropColumn('pendidikan');
            $table->dropColumn('motif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kmeans_data', function (Blueprint $table) {
            $table->float('jenis_produksi');
            $table->float('harga_satuan');
            $table->float('umur');
            $table->float('pendidikan');
            $table->float('motif');
        });
        Schema::table('kmeans_data_reals', function (Blueprint $table) {
            $table->float('jenis_produksi');
            $table->float('harga_satuan');
            $table->float('umur');
            $table->string('pendidikan');
            $table->string('motif');
        });
        Schema::table('kmeans_normalized_data', function (Blueprint $table) {
            $table->decimal('jenis_produksi', 10, 8);
            $table->decimal('harga_satuan', 10, 8);
            $table->decimal('umur', 10, 8);
            $table->decimal('pendidikan', 10, 8);
            $table->decimal('motif', 10, 8);
        });
    }
};
