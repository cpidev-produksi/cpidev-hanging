<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hanging_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitor_control_id')->unique()->constrained('monitor_controls')->cascadeOnDelete();

            $table->time('unloading_time')->nullable();
            $table->time('finish_time')->nullable();

            $table->enum('status', ['draft','running','done'])->default('draft');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hanging_forms');
    }
};
