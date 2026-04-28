<?php

declare(strict_types=1);

namespace Motoadapt\Port;

use Motoadapt\Model\AdaptiveDecision;
use Motoadapt\Model\DifficultyLevel;
use Motoadapt\Model\PatientResponse;

/**
 * Contract for calculating a new difficulty from a sliding window of responses.
 */
interface DifficultyCalculatorInterface
{
    /**
     * Calculate new difficulty from sliding window of responses.
     *
     * @param PatientResponse[] $window  Last N responses (window size: 3)
     * @param DifficultyLevel   $current Current difficulty level
     * @return AdaptiveDecision          Decision with reason
     */
    public function calculate(array $window, DifficultyLevel $current): AdaptiveDecision;
}
