<?php

namespace App\Exceptions;

use Exception;

/**
 * Raised when the infographic has no usable learning material, or a file
 * can't be read. Carries a SAFE, user-facing message and an HTTP status.
 * Never includes the API key or raw provider text.
 */
class InfographicSourceException extends Exception
{
    public function __construct(string $message, int $status = 422, ?\Throwable $previous = null)
    {
        parent::__construct($message, $status, $previous);
    }

    public function status(): int
    {
        return $this->getCode() ?: 422;
    }
}
