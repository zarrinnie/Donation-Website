<?php

use App\Livewire\Admin\GlobalSearch;
use App\Models\Donation;
use App\Models\User;
use Livewire\Livewire;

it('finds donations by donor name, email, or reference', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);
    $match = Donation::factory()->create(['donor_name' => 'Mary Donor', 'reference' => 'GCC-FINDME123']);
    Donation::factory()->create(['donor_name' => 'Someone Else', 'reference' => 'GCC-OTHER0000']);

    Livewire::actingAs($admin)
        ->test(GlobalSearch::class)
        ->set('search', 'Mary Donor')
        ->assertViewHas('donations', fn ($d) => $d->pluck('id')->contains($match->id) && $d->count() === 1)
        ->set('search', 'FINDME')
        ->assertViewHas('donations', fn ($d) => $d->pluck('id')->contains($match->id) && $d->count() === 1);
});

it('constrains user results by role (precedence bug fixed)', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);
    // Both contain "Alex" in their name; only one is an admin.
    $adminAlex = User::factory()->create(['name' => 'Alex Admin', 'role' => 'admin']);
    User::factory()->create(['name' => 'Alex User', 'role' => 'user']);

    Livewire::actingAs($admin)
        ->test(GlobalSearch::class)
        ->set('search', 'Alex')
        ->set('filterUserRole', 'admin')
        ->assertViewHas('users', fn ($u) => $u->pluck('id')->contains($adminAlex->id)
            && $u->count() === 1
            && $u->every(fn ($user) => $user->role === 'admin'));
});
