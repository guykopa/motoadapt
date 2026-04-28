<?php

declare(strict_types=1);

namespace Motoadapt\Model;

/**
 * Immutable record of a single patient exercise response.
 */
readonly class PatientResponse
{
    /**
     * @param float              $score        Score from 0.0 to 100.0
     * @param float              $responseTime Duration in seconds
     * @param \DateTimeImmutable $recordedAt   Timestamp of the response
     */
    public function __construct(
        public float $score,
        public float $responseTime,
        public \DateTimeImmutable $recordedAt,
    ) {}
}
