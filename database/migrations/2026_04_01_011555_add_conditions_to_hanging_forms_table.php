<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hanging_forms', function (Blueprint $table) {
            $table->string('basket_condition', 30)->nullable()->after('retur_total_kg');
            $table->string('truck_platform_condition', 60)->nullable()->after('basket_condition');
            $table->string('feather_condition', 30)->nullable()->after('truck_platform_condition');
        });
    }

    public function down(): void
    {
        Schema::table('hanging_forms', function (Blueprint $table) {
            $table->dropColumn(['basket_condition', 'truck_platform_condition', 'feather_condition']);
        });
    }
};
