<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitor_controls', function (Blueprint $table) {
            $table->id();

            $table->string('report_code', 50)->unique();

            $table->enum('location', ['SH01','SH02']);
            $table->date('process_date');
            $table->enum('shift', ['pagi','malam']);
            $table->decimal('size', 3, 1); // 1.2 - 1.5

            $table->string('driver_name', 150);

            $table->foreignId('truck_id')->constrained('trucks')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();

            $table->decimal('farm_fee_amount', 14, 2)->default(0);

            $table->enum('status', ['draft','running','done'])->default('draft');

            $table->unsignedInteger('set_count');
            $table->unsignedInteger('shackle_count');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitor_controls');
    }
};
