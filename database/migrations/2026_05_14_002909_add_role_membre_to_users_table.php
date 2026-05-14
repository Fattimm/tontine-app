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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'organisateur', 'membre'])
                ->default('membre')
                ->after('email');
            $table->foreignId('membre_id')
                ->nullable()
                ->after('role')
                ->constrained('membres')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['membre_id']);
            $table->dropColumn(['role', 'membre_id']);
        });
    }
};
