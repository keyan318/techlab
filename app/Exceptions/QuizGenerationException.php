<?php

namespace App\Exceptions;

use Exception;

/**
 * Raised when quiz generation fails after the source was valid
 * (model error, empty response, malformed JSON, schema failure, etc.).
 *
 * Mirrors InfographicGenerationException: carries a safe message, a category,
 * and a request id for server-side logs.
 */
class QuizGenerationException extends Exception
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
