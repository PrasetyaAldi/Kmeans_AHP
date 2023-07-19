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
        Schema::create('kmeans_centroids', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('normalize_id');
            $table->foreign('normalize_id')->references('id')->on('kmeans_normalized_data')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('cluster');
            $table->decimal('nilai_sse');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kmeans_centroids');
    }
};
