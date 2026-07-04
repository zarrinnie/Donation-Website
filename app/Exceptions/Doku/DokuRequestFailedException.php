<?php

namespace App\Exceptions\Doku;

use RuntimeException;

/**
 * Thrown when DOKU's API returns a non-2xx response or the request could
 * not be completed (network failure, timeout). This is treated as a
 * transient gateway failure, never as a failed payment.
 */
class DokuRequestFailedException extends RuntimeException {}
