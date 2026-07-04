<?php

namespace Database\Factories;

use App\Models\DonationSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DonationSubscription>
 */
class DonationSubscriptionFactory extends Factory
{
    protected $model = DonationSubscription::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'donor_name' => fake()->name(),
            'donor_email' => fake()->unique()->safeEmail(),
            'donor_phone' => fake()->numerify('+62 ###-####-####'),
            'amount' => fake()->randomElement([25000, 50000, 100000, 150000, 250000]),
            'currency' => 'IDR',
            'status' => DonationSubscription::STATUS_ACTIVE,
            'next_reminder_at' => now()->addMonthNoOverflow(),
        ];
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => ['status' => DonationSubscription::STATUS_CANCELLED]);
    }

    public function dueForReminder(): static
    {
        return $this->state(fn (array $attributes) => ['next_reminder_at' => now()->subDay()]);
    }
}
