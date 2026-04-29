<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_completions', function (Blueprint $table) {
            $table->id();
            $table->string('location', 10);
            $table->enum('shift', ['pagi', 'malam']);
            $table->date('process_date');
            $table->timestamp('finished_at');
            $table->integer('total_target')->default(0);
            $table->integer('total_completed')->default(0);
            $table->integer('remaining_target')->default(0);
            $table->integer('remaining_units')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['location', 'shift', 'process_date'], 'unique_shift_completion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_completions');
    }
};
