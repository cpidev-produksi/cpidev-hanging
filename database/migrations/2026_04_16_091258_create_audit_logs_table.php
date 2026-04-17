<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            $table->nullableMorphs('auditable'); // auditable_type + auditable_id
            $table->string('form_key', 40);      // hanging_form / retur_mati / qc_kondisi
            $table->string('action', 40);        // update / finish / update_cell / backfill

            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_role')->nullable();

            $table->json('changes')->nullable(); // field changes {field: {before, after}}
            $table->json('meta')->nullable();    // info tambahan (report_code, location, truck_no, etc)

            $table->timestamps();

            $table->index(['form_key', 'action']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};