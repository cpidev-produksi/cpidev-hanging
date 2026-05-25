<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('report_evis', function (Blueprint $table) {
            $table->id();
            $table->date('report_date');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('approved_signature_path')->nullable();
            
            $table->enum('status', ['draft', 'approved'])->default('draft');
            
            $table->decimal('total_bag', 10, 2)->default(0);
            $table->decimal('total_kg', 10, 2)->default(0);
            
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('report_evis');
    }
};
