<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('hanging_line_sets', function (Blueprint $table) {
            $table->unsignedTinyInteger('empty_count')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('hanging_line_sets', function (Blueprint $table) {
            $table->unsignedTinyInteger('empty_count')->nullable(false)->default(0)->change();
        });
    }
};