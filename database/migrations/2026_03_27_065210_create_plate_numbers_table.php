<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plate_numbers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('expedition_id')
                ->constrained('expeditions')
                ->cascadeOnDelete();

            $table->string('plate_number', 15)->unique();

            $table->timestamps();

            $table->index('expedition_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plate_numbers');
    }
};
