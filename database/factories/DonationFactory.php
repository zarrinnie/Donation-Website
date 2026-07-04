<?php

namespace Database\Factories;

use App\Models\Donation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Donation>
 */
class DonationFactory extends Factory
{
    protected $model = Donation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $range = fake()->randomElement([
            ['1 Day', 1],
            ['1 Month', 30],
            ['1 Year', 365],
        ]);

        return [
            'donor_name' => fake()->name(),
            'donor_age' => fake()->numberBetween(18, 75),
            'donor_email' => fake()->unique()->safeEmail(),
            'donor_phone' => fake()->numerify('+1 (###) ###-####'),
            'amount' => fake()->randomElement([25000, 50000, 100000, 150000, 250000]),
            'time_range_label' => $range[0],
            'time_range_days' => $range[1],
            'is_custom_amount' => false,
            'is_custom_range' => false,
            'status' => fake()->randomElement(Donation::STATUSES),
            'payment_method' => 'card',
            'reference' => Donation::generateReference(),
        ];
    }

    public function status(string $status): static
    {
        return $this->state(fn (array $attributes) => ['status' => $status]);
    }
}
