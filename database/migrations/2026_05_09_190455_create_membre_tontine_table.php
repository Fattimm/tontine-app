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
        Schema::create('membre_tontine', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membre_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tontine_id')->constrained()->cascadeOnDelete();
            $table->date('date_adhesion')->default(now());
            $table->enum('statut', ['actif', 'suspendu'])->default('actif');
            $table->timestamps();

            $table->unique(['membre_id', 'tontine_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membre_tontine');
    }
};
