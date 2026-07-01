<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationSetting extends Model
{
    use HasFactory;

    public const TYPE_AMOUNT = 'amount';

    public const TYPE_TIME_RANGE = 'time_range';

    protected $fillable = [
        'type',
        'label',
        'value',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /** Preset money amounts. */
    public function scopeAmounts(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_AMOUNT);
    }

    /** Preset time ranges (value = number of days). */
    public function scopeTimeRanges(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_TIME_RANGE);
    }

    /** Only options visible on the public Donate page. */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** Canonical display order. */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
