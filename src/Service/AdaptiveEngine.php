<?php

declare(strict_types=1);

namespace Motoadapt\Service;

use Motoadapt\Model\AdaptiveDecision;
use Motoadapt\Model\DifficultyLevel;
use Motoadapt\Model\PatientResponse;
use Motoadapt\Model\PerformanceReport;
use Motoadapt\Port\AdaptiveEngineInterface;
use Motoadapt\Port\DifficultyCalculatorInterface;

/**
 * Orchestrates the adaptation decision using a sliding window of responses.
 */
class AdaptiveEngine implements AdaptiveEngineInterface
{
    private const WINDOW_SIZE = 3;

    /** @var PatientResponse[] */
    private array $window = [];

    /** @var PatientResponse[] */
    private array $allResponses = [];

    /**
     * @param DifficultyCalculatorInterface $calculator Injected difficulty calculator
     * @param PerformanceEvaluator          $evaluator  Injected performance evaluator
     */
    public function __construct(
        private readonly DifficultyCalculatorInterface $calculator,
        private readonly PerformanceEvaluator $evaluator,
    ) {}

    /**
     * Process a patient response and return adaptation decision.
     *
     * @param PatientResponse $response Latest patient response
     * @param DifficultyLevel $current  Current difficulty level
     * @return AdaptiveDecision         New level and reason
     */
    public function process(PatientResponse $response, DifficultyLevel $current): AdaptiveDecision
    {
        $this->allResponses[] = $response;
        $this->window[]       = $response;

        if (count($this->window) > self::WINDOW_SIZE) {
            $this->window = array_slice($this->window, -self::WINDOW_SIZE);
        }

        $decision = $this->calculator->calculate($this->window, $current);

        // Reset window after a level change so the new level is assessed independently.
        if ($decision->levelChanged) {
            $this->window = [];
        }

        return $decision;
    }

    /**
     * Get performance report from all recorded responses.
     *
     * @return PerformanceReport
     * @throws \RuntimeException if no responses have been recorded
     */
    public function getPerformanceReport(): PerformanceReport
    {
        if (empty($this->allResponses)) {
            throw new \RuntimeException('No responses recorded — cannot generate performance report.');
        }

        return $this->evaluator->evaluate($this->allResponses);
    }
}
