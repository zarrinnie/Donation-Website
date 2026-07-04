<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds the columns needed to back a real DOKU checkout: the plan this
     * donation belongs to (if any), DOKU's own invoice id (distinct from our
     * internal `reference`), the hosted checkout URL and raw payloads for
     * audit/debug, when DOKU confirmed payment, and whether `status` was
     * last changed by the DOKU webhook or an admin manual override.
     */
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->foreignId('subscription_id')->nullable()->after('reminder_sent_at')
                ->constrained('donation_subscriptions')->nullOnDelete();

            $table->string('doku_invoice_number')->nullable()->unique()->after('reference');
            $table->text('doku_payment_url')->nullable()->after('doku_invoice_number');
            $table->json('doku_response_payload')->nullable()->after('doku_payment_url');
            $table->json('doku_webhook_payload')->nullable()->after('doku_response_payload');
            $table->timestamp('paid_at')->nullable()->after('doku_webhook_payload');
            $table->string('status_source')->nullable()->after('paid_at'); // 'doku_webhook' | 'admin_manual'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('subscription_id');
            $table->dropColumn([
                'doku_invoice_number',
                'doku_payment_url',
                'doku_response_payload',
                'doku_webhook_payload',
                'paid_at',
                'status_source',
            ]);
        });
    }
};
