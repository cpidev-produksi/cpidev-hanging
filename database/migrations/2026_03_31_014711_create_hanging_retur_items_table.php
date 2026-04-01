<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hanging_retur_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hanging_form_id')->constrained('hanging_forms')->cascadeOnDelete();
            $table->decimal('weight_kg', 10, 2); // berat per ayam retur
            $table->timestamps();

            $table->index(['hanging_form_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hanging_retur_items');
    }
};
