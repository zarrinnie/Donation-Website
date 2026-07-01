<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tracks when a "come give again" reminder was sent for a donation, so the
     * scheduled reminder job sends exactly once per donation period.
     */
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->timestamp('reminder_sent_at')->nullable()->after('reference');
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn('reminder_sent_at');
        });
    }
};
