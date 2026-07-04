<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonationSubscription extends Model
{
    /** @use HasFactory<\Database\Factories\DonationSubscriptionFactory> */
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'donor_name',
        'donor_email',
        'donor_phone',
        'amount',
        'currency',
        'status',
        'last_donation_id',
        'next_reminder_at',
        'last_reminder_sent_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'next_reminder_at' => 'datetime',
        'last_reminder_sent_at' => 'datetime',
    ];

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'subscription_id');
    }

    public function lastDonation(): BelongsTo
    {
        return $this->belongsTo(Donation::class, 'last_donation_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
