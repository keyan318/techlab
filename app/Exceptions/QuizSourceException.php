<?php

namespace App\Exceptions;

use Exception;

/**
 * Raised when the quiz has no usable conversation material.
 * Carries a SAFE, user-facing message and an HTTP status.
 * Never includes the API key or raw provider text.
 */
class QuizSourceException extends Exception
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
