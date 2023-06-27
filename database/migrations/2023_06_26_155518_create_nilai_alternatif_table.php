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
        Schema::create('bobot_alternatif', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('normalize_id')->constrained();
            $table->foreign('normalize_id')->references('id')->on('kmeans_normalized_data')->restrictOnDelete()->cascadeOnUpdate();
            $table->bigInteger('criteria_id')->constrained();
            $table->foreign('criteria_id')->references('id')->on('criterias')->restrictOnDelete()->cascadeOnUpdate();
            $table->decimal('eigen_value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_alternatif');
    }
};
