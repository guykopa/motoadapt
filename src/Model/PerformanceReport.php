<?php

declare(strict_types=1);

namespace Motoadapt\Model;

/**
 * Immutable summary of patient performance over a full session.
 */
readonly class PerformanceReport
{
    /**
     * @param float $avgPrecision    Average score as a percentage
     * @param float $avgResponseTime Average response time in seconds
     * @param Trend $trend           Overall performance trend
     * @param string $recommendation Clinical recommendation text
     * @param int   $totalResponses  Total number of responses recorded
     */
    public function __construct(
        public float $avgPrecision,
        public float $avgResponseTime,
        public Trend $trend,
        public string $recommendation,
        public int $totalResponses,
    ) {}
}
