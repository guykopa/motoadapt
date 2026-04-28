<?php

declare(strict_types=1);

namespace Motoadapt\Port;

use Motoadapt\Model\AdaptiveDecision;
use Motoadapt\Model\DifficultyLevel;
use Motoadapt\Model\PatientResponse;
use Motoadapt\Model\PerformanceReport;

/**
 * Contract for the adaptive engine that processes patient responses.
 */
interface AdaptiveEngineInterface
{
    /**
     * Process a patient response and return adaptation decision.
     *
     * @param PatientResponse $response Latest patient response
     * @param DifficultyLevel $current  Current difficulty level
     * @return AdaptiveDecision         New level and reason
     */
    public function process(PatientResponse $response, DifficultyLevel $current): AdaptiveDecision;

    /**
     * Get performance report from all recorded responses.
     *
     * @return PerformanceReport
     * @throws \RuntimeException if fewer than 3 responses recorded
     */
    public function getPerformanceReport(): PerformanceReport;
}
