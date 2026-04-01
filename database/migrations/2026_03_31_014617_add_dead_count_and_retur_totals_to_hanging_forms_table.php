<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hanging_forms', function (Blueprint $table) {
            $table->unsignedInteger('dead_count')->default(0)->after('status');

            // rekap retur (disimpan supaya cepat dibaca di form hanging + live monitor nanti)
            $table->unsignedInteger('retur_count')->default(0)->after('dead_count');
            $table->decimal('retur_total_kg', 10, 2)->default(0)->after('retur_count');
        });
    }

    public function down(): void
    {
        Schema::table('hanging_forms', function (Blueprint $table) {
            $table->dropColumn(['dead_count', 'retur_count', 'retur_total_kg']);
        });
    }
};
