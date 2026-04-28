<?php

declare(strict_types=1);

namespace Motoadapt\Model;

/**
 * Immutable result of an adaptation decision.
 */
readonly class AdaptiveDecision
{
    /**
     * @param DifficultyLevel $newLevel      Level after the decision
     * @param DifficultyLevel $previousLevel Level before the decision
     * @param string          $reason        Human-readable explanation
     * @param bool            $levelChanged  Whether the level actually changed
     */
    public function __construct(
        public DifficultyLevel $newLevel,
        public DifficultyLevel $previousLevel,
        public string $reason,
        public bool $levelChanged,
    ) {}
}
