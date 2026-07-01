<?php

namespace App\Actions\Donation;

use Illuminate\Support\Facades\Cookie;

class RememberDonorAction
{
    public const COOKIE = 'donor_biodata';

    private const ONE_YEAR = 60 * 24 * 365; // minutes

    /**
     * Persist (or clear) the donor's biodata in a browser cookie so the
     * Donate form pre-fills on their next visit.
     *
     * @param  array{donor_name?:string,donor_age?:int|string|null,donor_email?:string,donor_phone?:string|null}  $biodata
     */
    public function execute(array $biodata, bool $remember): void
    {
        if ($remember) {
            Cookie::queue(self::COOKIE, json_encode($biodata), self::ONE_YEAR);

            return;
        }

        Cookie::queue(Cookie::forget(self::COOKIE));
    }

    /**
     * Read previously stored biodata, or null when none/invalid.
     *
     * @return array<string, mixed>|null
     */
    public function read(): ?array
    {
        $raw = request()->cookie(self::COOKIE);

        if (! $raw) {
            return null;
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : null;
    }
}
