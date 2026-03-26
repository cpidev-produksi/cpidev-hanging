<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hanging_line_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hanging_line_id')->constrained('hanging_lines')->cascadeOnDelete();
            $table->unsignedTinyInteger('set_no'); // 1..4 / 1..3
            $table->unsignedTinyInteger('empty_count')->default(0); // 0..50
            $table->timestamps();

            $table->unique(['hanging_line_id','set_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hanging_line_sets');
    }
};
