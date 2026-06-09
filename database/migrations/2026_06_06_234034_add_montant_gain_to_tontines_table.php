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
            $table->decimal('montant_gain', 10, 2)->nullable()->after('montant_cotisation');
        });
    }

    public function down(): void
    {
        Schema::table('tontines', function (Blueprint $table) {
            $table->dropColumn('montant_gain');
        });
    }
};
