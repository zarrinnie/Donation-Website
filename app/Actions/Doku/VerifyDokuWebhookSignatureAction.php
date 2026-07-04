<?php

namespace App\Actions\Doku;

use App\Exceptions\Doku\InvalidDokuSignatureException;
use App\Services\Doku\DokuSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class VerifyDokuWebhookSignatureAction
{
    /** Reject notifications whose Request-Timestamp is older/newer than this. */
    private const MAX_CLOCK_SKEW_MINUTES = 5;

    /**
     * Verify a DOKU webhook notification is authentic. Must run against the
     * raw request body, before any JSON parsing, since the signature covers
     * the exact bytes DOKU sent.
     */
    public function execute(Request $request): void
    {
        $clientId = $request->header('Client-Id');
        $requestId = $request->header('Request-Id');
        $requestTimestamp = $request->header('Request-Timestamp');
        $signatureHeader = $request->header('Signature');

        // DOKU's HTTP notification sends Client-Id, Request-Id,
        // Request-Timestamp and Signature — but NOT a Digest header. The digest
        // is computed by the receiver from the raw body and folded into the
        // signed component string. (Requiring a Digest header here caused every
        // real notification to be rejected with 401 "missing headers".)
        if (! $clientId || ! $requestId || ! $requestTimestamp || ! $signatureHeader) {
            $present = array_keys(array_filter([
                'Client-Id' => $clientId,
                'Request-Id' => $requestId,
                'Request-Timestamp' => $requestTimestamp,
                'Signature' => $signatureHeader,
            ]));

            throw new InvalidDokuSignatureException(
                'Missing one or more required DOKU signature headers (present: '.(implode(', ', $present) ?: 'none').').'
            );
        }

        if (! DokuSignature::matches((string) config('services.doku.client_id'), $clientId)) {
            throw new InvalidDokuSignatureException('Client-Id header does not match the configured DOKU client.');
        }

        // Recompute the digest from the exact bytes DOKU sent, then rebuild the
        // canonical string DOKU signed over.
        $digest = DokuSignature::digest($request->getContent());

        $canonicalString = DokuSignature::canonicalString(
            $clientId,
            $requestId,
            $requestTimestamp,
            '/'.$request->path(),
            $digest,
        );
        $expectedSignature = DokuSignature::sign((string) config('services.doku.webhook_secret'), $canonicalString);

        if (! DokuSignature::matches($expectedSignature, $signatureHeader)) {
            throw new InvalidDokuSignatureException('Signature header is invalid.');
        }

        $skewMinutes = abs(now()->diffInMinutes(Carbon::parse($requestTimestamp), false));
        if ($skewMinutes > self::MAX_CLOCK_SKEW_MINUTES) {
            throw new InvalidDokuSignatureException('Request-Timestamp is outside the accepted freshness window.');
        }
    }
}
