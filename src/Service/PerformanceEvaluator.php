<?php

declare(strict_types=1);

namespace Motoadapt\Service;

use Motoadapt\Model\PatientResponse;
use Motoadapt\Model\PerformanceReport;
use Motoadapt\Model\Trend;

/**
 * Evaluates patient performance over a full session.
 */
class PerformanceEvaluator
{
    private const TREND_THRESHOLD = 10.0;

    /**
     * Evaluate a set of patient responses and produce a performance report.
     *
     * @param PatientResponse[] $responses All responses for the session
     * @return PerformanceReport
     */
    public function evaluate(array $responses): PerformanceReport
    {
        $total        = count($responses);
        $avgPrecision = array_sum(array_map(fn(PatientResponse $r) => $r->score, $responses)) / $total;
        $avgTime      = array_sum(array_map(fn(PatientResponse $r) => $r->responseTime, $responses)) / $total;
        $trend        = $this->computeTrend($responses);
        $recommendation = $this->buildRecommendation($avgPrecision, $trend);

        return new PerformanceReport($avgPrecision, $avgTime, $trend, $recommendation, $total);
    }

    /**
     * @param PatientResponse[] $responses
     */
    private function computeTrend(array $responses): Trend
    {
        if (count($responses) < 2) {
            return Trend::STABLE;
        }

        $first = $responses[0]->score;
        $last  = $responses[count($responses) - 1]->score;
        $delta = $last - $first;

        if ($delta >= self::TREND_THRESHOLD) {
            return Trend::PROGRESSION;
        }

        if ($delta <= -self::TREND_THRESHOLD) {
            return Trend::REGRESSION;
        }

        return Trend::STABLE;
    }

    /**
     * @return string
     */
    private function buildRecommendation(float $avgPrecision, Trend $trend): string
    {
        if ($avgPrecision >= 85.0) {
            return 'Excellent performance — consider increasing difficulty';
        }

        if ($avgPrecision < 50.0) {
            return 'Score below threshold — reduce difficulty and focus on accuracy';
        }

        return match ($trend) {
            Trend::PROGRESSION => 'Good progress — maintain current level',
            Trend::REGRESSION  => 'Performance declining — review exercise parameters',
            Trend::STABLE      => 'Stable performance — maintain current level',
        };
    }
}
