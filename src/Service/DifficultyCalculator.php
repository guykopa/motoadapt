<?php

declare(strict_types=1);

namespace Motoadapt\Service;

use Motoadapt\Model\AdaptiveDecision;
use Motoadapt\Model\DifficultyLevel;
use Motoadapt\Model\PatientResponse;
use Motoadapt\Port\DifficultyCalculatorInterface;

/**
 * Calculates new difficulty level from a sliding window of patient responses.
 */
class DifficultyCalculator implements DifficultyCalculatorInterface
{
    private const WINDOW_SIZE = 3;
    private const INCREASE_THRESHOLD = 85.0;
    private const DECREASE_THRESHOLD = 50.0;

    /**
     * Calculate new difficulty from sliding window of responses.
     *
     * @param PatientResponse[] $window  Last N responses (window size: 3)
     * @param DifficultyLevel   $current Current difficulty level
     * @return AdaptiveDecision          Decision with reason
     */
    public function calculate(array $window, DifficultyLevel $current): AdaptiveDecision
    {
        if (count($window) < self::WINDOW_SIZE) {
            return new AdaptiveDecision($current, $current, 'Insufficient data', false);
        }

        $avgScore = array_sum(array_map(fn(PatientResponse $r) => $r->score, $window)) / count($window);

        if ($avgScore >= self::INCREASE_THRESHOLD) {
            return $this->increase($current, $avgScore);
        }

        if ($avgScore < self::DECREASE_THRESHOLD) {
            return $this->decrease($current, $avgScore);
        }

        return new AdaptiveDecision($current, $current, sprintf('Stable performance — avg: %.1f%%', $avgScore), false);
    }

    /**
     * @return AdaptiveDecision
     */
    private function increase(DifficultyLevel $current, float $avgScore): AdaptiveDecision
    {
        if ($current === DifficultyLevel::EXPERT) {
            return new AdaptiveDecision(DifficultyLevel::EXPERT, DifficultyLevel::EXPERT, 'Maximum level reached', false);
        }

        $next = match ($current) {
            DifficultyLevel::EASY   => DifficultyLevel::MEDIUM,
            DifficultyLevel::MEDIUM => DifficultyLevel::HARD,
            DifficultyLevel::HARD   => DifficultyLevel::EXPERT,
            DifficultyLevel::EXPERT => DifficultyLevel::EXPERT,
        };

        return new AdaptiveDecision($next, $current, sprintf('3 consecutive high scores — avg: %.1f%%', $avgScore), true);
    }

    /**
     * @return AdaptiveDecision
     */
    private function decrease(DifficultyLevel $current, float $avgScore): AdaptiveDecision
    {
        if ($current === DifficultyLevel::EASY) {
            return new AdaptiveDecision(DifficultyLevel::EASY, DifficultyLevel::EASY, 'Minimum level reached', false);
        }

        $next = match ($current) {
            DifficultyLevel::MEDIUM => DifficultyLevel::EASY,
            DifficultyLevel::HARD   => DifficultyLevel::MEDIUM,
            DifficultyLevel::EXPERT => DifficultyLevel::HARD,
            DifficultyLevel::EASY   => DifficultyLevel::EASY,
        };

        return new AdaptiveDecision($next, $current, sprintf('Score below threshold — avg: %.1f%%', $avgScore), true);
    }
}
