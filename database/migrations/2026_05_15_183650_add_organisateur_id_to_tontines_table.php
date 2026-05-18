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
        Schema::table('tontines', function (Blueprint $table) {
            $table->foreignId('organisateur_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tontines', function (Blueprint $table) {
            $table->dropForeign(['organisateur_id']);
            $table->dropColumn('organisateur_id');
        });
    }
};
