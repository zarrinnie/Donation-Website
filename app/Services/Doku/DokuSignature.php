<?php

namespace App\Services\Doku;

/**
 * Implements DOKU's Checkout/Jokul HMAC-SHA256 request-signing scheme, used
 * both to sign our outbound API calls and to verify DOKU's inbound webhook
 * notifications, so the canonical-string logic lives in exactly one place.
 *
 * NOTE: the exact header names / canonical-string format here follow DOKU's
 * documented Checkout API signing scheme. Verify against DOKU's current
 * official sandbox docs before going live — gateway API details can shift.
 */
class DokuSignature
{
    /** Base64(SHA-256(rawBody)) — DOKU's "Digest" header. */
    public static function digest(string $rawBody): string
    {
        return base64_encode(hash('sha256', $rawBody, true));
    }

    /**
     * Build the canonical newline-joined string DOKU signs/verifies against.
     */
    public static function canonicalString(
        string $clientId,
        string $requestId,
        string $requestTimestamp,
        string $requestTarget,
        string $digest,
    ): string {
        return implode("\n", [
            "Client-Id:{$clientId}",
            "Request-Id:{$requestId}",
            "Request-Timestamp:{$requestTimestamp}",
            "Request-Target:{$requestTarget}",
            "Digest:{$digest}",
        ]);
    }

    /** "HMACSHA256=" + Base64(HMAC-SHA256(secret, canonicalString)) — DOKU's "Signature" header. */
    public static function sign(string $secret, string $canonicalString): string
    {
        return 'HMACSHA256='.base64_encode(hash_hmac('sha256', $canonicalString, $secret, true));
    }

    /** Timing-safe comparison of an expected vs. received signature. */
    public static function matches(string $expected, string $received): bool
    {
        return hash_equals($expected, $received);
    }
}
