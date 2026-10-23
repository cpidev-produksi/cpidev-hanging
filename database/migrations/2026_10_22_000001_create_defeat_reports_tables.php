<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('defeat_reports', function (Blueprint $t) {
            $t->id();
            $t->date('report_date');                    // tanggal operasional
            $t->string('shift', 10);                    // pagi | malam
            $t->string('location', 10);                 // SH01 | SH02
            $t->string('status', 20)->default('draft'); // draft | known | approved

            $t->foreignId('created_by')->constrained('users');          // Dibuat oleh (Checker)
            $t->foreignId('known_by')->nullable()->constrained('users')->nullOnDelete(); // Diketahui oleh (Foreman/lady)
            $t->timestamp('known_at')->nullable();
            $t->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete(); // Disetujui oleh (Supervisor)
            $t->timestamp('approved_at')->nullable();

            $t->timestamps();

            // 1 dokumen per tanggal + shift + lokasi (di dalamnya banyak farm/batch)
            $t->unique(['report_date', 'shift', 'location'], 'defeat_reports_unique_shift');
            $t->index(['report_date', 'location']);
        });

        Schema::create('defeat_report_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('defeat_report_id')->constrained('defeat_reports')->cascadeOnDelete();
            $t->foreignId('monitor_control_id')->nullable()->constrained('monitor_controls')->nullOnDelete();

            $t->string('farm_name');                    // snapshot nama farm saat diisi
            $t->string('size', 20);

            $t->string('time_start', 5);                // H:i hanging mulai
            $t->string('time_end', 5);                  // H:i hanging selesai
            $t->decimal('avg_dta', 5, 2)->nullable();   // kg

            $t->unsignedInteger('speed_live_bird')->nullable(); // BPH
            $t->unsignedInteger('speed_evis_c')->nullable();    // BPH

            // format "mm.ss" (menit.detik)
            $t->string('bleeding_time', 5)->nullable();
            $t->string('scalder1_time', 5)->nullable();
            $t->string('scalder2_time', 5)->nullable();
            $t->string('scalder3_time', 5)->nullable();

            // pembacaan berulang per parameter: {"amp":[0.7,0.8], "volt":[40,41], ...}
            $t->json('samples')->nullable();

            $t->unsignedSmallInteger('sort')->default(0);
            $t->timestamps();

            $t->index('monitor_control_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('defeat_report_items');
        Schema::dropIfExists('defeat_reports');
    }
};
