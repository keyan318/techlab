<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when NVIDIA NIM cannot fulfil an Astro chat/extraction request.
 *
 * Carries a stable error CATEGORY (e.g. AUTHENTICATION_ERROR) so the frontend
 * and logs can distinguish failure modes, plus a correlation REQUEST_ID that
 * lets a developer trace one failed request through the system. The message is
 * always a safe, user-facing sentence — never an API key or raw provider text.
 */
class NvidiaNimException extends RuntimeException
{
    public string $category;

    public ?int $status;

    public string $requestId;

    public function __construct(
        string $message,
        string $category = 'UNKNOWN_ERROR',
        ?int $status = null,
        string $requestId = '',
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);

        $this->category = $category;
        $this->status = $status;
        $this->requestId = $requestId;
    }
}
