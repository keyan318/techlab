<?php

namespace App\Exceptions;

use Exception;

/**
 * Raised when the infographic plan generation fails after the source was valid
 * (model error, empty response, malformed JSON, image failure, etc.).
 *
 * Mirrors NvidiaNimException: carries a safe message, a category, and a
 * request id for server-side logs. The category lets the controller map to a
 * friendly HTTP status WITHOUT ever exposing the API key or provider text.
 */
class InfographicGenerationException extends Exception
{
    public function __construct(
        string $message,
        public readonly string $category = 'UNKNOWN_ERROR',
        int $status = 502,
        public readonly ?string $requestId = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $status, $previous);
    }

    public function status(): int
    {
        return $this->getCode() ?: 502;
    }
}
