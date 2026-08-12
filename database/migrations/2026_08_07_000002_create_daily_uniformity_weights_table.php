<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_uniformity_weights', function (Blueprint $table) {
            $table->id();

            $table->foreignId('daily_uniformity_id')
                ->constrained('daily_uniformities')
                ->cascadeOnDelete();

            // Nomor urut sampling ayam ke-1, ke-2, dst (untuk ditampilkan di tabel)
            $table->unsignedInteger('sequence')->default(1);

            // Berat ayam, satuan kg, 3 angka di belakang koma
            $table->decimal('weight_kg', 6, 3);

            $table->timestamps();

            $table->index(['daily_uniformity_id', 'sequence']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_uniformity_weights');
    }
};
