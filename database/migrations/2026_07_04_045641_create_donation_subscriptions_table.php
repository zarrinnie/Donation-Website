<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A subscription is the recurring "monthly retention loop" a donor
     * enters after their first successful DOKU payment. Donor biodata is
     * snapshotted inline here too (no donor login), matching the donations
     * table's convention. Each individual charge (first gift or a monthly
     * renewal) is still its own `donations` row, linked back via
     * donations.subscription_id.
     */
    public function up(): void
    {
        Schema::create('donation_subscriptions', function (Blueprint $table) {
            $table->id();

            $table->string('donor_name');
            $table->string('donor_email')->index();
            $table->string('donor_phone')->nullable();

            // Suggested amount for the *next* reminder — set at enrollment,
            // refreshed to the actual paid amount after every renewal.
            $table->decimal('amount', 12, 2);
            $table->char('currency', 3)->default('IDR');

            $table->enum('status', ['active', 'cancelled'])->default('active');

            // Most recent donation (origin or latest renewal) tied to this
            // plan — lets the review page prefill without a subquery.
            $table->foreignId('last_donation_id')->nullable()
                ->constrained('donations')->nullOnDelete();

            // Forward-looking "due" marker for the monthly scheduler, checked
            // in PHP (not SQL) so behaviour matches MySQL and the SQLite test
            // database, same rationale as SendDonationRemindersAction.
            $table->timestamp('next_reminder_at')->nullable();
            $table->timestamp('last_reminder_sent_at')->nullable();

            $table->timestamps();

            $table->index(['status', 'next_reminder_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donation_subscriptions');
    }
};
