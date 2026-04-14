<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planning_lbs', function (Blueprint $table) {
            $table->id();
            $table->enum('location', ['SH01','SH02']);
            $table->date('process_date');
            $table->unsignedInteger('total_plan_chicken')->default(0);
            $table->unsignedInteger('total_plan_truck')->default(0);
            $table->timestamps();

            $table->index(['location', 'process_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planning_lbs');
    }
};
