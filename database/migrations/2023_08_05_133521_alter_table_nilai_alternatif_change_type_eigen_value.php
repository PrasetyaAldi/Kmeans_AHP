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
        Schema::table('bobot_alternatif', function (Blueprint $table) {
            $table->float('eigen_value', 5, 6)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bobot_alternatif', function (Blueprint $table) {
            $table->decimal('eigen_value', 5, 6)->change();
        });
    }
};
