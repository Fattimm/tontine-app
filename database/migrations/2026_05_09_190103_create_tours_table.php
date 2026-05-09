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
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tontine_id')->constrained()->cascadeOnDelete();
            $table->foreignId('membre_id')->constrained()->cascadeOnDelete();
            $table->integer('numero_tour');
            $table->date('date_prevue');
            $table->date('date_effective')->nullable();
            $table->decimal('montant_recu', 10, 2)->nullable();
            $table->enum('statut', ['en_attente', 'complete', 'reporte'])->default('en_attente');
            $table->timestamps();

            $table->unique(['tontine_id', 'numero_tour']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
