<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitor_controls', function (Blueprint $table) {
            $table->longText('supervisor_signature')->nullable()->after('status'); // data URL png
            $table->string('supervisor_signed_name')->nullable()->after('supervisor_signature');
            $table->timestamp('supervisor_signed_at')->nullable()->after('supervisor_signed_name');
        });
    }

    public function down(): void
    {
        Schema::table('monitor_controls', function (Blueprint $table) {
            $table->dropColumn(['supervisor_signature','supervisor_signed_name','supervisor_signed_at']);
        });
    }
};
