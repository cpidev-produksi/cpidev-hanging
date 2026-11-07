<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hanging_forms', function (Blueprint $table) {
            // Dibuat oleh (diisi checker lewat modal saat menekan "Selesai")
            $table->string('creator_name', 120)->nullable()->after('feather_condition');
            $table->unsignedBigInteger('creator_user_id')->nullable()->after('creator_name');

            // Diketahui oleh (Review - Foreman)
            $table->string('known_name', 120)->nullable()->after('creator_user_id');
            $table->unsignedBigInteger('known_user_id')->nullable()->after('known_name');
            $table->timestamp('known_at')->nullable()->after('known_user_id');

            // Disetujui oleh (Approve - Supervisor)
            $table->string('approved_name', 120)->nullable()->after('known_at');
            $table->unsignedBigInteger('approved_user_id')->nullable()->after('approved_name');
            $table->timestamp('approved_at')->nullable()->after('approved_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('hanging_forms', function (Blueprint $table) {
            $table->dropColumn([
                'creator_name', 'creator_user_id',
                'known_name', 'known_user_id', 'known_at',
                'approved_name', 'approved_user_id', 'approved_at',
            ]);
        });
    }
};