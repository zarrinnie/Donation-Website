<?php

namespace Database\Seeders;

use App\Models\Donation;
use App\Models\DonationSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ==========================================
        // ADMIN ACCOUNTS
        // ==========================================
        echo "Creating admin accounts...\n";

        DB::table('users')->insert([
            [
                'name' => 'Super Admin',
                'email' => 'admin@gracechurch.test',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Church Admin',
                'email' => 'staff@gracechurch.test',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ==========================================
        // DONATION PRESETS (drive the public Donate page)
        // ==========================================
        echo "Creating donation presets...\n";

        $amounts = [25000, 50000, 100000];
        foreach ($amounts as $i => $amount) {
            DonationSetting::create([
                'type' => DonationSetting::TYPE_AMOUNT,
                'label' => 'Rp '.number_format($amount, 0, ',', '.'),
                'value' => (string) $amount,
                'is_active' => true,
                'sort_order' => $i,
            ]);
        }

        $ranges = [
            ['1 Day', 1],
            ['1 Month', 30],
            ['1 Year', 365],
        ];
        foreach ($ranges as $i => [$label, $days]) {
            DonationSetting::create([
                'type' => DonationSetting::TYPE_TIME_RANGE,
                'label' => $label,
                'value' => (string) $days,
                'is_active' => true,
                'sort_order' => $i,
            ]);
        }

        // ==========================================
        // SAMPLE DONATIONS (for dashboard / ledger demo)
        // ==========================================
        echo "Creating sample donations...\n";

        Donation::factory()->count(15)->create();

        echo "\nDONE! Database seeded successfully.\n";
        echo "Super Admin: admin@gracechurch.test / password\n";
        echo "Admin:       staff@gracechurch.test / password\n";
    }
}
