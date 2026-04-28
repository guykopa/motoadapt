<?php

declare(strict_types=1);

namespace Motoadapt\Model;

/**
 * Difficulty levels for rehabilitation exercises.
 */
enum DifficultyLevel
{
    case EASY;
    case MEDIUM;
    case HARD;
    case EXPERT;
}
