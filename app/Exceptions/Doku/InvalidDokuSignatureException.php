<?php

namespace App\Exceptions\Doku;

use RuntimeException;

/**
 * Thrown when an inbound DOKU webhook notification fails signature/HMAC
 * verification, is missing required headers, or its Request-Timestamp is
 * outside the accepted freshness window. Always results in a 401 response,
 * without revealing which specific check failed.
 */
class InvalidDokuSignatureException extends RuntimeException {}
