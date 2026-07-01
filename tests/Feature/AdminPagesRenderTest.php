<?php

use App\Models\Donation;
use App\Models\User;

beforeEach(function () {
    // Seed presets + a few donations so the pages have data to render.
    \App\Models\DonationSetting::create(['type' => 'amount', 'label' => '$50', 'value' => '50', 'is_active' => true, 'sort_order' => 0]);
    \App\Models\DonationSetting::create(['type' => 'time_range', 'label' => '1 Month', 'value' => '30', 'is_active' => true, 'sort_order' => 0]);
    Donation::factory()->count(5)->create();
});

it('renders every admin page for an admin user', function (string $route) {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route($route))
        ->assertOk();
})->with([
    'admin.dashboard',
    'admin.donations',
    'admin.donation-settings',
    'admin.donors',
    'admin.global-search',
]);

it('renders the public pages for guests', function (string $route) {
    $this->get(route($route))->assertOk();
})->with(['home', 'about', 'donate']);
