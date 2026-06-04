<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_evis', function (Blueprint $table) {
            $table->decimal('netto_weight', 12, 2)->nullable()->after('yield_percent');
        });
    }

    public function down(): void
    {
        Schema::table('report_evis', function (Blueprint $table) {
            $table->dropColumn('netto_weight');
        });
    }
};
