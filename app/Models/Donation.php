<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'donor_age' => 'integer',
        'time_range_days' => 'integer',
        'is_custom_amount' => 'boolean',
        'is_custom_range' => 'boolean',
        'reminder_sent_at' => 'datetime',
    ];

    /** Latest donations first (overview default). */
    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->latest();
    }

    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SUCCESSFUL);
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
