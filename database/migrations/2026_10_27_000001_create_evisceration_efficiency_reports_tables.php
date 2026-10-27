<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evisceration_efficiency_reports', function (Blueprint $table) {
            $table->id();
            $table->date('report_date');
            $table->string('shift', 10);
            $table->string('location', 10);
            $table->string('status', 20)->default('draft');
            $table->foreignId('created_by')->constrained('users');
            $table->string('checker_name')->nullable();
            $table->foreignId('known_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('known_name')->nullable();
            $table->timestamp('known_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('approved_name')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->unique(['report_date', 'shift', 'location'], 'evis_efficiency_report_date_shift_location_unique');
            $table->index(['report_date', 'location']);
        });

        Schema::create('evisceration_efficiency_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evisceration_efficiency_report_id')
                ->constrained(
                    table: 'evisceration_efficiency_reports',
                    indexName: 'evis_eff_items_report_fk'
                )
                ->cascadeOnDelete();
            $table->foreignId('monitor_control_id')->nullable()->constrained('monitor_controls')->nullOnDelete();
            $table->foreignId('farm_id')->nullable()->constrained('farms')->nullOnDelete();
            $table->string('farm_name');
            $table->string('size', 20);
            $table->string('time_start', 5);
            $table->string('time_end', 5);
            $table->unsignedInteger('sampling_count');
            $table->unsignedInteger('speed_evis');
            $table->unsignedInteger('vent_not_torn');
            $table->unsignedInteger('viscera_not_removed');
            $table->string('sprayer_status', 20);
            $table->unsignedInteger('grit_findings');
            $table->decimal('water_flow_start', 10, 2);
            $table->decimal('water_flow_end', 10, 2);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();

            $table->unique(['evisceration_efficiency_report_id', 'monitor_control_id'], 'evis_efficiency_report_monitor_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evisceration_efficiency_items');
        Schema::dropIfExists('evisceration_efficiency_reports');
    }
};
