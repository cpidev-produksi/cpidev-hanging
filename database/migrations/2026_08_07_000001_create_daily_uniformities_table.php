<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_uniformities', function (Blueprint $table) {
            $table->id();

            // Sumber data: satu laporan uniformity dibuat dari satu record Monitor Control
            // (dipilih berdasarkan No. SPPA). unique() -> 1 monitor control hanya boleh
            // punya 1 laporan uniformity.
            $table->foreignId('monitor_control_id')
                ->unique()
                ->constrained('monitor_controls')
                ->cascadeOnDelete();

            // Denormalisasi ringan supaya listing "per tanggal" cepat & mudah difilter
            // tanpa join ke monitor_controls setiap saat.
            $table->date('process_date');
            $table->string('shift', 10);
            $table->string('location', 10)->nullable();

            // Input manual oleh user saat create/edit
            $table->decimal('avg_rpa', 8, 2)->nullable();

            $table->timestamps();

            $table->index(['process_date', 'shift']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_uniformities');
    }
};
