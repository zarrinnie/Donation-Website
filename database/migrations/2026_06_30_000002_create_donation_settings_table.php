<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Preset donation options shown on the public Donate page. `type`
     * distinguishes a preset amount from a preset time range. For amounts,
     * `value` holds the money amount; for time ranges, `value` holds the
     * number of days the pledge spans.
     */
    public function up(): void
    {
        Schema::create('donation_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['amount', 'time_range']);
            $table->string('label');
            $table->string('value'); // amount (e.g. "50") or days (e.g. "30")
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['type', 'is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donation_settings');
    }
};
