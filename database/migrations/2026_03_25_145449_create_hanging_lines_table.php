<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hanging_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hanging_form_id')->constrained('hanging_forms')->cascadeOnDelete();

            $table->unsignedInteger('line_no');
            $table->string('shackle_label', 20);

            $table->unsignedInteger('rule_min');
            $table->unsignedInteger('rule_max');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hanging_lines');
    }
};
