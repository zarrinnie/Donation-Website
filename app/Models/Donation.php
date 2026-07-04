<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Donation extends Model
{
    use HasFactory;

    public const STATUS_SUCCESSFUL = 'successful';

    public const STATUS_PENDING = 'pending';

    public const STATUS_FAILED = 'failed';

    public const STATUSES = [
        self::STATUS_SUCCESSFUL,
        self::STATUS_PENDING,
        self::STATUS_FAILED,
    ];

    public const STATUS_SOURCE_WEBHOOK = 'doku_webhook';

    public const STATUS_SOURCE_ADMIN = 'admin_manual';

    protected $fillable = [
        'donor_name',
        'donor_age',
        'donor_email',
        'donor_phone',
        'amount',
        'time_range_label',
        'time_range_days',
        'is_custom_amount',
        'is_custom_range',
        'status',
        'payment_method',
        'reference',
        'reminder_sent_at',
        'subscription_id',
        'doku_invoice_number',
        'doku_payment_url',
        'doku_response_payload',
        'doku_webhook_payload',
        'paid_at',
        'status_source',
        'admin_seen_at',
        'status_email_sent_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'donor_age' => 'integer',
        'time_range_days' => 'integer',
        'is_custom_amount' => 'boolean',
        'is_custom_range' => 'boolean',
        'reminder_sent_at' => 'datetime',
        'doku_response_payload' => 'array',
        'doku_webhook_payload' => 'array',
        'paid_at' => 'datetime',
        'admin_seen_at' => 'datetime',
        'status_email_sent_at' => 'datetime',
    ];

    /** Generate the permanent, donor-facing donation reference. */
    public static function generateReference(): string
    {
        return 'GCC-'.strtoupper(Str::random(10));
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(DonationSubscription::class, 'subscription_id');
    }

    /** Latest donations first (overview default). */
    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->latest();
    }

    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SUCCESSFUL);
    }

    /**
     * A "newly received" donation: successfully paid but not yet opened by an
     * admin. Drives the NEW marker in the ledger and dashboard.
     */
    public function isNewForAdmin(): bool
    {
        return $this->status === self::STATUS_SUCCESSFUL && $this->admin_seen_at === null;
    }

    /** DaisyUI badge colour for the current status. */
    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_SUCCESSFUL => 'badge-success',
            self::STATUS_PENDING => 'badge-warning',
            self::STATUS_FAILED => 'badge-error',
            default => 'badge-ghost',
        };
    }
}
