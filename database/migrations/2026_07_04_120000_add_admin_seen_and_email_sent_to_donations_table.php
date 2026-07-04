<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds two admin-facing timestamps to donations:
     *  - `admin_seen_at`: when an admin first opened this donation's detail page.
     *    Null means "newly received / unseen" — drives the NEW marker in the
     *    ledger and dashboard.
     *  - `status_email_sent_at`: when the last status-verification email was sent
     *    to the donor, so the admin can see whether a donor has been emailed.
     */
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->timestamp('admin_seen_at')->nullable()->after('status_source');
            $table->timestamp('status_email_sent_at')->nullable()->after('admin_seen_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn(['admin_seen_at', 'status_email_sent_at']);
        });
    }
};
