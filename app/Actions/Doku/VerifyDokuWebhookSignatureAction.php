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
        $digestHeader = $request->header('Digest');
        $signatureHeader = $request->header('Signature');

        if (! $clientId || ! $requestId || ! $requestTimestamp || ! $digestHeader || ! $signatureHeader) {
            throw new InvalidDokuSignatureException('Missing one or more required DOKU signature headers.');
        }

        if (! DokuSignature::matches((string) config('services.doku.client_id'), $clientId)) {
            throw new InvalidDokuSignatureException('Client-Id header does not match the configured DOKU client.');
        }

        $rawBody = $request->getContent();
        $expectedDigest = DokuSignature::digest($rawBody);

        if (! DokuSignature::matches($expectedDigest, $digestHeader)) {
            throw new InvalidDokuSignatureException('Digest header does not match the request body.');
        }

        $canonicalString = DokuSignature::canonicalString(
            $clientId,
            $requestId,
            $requestTimestamp,
            '/'.$request->path(),
            $digestHeader,
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
