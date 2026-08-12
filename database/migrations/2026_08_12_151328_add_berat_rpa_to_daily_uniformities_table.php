<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_uniformities', function (Blueprint $table) {
            $table->decimal('berat_rpa', 8, 2)->nullable()->after('avg_rpa');
        });
    }

    public function down(): void
    {
        Schema::table('daily_uniformities', function (Blueprint $table) {
            $table->dropColumn('berat_rpa');
        });
    }
};
