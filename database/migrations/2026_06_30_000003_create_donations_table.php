<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Each donation stores the donor's biodata inline (no donor login).
     * The admin "User Directory" derives unique donors by grouping on
     * donor_email.
     */
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();

            // Donor biodata (collected on the public Donate page)
            $table->string('donor_name');
            $table->unsignedSmallInteger('donor_age')->nullable();
            $table->string('donor_email')->index();
            $table->string('donor_phone')->nullable();

            // Donation details
            $table->decimal('amount', 12, 2);
            $table->string('time_range_label');           // e.g. "1 Month" / "Custom: 14 days"
            $table->unsignedInteger('time_range_days')->nullable();
            $table->boolean('is_custom_amount')->default(false);
            $table->boolean('is_custom_range')->default(false);

            // Payment / status
            $table->enum('status', ['successful', 'pending', 'failed'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('reference')->unique();

            $table->timestamps();

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
