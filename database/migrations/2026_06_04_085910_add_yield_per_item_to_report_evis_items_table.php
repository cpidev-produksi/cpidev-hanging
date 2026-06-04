<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_evis_items', function (Blueprint $table) {
            $table->decimal('yield_percent', 8, 2)->nullable()->after('total_kg');
        });
    }

    public function down(): void
    {
        Schema::table('report_evis_items', function (Blueprint $table) {
            $table->dropColumn('yield_percent');
        });
    }
};
