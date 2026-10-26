<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('defeat_report_items', function (Blueprint $table) {
            $table->unsignedInteger('sampling_count')->nullable();
            $table->unsignedInteger('head_pulling_count')->nullable();
            $table->unsignedInteger('leg_cutting_count')->nullable();
            $table->unsignedInteger('leg_pulling_count')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('defeat_report_items', function (Blueprint $table) {
            $table->dropColumn([
                'sampling_count',
                'head_pulling_count',
                'leg_cutting_count',
                'leg_pulling_count',
            ]);
        });
    }
};
