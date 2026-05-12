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
        Schema::table('cotisations', function (Blueprint $table) {
            $table->boolean('est_reserve')->default(false)->after('statut');
            $table->unsignedTinyInteger('mois')->nullable()->after('est_reserve'); // 1-12
            $table->unsignedSmallInteger('annee')->nullable()->after('mois');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cotisations', function (Blueprint $table) {
            $table->dropColumn(['est_reserve', 'mois', 'annee']);
        });
    }
};
